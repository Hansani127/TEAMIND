#!/usr/bin/env python3
"""
TEAMIND - Tea Yield Prediction Training Script
Trains a regression model to predict tea yield based on environmental factors.

Features:
- temperature_c: Temperature in Celsius
- rainfall_mm: Annual rainfall in mm
- humidity_percent: Relative humidity percentage
- soil_ph: Soil pH level
- fertilizer_kg_ha: Fertilizer application (kg/hectare)
- sunshine_hours: Average daily sunshine hours
- region: Tea growing region (one-hot encoded)

Target: yield_kg_ha (kg per hectare)

Usage:
    python training_scripts/train_yield.py --data_file datasets/yield_data.csv --model_type catboost
"""

import os
import sys
import argparse
import json
import pickle
import numpy as np
import pandas as pd
from pathlib import Path
from datetime import datetime
from sklearn.model_selection import train_test_split, cross_val_score, GridSearchCV
from sklearn.preprocessing import StandardScaler, LabelEncoder
from sklearn.ensemble import RandomForestRegressor, GradientBoostingRegressor
from sklearn.linear_model import Ridge, Lasso, ElasticNet
from sklearn.svm import SVR
from sklearn.neural_network import MLPRegressor
from sklearn.metrics import mean_squared_error, mean_absolute_error, r2_score

# Optional: CatBoost (best for tabular data)
try:
    from catboost import CatBoostRegressor
    CATBOOST_AVAILABLE = True
except ImportError:
    CATBOOST_AVAILABLE = False
    print("WARNING: CatBoost not installed. Install with: pip install catboost")

# Optional: XGBoost
try:
    from xgboost import XGBRegressor
    XGBOOST_AVAILABLE = True
except ImportError:
    XGBOOST_AVAILABLE = False
    print("WARNING: XGBoost not installed. Install with: pip install xgboost")

# Optional: LightGBM
try:
    from lightgbm import LGBMRegressor
    LIGHTGBM_AVAILABLE = True
except ImportError:
    LIGHTGBM_AVAILABLE = False
    print("WARNING: LightGBM not installed. Install with: pip install lightgbm")

# Configuration
REGIONS = ['Nuwara_Eliya', 'Uda_Pussellawa', 'Uva', 'Dimbula', 
           'Kandy', 'Ruhuna', 'Sabaragamuwa', 'Low_Country']

def generate_synthetic_data(n_samples=2000, output_file="datasets/yield_data.csv"):
    """Generate realistic synthetic tea yield data based on Sri Lankan conditions."""
    print("=== Generating Synthetic Yield Data ===")

    np.random.seed(42)

    # Generate features
    data = {
        'temperature_c': np.random.normal(22, 4, n_samples),  # 18-28°C optimal
        'rainfall_mm': np.random.normal(2000, 500, n_samples),  # 1500-2500mm
        'humidity_percent': np.random.normal(75, 10, n_samples),  # 65-85%
        'soil_ph': np.random.normal(5.2, 0.5, n_samples),  # 4.5-6.0
        'fertilizer_kg_ha': np.random.normal(250, 80, n_samples),  # 150-350
        'sunshine_hours': np.random.normal(6, 1.5, n_samples),  # 4-8 hours
        'region': np.random.choice(REGIONS, n_samples)
    }

    df = pd.DataFrame(data)

    # Clip to realistic ranges
    df['temperature_c'] = df['temperature_c'].clip(10, 35)
    df['rainfall_mm'] = df['rainfall_mm'].clip(1000, 3500)
    df['humidity_percent'] = df['humidity_percent'].clip(50, 95)
    df['soil_ph'] = df['soil_ph'].clip(4.0, 7.0)
    df['fertilizer_kg_ha'] = df['fertilizer_kg_ha'].clip(100, 500)
    df['sunshine_hours'] = df['sunshine_hours'].clip(3, 10)

    # Calculate yield based on realistic formula (kg/hectare)
    # Base yields by region
    base_yields = {
        'Nuwara_Eliya': 1200, 'Uda_Pussellawa': 1250, 'Uva': 1400,
        'Dimbula': 1450, 'Kandy': 1500, 'Ruhuna': 1600,
        'Sabaragamuwa': 1550, 'Low_Country': 1700
    }

    yields = []
    for _, row in df.iterrows():
        base = base_yields[row['region']]

        # Temperature factor (optimal 22°C)
        temp_factor = 1 - abs(row['temperature_c'] - 22) / 20

        # Rainfall factor (optimal 2000mm)
        rain_factor = min(row['rainfall_mm'] / 2000, 1.2)

        # Humidity factor
        humid_factor = row['humidity_percent'] / 100

        # pH factor (optimal 5.2)
        ph_factor = 1 - abs(row['soil_ph'] - 5.2) / 3

        # Fertilizer factor
        fert_factor = row['fertilizer_kg_ha'] / 300

        # Sunshine factor
        sun_factor = row['sunshine_hours'] / 7

        # Calculate yield with some noise
        yield_val = base * temp_factor * rain_factor * humid_factor * ph_factor * fert_factor * sun_factor
        yield_val = max(400, min(3000, yield_val * np.random.normal(1, 0.05)))
        yields.append(yield_val)

    df['yield_kg_ha'] = yields

    # Save
    output_path = Path(output_file)
    output_path.parent.mkdir(parents=True, exist_ok=True)
    df.to_csv(output_path, index=False)
    print(f"✓ Generated {n_samples} samples: {output_path}")
    print(f"  Yield range: {df['yield_kg_ha'].min():.0f} - {df['yield_kg_ha'].max():.0f} kg/ha")
    print(f"  Mean yield: {df['yield_kg_ha'].mean():.0f} kg/ha")

    return df

def preprocess_data(df):
    """Preprocess data: encode categoricals, split features/target."""

    # One-hot encode region
    df_encoded = pd.get_dummies(df, columns=['region'], prefix='region')

    # Separate features and target
    feature_cols = [c for c in df_encoded.columns if c != 'yield_kg_ha']
    X = df_encoded[feature_cols]
    y = df_encoded['yield_kg_ha']

    return X, y, feature_cols

def get_models():
    """Get dictionary of models to train."""
    models = {
        'RandomForest': RandomForestRegressor(n_estimators=200, max_depth=15, random_state=42, n_jobs=-1),
        'GradientBoosting': GradientBoostingRegressor(n_estimators=200, max_depth=5, random_state=42),
        'Ridge': Ridge(alpha=1.0),
        'Lasso': Lasso(alpha=1.0),
        'ElasticNet': ElasticNet(alpha=1.0, l1_ratio=0.5),
        'SVR': SVR(kernel='rbf', C=100, gamma='scale'),
        'MLP': MLPRegressor(hidden_layer_sizes=(128, 64, 32), max_iter=1000, random_state=42)
    }

    if CATBOOST_AVAILABLE:
        models['CatBoost'] = CatBoostRegressor(
            iterations=1000, depth=8, learning_rate=0.05,
            loss_function='RMSE', verbose=False, random_state=42
        )

    if XGBOOST_AVAILABLE:
        models['XGBoost'] = XGBRegressor(
            n_estimators=200, max_depth=6, learning_rate=0.1,
            random_state=42, n_jobs=-1
        )

    if LIGHTGBM_AVAILABLE:
        models['LightGBM'] = LGBMRegressor(
            n_estimators=200, max_depth=6, learning_rate=0.1,
            random_state=42, n_jobs=-1, verbose=-1
        )

    return models

def train_and_evaluate(X_train, X_test, y_train, y_test, feature_names, model_type='all'):
    """Train models and evaluate performance."""

    # Scale features
    scaler = StandardScaler()
    X_train_scaled = scaler.fit_transform(X_train)
    X_test_scaled = scaler.transform(X_test)

    models = get_models()

    if model_type != 'all' and model_type in models:
        models = {model_type: models[model_type]}

    results = {}
    best_model = None
    best_score = -float('inf')

    print("\n=== Training Models ===")
    print(f"{'Model':<20} {'R²':>8} {'RMSE':>10} {'MAE':>10}")
    print("-" * 52)

    for name, model in models.items():
        print(f"Training {name}...", end=" ")

        # Use scaled data for models that need it
        if name in ['SVR', 'MLP', 'Ridge', 'Lasso', 'ElasticNet']:
            model.fit(X_train_scaled, y_train)
            y_pred = model.predict(X_test_scaled)
        else:
            model.fit(X_train, y_train)
            y_pred = model.predict(X_test)

        # Metrics
        r2 = r2_score(y_test, y_pred)
        rmse = np.sqrt(mean_squared_error(y_test, y_pred))
        mae = mean_absolute_error(y_test, y_pred)

        results[name] = {
            'r2': r2,
            'rmse': rmse,
            'mae': mae,
            'predictions': y_pred
        }

        print(f"R²={r2:.4f} RMSE={rmse:.1f} MAE={mae:.1f}")

        if r2 > best_score:
            best_score = r2
            best_model = model
            best_name = name

    print(f"\n✓ Best model: {best_name} (R² = {best_score:.4f})")

    return best_model, scaler, results, best_name

def save_model(model, scaler, feature_names, output_dir="models"):
    """Save model, scaler, and feature names."""
    output_path = Path(output_dir)
    output_path.mkdir(parents=True, exist_ok=True)

    # Save model
    model_path = output_path / "yield_model.pkl"
    with open(model_path, "wb") as f:
        pickle.dump(model, f)
    print(f"✓ Model saved: {model_path}")

    # Save scaler
    scaler_path = output_path / "yield_scaler.pkl"
    with open(scaler_path, "wb") as f:
        pickle.dump(scaler, f)
    print(f"✓ Scaler saved: {scaler_path}")

    # Save feature names
    feature_path = output_path / "yield_feature_names.json"
    with open(feature_path, "w") as f:
        json.dump(feature_names, f, indent=2)
    print(f"✓ Feature names saved: {feature_path}")

    return model_path

def feature_importance(model, feature_names, output_dir="models"):
    """Extract and save feature importance."""
    importance = {}

    if hasattr(model, 'feature_importances_'):
        # Tree-based models
        for name, imp in zip(feature_names, model.feature_importances_):
            importance[name] = float(imp)
    elif hasattr(model, 'coef_'):
        # Linear models
        for name, coef in zip(feature_names, model.coef_):
            importance[name] = float(abs(coef))

    if importance:
        # Sort by importance
        importance = dict(sorted(importance.items(), key=lambda x: x[1], reverse=True))

        output_path = Path(output_dir) / "yield_feature_importance.json"
        with open(output_path, "w") as f:
            json.dump(importance, f, indent=2)
        print(f"✓ Feature importance saved: {output_path}")

        print("\nTop 5 Important Features:")
        for name, imp in list(importance.items())[:5]:
            print(f"  {name}: {imp:.4f}")

def main():
    parser = argparse.ArgumentParser(description="Train Tea Yield Prediction Model")
    parser.add_argument("--data_file", default="datasets/yield_data.csv", help="CSV data file")
    parser.add_argument("--model_type", default="all", help="Model type: all, RandomForest, CatBoost, XGBoost, etc.")
    parser.add_argument("--generate_data", action="store_true", help="Generate synthetic data")
    parser.add_argument("--test_size", type=float, default=0.2, help="Test set ratio")
    parser.add_argument("--n_samples", type=int, default=2000, help="Number of synthetic samples")
    args = parser.parse_args()

    print("=" * 70)
    print("  TEAMIND - Tea Yield Prediction Training")
    print("=" * 70)
    print(f"  CatBoost: {CATBOOST_AVAILABLE}")
    print(f"  XGBoost: {XGBOOST_AVAILABLE}")
    print(f"  LightGBM: {LIGHTGBM_AVAILABLE}")
    print("=" * 70)

    # Generate or load data
    data_path = Path(args.data_file)
    if args.generate_data or not data_path.exists():
        df = generate_synthetic_data(args.n_samples, args.data_file)
    else:
        print(f"\nLoading data from: {data_path}")
        df = pd.read_csv(data_path)
        print(f"  Loaded {len(df)} samples")

    # Preprocess
    print("\n=== Preprocessing ===")
    X, y, feature_names = preprocess_data(df)
    print(f"  Features: {len(feature_names)}")
    print(f"  Samples: {len(X)}")

    # Split
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=args.test_size, random_state=42
    )
    print(f"  Train: {len(X_train)} | Test: {len(X_test)}")

    # Train
    best_model, scaler, results, best_name = train_and_evaluate(
        X_train, X_test, y_train, y_test, feature_names, args.model_type
    )

    # Save
    print("\n=== Saving Model ===")
    save_model(best_model, scaler, feature_names)

    # Feature importance
    feature_importance(best_model, feature_names)

    # Save results
    results_summary = {
        'best_model': best_name,
        'training_date': datetime.now().isoformat(),
        'metrics': {k: {m: float(v[m]) for m in ['r2', 'rmse', 'mae']} 
                   for k, v in results.items()}
    }

    results_path = Path("models") / "yield_results.json"
    with open(results_path, "w") as f:
        json.dump(results_summary, f, indent=2)
    print(f"✓ Results saved: {results_path}")

    print("\n" + "=" * 70)
    print("  Training Complete!")
    print("  Models saved to 'models/' folder")
    print("  ")
    print("=" * 70)

if __name__ == "__main__":
    main()
