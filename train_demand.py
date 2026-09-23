#!/usr/bin/env python3
"""
TEAMIND - Tea Market Demand Forecasting Training Script
Trains a time-series model to forecast tea market demand.

Features:
- month, year (temporal)
- region (categorical)
- tea_type (categorical)
- historical_demand (lag features)
- exports (global export volume)
- seasonal factors

Target: forecasted_demand (metric tons)

Usage:
    python training_scripts/train_demand.py --data_file datasets/demand_data.csv --model_type prophet
"""

import os
import sys
import argparse
import json
import pickle
import numpy as np
import pandas as pd
from pathlib import Path
from datetime import datetime, timedelta
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler, LabelEncoder
from sklearn.ensemble import RandomForestRegressor, GradientBoostingRegressor
from sklearn.linear_model import LinearRegression
from sklearn.metrics import mean_squared_error, mean_absolute_error, r2_score

# Optional time-series libraries
try:
    from statsmodels.tsa.seasonal import seasonal_decompose
    from statsmodels.tsa.holtwinters import ExponentialSmoothing
    STATSMODELS_AVAILABLE = True
except ImportError:
    STATSMODELS_AVAILABLE = False
    print("WARNING: statsmodels not installed. Install with: pip install statsmodels")

try:
    from prophet import Prophet
    PROPHET_AVAILABLE = True
except ImportError:
    PROPHET_AVAILABLE = False
    print("WARNING: Prophet not installed. Install with: pip install prophet")

# Configuration
REGIONS = ['Central Province', 'Uva Province', 'Sabaragamuwa', 
           'Southern Province', 'Nuwara Eliya', 'Dimbula', 'Kandy']
TEA_TYPES = ['CTC', 'Orthodox', 'Green Tea', 'White Tea', 'Oolong']
SEASONAL_FACTORS = {
    1: 1.15, 2: 1.10, 3: 1.08, 4: 0.92, 5: 0.88, 6: 0.85,
    7: 1.05, 8: 1.08, 9: 1.12, 10: 0.95, 11: 0.90, 12: 0.92
}

def generate_synthetic_data(start_year=2020, end_year=2025, output_file="datasets/demand_data.csv"):
    """Generate realistic synthetic tea demand data for Sri Lanka."""
    print("=== Generating Synthetic Demand Data ===")

    np.random.seed(42)

    data = []
    base_demand = 22000  # Base monthly demand in MT

    for year in range(start_year, end_year + 1):
        for month in range(1, 13):
            for region in REGIONS:
                for tea_type in TEA_TYPES:
                    # Seasonal factor
                    seasonal = SEASONAL_FACTORS[month]

                    # Regional factor
                    region_factors = {
                        'Central Province': 1.0, 'Uva Province': 0.95,
                        'Sabaragamuwa': 0.90, 'Southern Province': 0.85,
                        'Nuwara Eliya': 1.05, 'Dimbula': 1.02, 'Kandy': 0.98
                    }
                    region_factor = region_factors.get(region, 1.0)

                    # Tea type factor
                    type_factors = {
                        'CTC': 1.0, 'Orthodox': 0.85, 'Green Tea': 0.65,
                        'White Tea': 0.30, 'Oolong': 0.20
                    }
                    type_factor = type_factors.get(tea_type, 1.0)

                    # Year trend (growth)
                    year_factor = 1 + (year - start_year) * 0.03

                    # Random noise
                    noise = np.random.normal(1, 0.05)

                    # Calculate demand
                    demand = base_demand * seasonal * region_factor * type_factor * year_factor * noise
                    demand = max(1000, demand)

                    # Exports (correlated with demand)
                    exports = demand * np.random.uniform(0.6, 0.8)

                    # Growth rate
                    growth_rate = np.random.normal(8, 3)

                    data.append({
                        'year': year,
                        'month': month,
                        'region': region,
                        'tea_type': tea_type,
                        'historical_demand': round(demand, 2),
                        'exports': round(exports, 2),
                        'growth_rate': round(growth_rate, 2),
                        'forecasted_demand': round(demand * (1 + growth_rate/100), 2)
                    })

    df = pd.DataFrame(data)

    # Save
    output_path = Path(output_file)
    output_path.parent.mkdir(parents=True, exist_ok=True)
    df.to_csv(output_path, index=False)
    print(f"✓ Generated {len(df)} samples: {output_path}")
    print(f"  Date range: {start_year}-{end_year}")
    print(f"  Regions: {len(REGIONS)} | Tea types: {len(TEA_TYPES)}")

    return df

def preprocess_data(df):
    """Preprocess demand data with temporal features."""

    df = df.copy()

    # Create date column
    df['date'] = pd.to_datetime(df[['year', 'month']].assign(day=1))

    # Temporal features
    df['quarter'] = df['date'].dt.quarter
    df['month_sin'] = np.sin(2 * np.pi * df['month'] / 12)
    df['month_cos'] = np.cos(2 * np.pi * df['month'] / 12)

    # Lag features
    df = df.sort_values(['region', 'tea_type', 'date'])
    df['demand_lag_1'] = df.groupby(['region', 'tea_type'])['historical_demand'].shift(1)
    df['demand_lag_3'] = df.groupby(['region', 'tea_type'])['historical_demand'].shift(3)
    df['demand_lag_6'] = df.groupby(['region', 'tea_type'])['historical_demand'].shift(6)

    # Rolling averages
    df['demand_ma_3'] = df.groupby(['region', 'tea_type'])['historical_demand'].transform(
        lambda x: x.rolling(3, min_periods=1).mean()
    )
    df['demand_ma_6'] = df.groupby(['region', 'tea_type'])['historical_demand'].transform(
        lambda x: x.rolling(6, min_periods=1).mean()
    )

    # Drop rows with NaN lag features
    df = df.dropna()

    # One-hot encode categoricals
    df_encoded = pd.get_dummies(df, columns=['region', 'tea_type'], prefix=['region', 'type'])

    # Feature columns
    feature_cols = [c for c in df_encoded.columns if c not in 
                    ['forecasted_demand', 'date', 'year', 'month']]

    X = df_encoded[feature_cols]
    y = df_encoded['forecasted_demand']

    return X, y, feature_cols, df_encoded

def train_prophet(df):
    """Train Prophet model for time-series forecasting."""
    if not PROPHET_AVAILABLE:
        print("Prophet not available, skipping...")
        return None, None

    print("\n=== Training Prophet Model ===")

    # Aggregate by date for overall trend
    df_agg = df.groupby('date').agg({
        'historical_demand': 'sum',
        'exports': 'sum'
    }).reset_index()
    df_agg.columns = ['ds', 'y', 'exports']

    model = Prophet(
        yearly_seasonality=True,
        weekly_seasonality=False,
        daily_seasonality=False,
        seasonality_mode='multiplicative'
    )
    model.add_regressor('exports')

    # Split
    train_size = int(len(df_agg) * 0.8)
    train_df = df_agg.iloc[:train_size]
    test_df = df_agg.iloc[train_size:]

    model.fit(train_df)

    # Predict
    future = model.make_future_dataframe(periods=len(test_df), freq='MS')
    future = future.merge(df_agg[['ds', 'exports']], on='ds', how='left')
    future['exports'] = future['exports'].fillna(future['exports'].mean())

    forecast = model.predict(future)

    # Evaluate
    pred = forecast[forecast['ds'].isin(test_df['ds'])]['yhat'].values
    actual = test_df['y'].values

    r2 = r2_score(actual, pred)
    rmse = np.sqrt(mean_squared_error(actual, pred))

    print(f"  Prophet R²: {r2:.4f}, RMSE: {rmse:.1f}")

    return model, {'r2': r2, 'rmse': rmse}

def train_ml_models(X_train, X_test, y_train, y_test):
    """Train ML regression models."""

    models = {
        'RandomForest': RandomForestRegressor(n_estimators=200, max_depth=15, random_state=42, n_jobs=-1),
        'GradientBoosting': GradientBoostingRegressor(n_estimators=200, max_depth=5, random_state=42),
        'LinearRegression': LinearRegression()
    }

    results = {}
    best_model = None
    best_score = -float('inf')

    print("\n=== Training ML Models ===")
    print(f"{'Model':<20} {'R²':>8} {'RMSE':>10} {'MAE':>10}")
    print("-" * 52)

    for name, model in models.items():
        model.fit(X_train, y_train)
        y_pred = model.predict(X_test)

        r2 = r2_score(y_test, y_pred)
        rmse = np.sqrt(mean_squared_error(y_test, y_pred))
        mae = mean_absolute_error(y_test, y_pred)

        results[name] = {'r2': r2, 'rmse': rmse, 'mae': mae}
        print(f"{name:<20} {r2:>8.4f} {rmse:>10.1f} {mae:>10.1f}")

        if r2 > best_score:
            best_score = r2
            best_model = model
            best_name = name

    print(f"\n✓ Best ML model: {best_name} (R² = {best_score:.4f})")

    return best_model, results, best_name

def save_model(model, feature_cols, output_dir="models"):
    """Save model and metadata."""
    output_path = Path(output_dir)
    output_path.mkdir(parents=True, exist_ok=True)

    # Save model
    model_path = output_path / "demand_model.pkl"
    with open(model_path, "wb") as f:
        pickle.dump(model, f)
    print(f"✓ Model saved: {model_path}")

    # Save feature names
    feature_path = output_path / "demand_features.json"
    with open(feature_path, "w") as f:
        json.dump(feature_cols, f, indent=2)
    print(f"✓ Features saved: {feature_path}")

    # Save seasonal factors
    seasonal_path = output_path / "demand_seasonal.json"
    with open(seasonal_path, "w") as f:
        json.dump(SEASONAL_FACTORS, f, indent=2)
    print(f"✓ Seasonal factors saved: {seasonal_path}")

    return model_path

def main():
    parser = argparse.ArgumentParser(description="Train Tea Demand Forecasting Model")
    parser.add_argument("--data_file", default="datasets/demand_data.csv", help="CSV data file")
    parser.add_argument("--model_type", default="all", help="Model type: all, RandomForest, Prophet, etc.")
    parser.add_argument("--generate_data", action="store_true", help="Generate synthetic data")
    parser.add_argument("--test_size", type=float, default=0.2, help="Test set ratio")
    args = parser.parse_args()

    print("=" * 70)
    print("  TEAMIND - Tea Demand Forecasting Training")
    print("=" * 70)
    print(f"  statsmodels: {STATSMODELS_AVAILABLE}")
    print(f"  Prophet: {PROPHET_AVAILABLE}")
    print("=" * 70)

    # Generate or load data
    data_path = Path(args.data_file)
    if args.generate_data or not data_path.exists():
        df = generate_synthetic_data(output_file=args.data_file)
    else:
        print(f"\nLoading data from: {data_path}")
        df = pd.read_csv(data_path)
        df['date'] = pd.to_datetime(df[['year', 'month']].assign(day=1))
        print(f"  Loaded {len(df)} samples")

    # Preprocess
    print("\n=== Preprocessing ===")
    X, y, feature_cols, df_processed = preprocess_data(df)
    print(f"  Features: {len(feature_cols)}")
    print(f"  Samples: {len(X)}")

    # Split
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=args.test_size, shuffle=False  # Time-series: no shuffle
    )
    print(f"  Train: {len(X_train)} | Test: {len(X_test)}")

    # Train ML models
    best_model, ml_results, best_name = train_ml_models(X_train, X_test, y_train, y_test)

    # Train Prophet if available
    prophet_model, prophet_results = None, None
    if PROPHET_AVAILABLE and args.model_type in ['all', 'Prophet']:
        prophet_model, prophet_results = train_prophet(df_processed)

    # Save best model
    print("\n=== Saving Model ===")
    save_model(best_model, feature_cols)

    # Save results
    results_summary = {
        'best_model': best_name,
        'training_date': datetime.now().isoformat(),
        'ml_results': {k: {m: float(v[m]) for m in ['r2', 'rmse', 'mae']} 
                      for k, v in ml_results.items()},
        'prophet_results': prophet_results
    }

    results_path = Path("models") / "demand_results.json"
    with open(results_path, "w") as f:
        json.dump(results_summary, f, indent=2)
    print(f"✓ Results saved: {results_path}")

    print("\n" + "=" * 70)
    print("  Training Complete!")
    print("  Models saved to 'models/' folder")
    print("=" * 70)

if __name__ == "__main__":
    main()
