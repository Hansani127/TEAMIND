#!/usr/bin/env python3
"""
TEAMIND - AI Flask API Server (FINAL FIXED VERSION)
Properly integrates all 4 AI modules with correct class mappings.
"""

import os, sys, json, pickle, traceback, random
from pathlib import Path
from datetime import datetime

# Optional imports with fallbacks
try:
    from flask import Flask, request, jsonify
    from flask_cors import CORS
    from werkzeug.utils import secure_filename
    FLASK_OK = True
except ImportError:
    FLASK_OK = False

try:
    import numpy as np
    NP_OK = True
except ImportError:
    NP_OK = False

try:
    from PIL import Image
    PIL_OK = True
except ImportError:
    PIL_OK = False

# TensorFlow / Keras import
try:
    import tensorflow as tf
    if hasattr(tf, 'keras'):
        keras = tf.keras
        keras_models = tf.keras.models
        KERAS_OK = True
    else:
        KERAS_OK = False
    TF_OK = True
except ImportError:
    TF_OK = False
    KERAS_OK = False
    keras = None
    keras_models = None

try:
    import pandas as pd
    PD_OK = True
except ImportError:
    PD_OK = False

# Paths
BASE = Path(__file__).parent
MODELS_DIR = BASE / "models"
UPLOADS = BASE / "uploads"
UPLOADS.mkdir(exist_ok=True)
MODELS_DIR.mkdir(exist_ok=True)

# ============================================================
# DISEASE INFORMATION DATABASE (7 classes)
# ============================================================
DISEASE_INFO = {
    0: {
        "name": "Algal Leaf Spot",
        "severity": "Moderate",
        "description": "Algal leaf spot is caused by the parasitic algae Cephaleuros virescens. It appears as orange, velvety spots on the upper leaf surface, typically 2-10mm in diameter. The disease thrives in warm, humid conditions with poor air circulation.",
        "recommendations": [
            "Apply copper-based fungicide (Bordeaux mixture 1%) during wet seasons",
            "Prune dense branches to improve air circulation and sunlight penetration",
            "Remove and destroy severely infected leaves to reduce spore spread",
            "Maintain proper spacing between plants (1.2m x 0.6m)",
            "Apply balanced NPK fertilizer to boost plant vigor"
        ]
    },
    1: {
        "name": "Brown Blight",
        "severity": "High",
        "description": "Brown blight is caused by the fungus Colletotrichum camelliae. It produces circular to irregular brown lesions with concentric rings on leaves, starting from the margins. The fungus spreads rapidly during rainy seasons and can cause up to 30% yield loss.",
        "recommendations": [
            "Apply Bordeaux mixture (1%) or Carbendazim (0.1%) at first sign of infection",
            "Remove and burn all infected plant debris during pruning",
            "Avoid overhead irrigation to reduce leaf wetness duration",
            "Apply calcium to strengthen cell walls and improve resistance",
            "Monitor fields weekly during monsoon seasons"
        ]
    },
    2: {
        "name": "Gray Blight",
        "severity": "Moderate",
        "description": "Gray blight is caused by Pestalotiopsis theae fungus. It presents as grayish-brown lesions with dark margins, often surrounded by a yellow halo. The center of lesions may develop tiny black fruiting bodies. It affects mature leaves primarily.",
        "recommendations": [
            "Spray Mancozeb (0.2%) or Copper oxychloride (0.3%) every 14 days",
            "Collect and destroy fallen leaves to eliminate overwintering spores",
            "Maintain field sanitation by removing weed hosts",
            "Ensure proper drainage to prevent waterlogging",
            "Apply organic mulch to regulate soil temperature and moisture"
        ]
    },
    3: {
        "name": "Helopeltis (Tea Mosquito Bug)",
        "severity": "High",
        "description": "Helopeltis theivora, commonly known as the tea mosquito bug, is a sucking pest that feeds on young tea shoots and leaves. It causes characteristic necrotic spots and curling of tender leaves. Heavy infestation can result in 'hopperburn'.",
        "recommendations": [
            "Apply Cypermethrin (0.005%) or Dimethoate (0.03%) targeting nymph stages",
            "Install pheromone traps (5 per hectare) for monitoring and mass trapping",
            "Conserve natural enemies like spiders, praying mantids, and parasitoids",
            "Remove alternate host plants (jungle weeds) from field boundaries",
            "Apply neem-based botanical pesticides as a preventive measure"
        ]
    },
    4: {
        "name": "Red Spider",
        "severity": "Moderate",
        "description": "Red spider mite (Oligonychus coffeae) is a tiny arachnid pest that feeds on leaf undersides, causing yellow stippling and bronzing. Severe infestations lead to leaf drop and reduced photosynthesis. They thrive in hot, dry conditions.",
        "recommendations": [
            "Apply dicofol (0.05%) or wettable sulfur (0.2%) on leaf undersides",
            "Release predatory mites (Phytoseiulus persimilis) for biological control",
            "Increase humidity through micro-sprinkler irrigation during dry spells",
            "Prune and remove heavily infested branches immediately",
            "Monitor underside of 50 leaves per hectare weekly using hand lens"
        ]
    },
    5: {
        "name": "Green Mirid Bug",
        "severity": "High",
        "description": "The green mirid bug (Helopeltis antonii) feeds on tender shoots and young leaves using piercing-sucking mouthparts. Damage appears as elongated necrotic lesions on shoots, leading to die-back of growing tips.",
        "recommendations": [
            "Apply Imidacloprid (0.005%) or Thiamethoxam (0.01%) systemically",
            "Use yellow sticky traps (20 per hectare) for monitoring adult populations",
            "Maintain optimal shade levels - avoid excessive shade that favors the pest",
            "Encourage bird populations by maintaining perching sites in the estate",
            "Apply botanical insecticides (neem/azadirachtin) as a rotation strategy"
        ]
    },
    6: {
        "name": "Healthy Leaf",
        "severity": "Low",
        "description": "The leaf appears healthy with no visible signs of disease or pest damage. The tissue shows normal green coloration, intact cell structure, and no lesions, spots, or discoloration.",
        "recommendations": [
            "Continue regular monitoring every 7-10 days for early detection",
            "Maintain balanced fertilization (N:P:K = 10:2:8 for young tea)",
            "Ensure proper drainage and soil pH between 4.5-5.5",
            "Practice light, frequent tipping to maintain bush frame",
            "Record observations to establish baseline health metrics for the field"
        ]
    }
}

DISEASE_CLASSES = {
    0: "Algal Leaf Spot", 1: "Brown Blight", 2: "Gray Blight",
    3: "Helopeltis (Tea Mosquito Bug)", 4: "Red Spider",
    5: "Green Mirid Bug", 6: "Healthy Leaf"
}

# ============================================================
# GRADE CLASSIFICATION - DYNAMIC 34-CLASS SYSTEM
# ============================================================
# These will be populated from grade_classes.json on startup
GRADE_CLASSES = {}          # {class_id: "grade_code"}
GRADE_DESCRIPTIONS = {}     # {class_id: "description"}
GRADE_QUALITY_SCORES = {}   # {class_id: score}

# ============================================================
# DEMAND FORECASTING - FEATURE CONFIG
# ============================================================
DEMAND_REGIONS = ['Central Province', 'Uva Province', 'Sabaragamuwa',
                  'Southern Province', 'Nuwara Eliya', 'Dimbula', 'Kandy']
DEMAND_TEA_TYPES = ['CTC', 'Orthodox', 'Green Tea', 'White Tea', 'Oolong']
DEMAND_SEASONAL = {
    1: 1.15, 2: 1.10, 3: 1.08, 4: 0.92, 5: 0.88, 6: 0.85,
    7: 1.05, 8: 1.08, 9: 1.12, 10: 0.95, 11: 0.90, 12: 0.92
}

# Model storage
models = {'disease': None, 'yield': None, 'demand': None, 'grade': None}
scalers = {'yield': None, 'demand': None}
features = {'yield': None, 'demand': None}
grade_config = {'num_classes': 0, 'classes': {}, 'descriptions': {}, 'quality_scores': {}}

# ========== MODEL LOADING ==========

def load_model(name, path, is_keras=False):
    """Generic model loader."""
    fpath = MODELS_DIR / path
    if not fpath.exists():
        print(f"  [{name}] Not found: {fpath}")
        return None
    try:
        if is_keras and KERAS_OK and keras_models:
            m = keras_models.load_model(str(fpath))
        else:
            with open(fpath, 'rb') as f:
                m = pickle.load(f)
        print(f"  [{name}] Loaded successfully")
        return m
    except Exception as e:
        print(f"  [{name}] Error: {e}")
        return None

def load_all():
    print("\n" + "="*60 + "\nLOADING MODELS\n" + "="*60)
    models['disease'] = load_model('Disease', 'disease_model.h5', True) if KERAS_OK else None
    models['yield'], scalers['yield'], features['yield'] = load_yield()
    models['demand'] = load_model('Demand', 'demand_model.pkl')
    scalers['demand'], features['demand'] = load_demand_config()
    models['grade'] = load_model('Grade', 'grade_model.h5', True) if KERAS_OK else None
    load_grade_config()

    print("\n" + "="*60 + "\nMODEL STATUS")
    for n, m in models.items():
        status = 'LOADED' if m else 'SIMULATION'
        extra = ""
        if n == 'grade' and grade_config['num_classes'] > 0:
            extra = f" ({grade_config['num_classes']} classes)"
        print(f"  {n.capitalize():12} {status}{extra}")
    print("="*60)

def load_yield():
    m = load_model('Yield', 'yield_model.pkl')
    s, f = None, None
    try:
        with open(MODELS_DIR / 'yield_scaler.pkl', 'rb') as f:
            s = pickle.load(f)
    except: pass
    try:
        with open(MODELS_DIR / 'yield_feature_names.json', 'r') as f:
            f = json.load(f)
    except: pass
    return m, s, f

def load_demand_config():
    """Load demand model configuration."""
    scaler = None
    feat_names = None
    try:
        with open(MODELS_DIR / 'demand_features.json', 'r') as f:
            feat_names = json.load(f)
        print("  [Demand] Features loaded from demand_features.json")
    except Exception as e:
        print(f"  [Demand] Features not loaded: {e}")
    return scaler, feat_names

def load_grade_config():
    """
    Load grade configuration from JSON files.
    Priority: grade_classes.json → auto-detect from model → fallback defaults.
    """
    global GRADE_CLASSES, GRADE_DESCRIPTIONS, GRADE_QUALITY_SCORES

    num_classes = 0

    # 1. Inspect model output shape
    if models['grade'] is not None and KERAS_OK:
        try:
            output_shape = models['grade'].output_shape
            if isinstance(output_shape, list):
                num_classes = output_shape[0][-1]
            else:
                num_classes = output_shape[-1]
            print(f"  [Grade] Model output shape detected: {num_classes} classes")
        except Exception as e:
            print(f"  [Grade] Could not inspect model: {e}")

    # 2. Load class mapping from grade_classes.json
    try:
        with open(MODELS_DIR / 'grade_classes.json', 'r') as f:
            loaded = json.load(f)
            # Convert string keys to int
            GRADE_CLASSES = {int(k): v for k, v in loaded.items()}
            if len(GRADE_CLASSES) > 0:
                num_classes = len(GRADE_CLASSES)
                print(f"  [Grade] Loaded {num_classes} classes from grade_classes.json")
    except Exception as e:
        print(f"  [Grade] grade_classes.json not found: {e}")
        # Fallback: generate generic names
        GRADE_CLASSES = {i: f"Grade_{i}" for i in range(num_classes)}
        print(f"  [Grade] Using generic class names")

    # 3. Load descriptions from grade_descriptions.json (keys are grade codes like "BP1")
    try:
        with open(MODELS_DIR / 'grade_descriptions.json', 'r') as f:
            loaded_desc = json.load(f)
        # Map by class name
        GRADE_DESCRIPTIONS = {}
        for cid, cname in GRADE_CLASSES.items():
            GRADE_DESCRIPTIONS[cid] = loaded_desc.get(cname, f"Sri Lankan tea grade: {cname}")
        print(f"  [Grade] Loaded descriptions from grade_descriptions.json")
    except Exception as e:
        print(f"  [Grade] grade_descriptions.json not found: {e}")
        GRADE_DESCRIPTIONS = {cid: f"Sri Lankan tea grade: {cname}" 
                              for cid, cname in GRADE_CLASSES.items()}

    # 4. Load quality scores from grade_quality_scores.json
    try:
        with open(MODELS_DIR / 'grade_quality_scores.json', 'r') as f:
            loaded_scores = json.load(f)
        GRADE_QUALITY_SCORES = {}
        for cid, cname in GRADE_CLASSES.items():
            GRADE_QUALITY_SCORES[cid] = loaded_scores.get(cname, 50)
        print(f"  [Grade] Loaded quality scores from grade_quality_scores.json")
    except Exception as e:
        print(f"  [Grade] grade_quality_scores.json not found: {e}")
        # Generate heuristic scores
        GRADE_QUALITY_SCORES = {}
        for cid, cname in GRADE_CLASSES.items():
            if any(x in cname for x in ['Golden_Tips', 'Silver_Tips', 'GC']):
                GRADE_QUALITY_SCORES[cid] = 95
            elif any(x in cname for x in ['FBOPFEXSP', 'FNGS', 'FBOPFSP']):
                GRADE_QUALITY_SCORES[cid] = 90
            elif any(x in cname for x in ['FBOP1', 'GP1', 'BOP1A']):
                GRADE_QUALITY_SCORES[cid] = 85
            elif 'BOP1' in cname or 'OP1' in cname:
                GRADE_QUALITY_SCORES[cid] = 83
            elif 'BOP' in cname or 'FBOP' in cname:
                GRADE_QUALITY_SCORES[cid] = 78
            elif 'BP1' in cname:
                GRADE_QUALITY_SCORES[cid] = 77
            elif 'PF1' in cname:
                GRADE_QUALITY_SCORES[cid] = 71
            elif 'BOPF' in cname:
                GRADE_QUALITY_SCORES[cid] = 72
            elif 'D1' in cname:
                GRADE_QUALITY_SCORES[cid] = 60
            elif cname == 'D':
                GRADE_QUALITY_SCORES[cid] = 55
            else:
                GRADE_QUALITY_SCORES[cid] = 65

    grade_config['num_classes'] = num_classes
    grade_config['classes'] = GRADE_CLASSES
    grade_config['descriptions'] = GRADE_DESCRIPTIONS
    grade_config['quality_scores'] = GRADE_QUALITY_SCORES

# ========== PREDICTIONS ==========

def predict_disease(img_path):
    if models['disease'] and KERAS_OK and PIL_OK:
        try:
            img = Image.open(img_path).convert('RGB').resize((224, 224))
            arr = np.array(img) / 255.0
            arr = np.expand_dims(arr, 0)
            pred = models['disease'].predict(arr, verbose=0)

            if len(pred[0]) != 7:
                print(f"Disease model output {len(pred[0])} classes, expected 7. Using fallback.")
                raise ValueError(f"Model output shape mismatch: {len(pred[0])} != 7")

            did = int(np.argmax(pred[0]))
            conf = float(pred[0][did])
            probs = {DISEASE_CLASSES[i]: round(float(pred[0][i])*100, 2) for i in range(7)}

            info = DISEASE_INFO.get(did, DISEASE_INFO[6])
            return {
                "prediction": info["name"],
                "confidence": round(conf*100, 2),
                "disease_id": did,
                "all_probabilities": probs,
                "severity": info["severity"],
                "description": info["description"],
                "recommendations": info["recommendations"],
                "model_used": "disease_model.h5",
                "note": "AI model active"
            }
        except Exception as e:
            print(f"Disease model error: {e}")
            traceback.print_exc()

    # Simulation fallback
    img_hash = hash(str(img_path)) if img_path else int(datetime.now().timestamp() * 1000)
    random.seed(img_hash % 10000)
    did = random.randint(0, 6)
    conf = random.uniform(0.72, 0.96)
    probs = [random.uniform(0.01, 0.12) for _ in range(7)]
    probs[did] = conf
    total = sum(probs)
    probs = [p/total for p in probs]
    random.seed()

    info = DISEASE_INFO[did]
    return {
        "prediction": info["name"],
        "confidence": round(conf*100, 2),
        "disease_id": did,
        "all_probabilities": {DISEASE_CLASSES[i]: round(probs[i]*100, 2) for i in range(7)},
        "severity": info["severity"],
        "description": info["description"],
        "recommendations": info["recommendations"],
        "model_used": "simulation",
        "note": "Disease model not loaded or error occurred. Using simulation fallback."
    }

def yield_rec(y, t, r, ph, hum=75, fert=250, sun=6, region='Nuwara_Eliya'):
    """Generate dynamic, context-aware AI recommendations."""
    recs = []

    if y < 800:
        recs.append("CRITICAL: Yield is severely below optimal. Immediate action required: increase fertilizer application by 20-30%, check for pest/disease infestation, and ensure adequate soil drainage.")
    elif y < 1200:
        recs.append("Yield is below optimal. Consider increasing NPK fertilizer application, improving soil organic matter, and checking irrigation adequacy.")
    elif y > 2200:
        recs.append("Excellent yield potential detected! Maintain current practices. Consider documenting this configuration for future seasons.")

    if t < 15:
        recs.append(f"Temperature ({t}°C) is too cold for tea. Risk of frost damage. Consider windbreaks or shade trees. Growth will be significantly slowed.")
    elif t < 18:
        recs.append(f"Temperature ({t}°C) is below optimal. Tea growth slows below 18°C. Consider protective mulching to retain soil warmth.")
    elif t > 30:
        recs.append(f"Temperature ({t}°C) is excessively high. Risk of leaf scorch and quality degradation. Increase shade coverage and ensure adequate soil moisture.")
    elif t > 28:
        recs.append(f"Temperature ({t}°C) is above optimal range. Monitor for heat stress. Increase irrigation frequency during peak heat hours.")
    elif 20 <= t <= 24:
        recs.append(f"Temperature ({t}°C) is in the ideal range for tea cultivation. Excellent growing conditions.")

    if r < 1200:
        recs.append(f"Rainfall ({r}mm) is critically low. Drought stress likely. Implement drip irrigation immediately and apply mulch to reduce evaporation.")
    elif r < 1500:
        recs.append(f"Rainfall ({r}mm) is below ideal. Supplement with irrigation during dry spells, especially during flushing periods.")
    elif r > 3000:
        recs.append(f"Excessive rainfall ({r}mm) detected. Risk of root rot and fungal diseases. Improve drainage systems and reduce irrigation.")
    elif r > 2500:
        recs.append(f"Rainfall ({r}mm) is above optimal. Monitor for waterlogging. Ensure proper field drainage and consider raised bed planting.")
    elif 1800 <= r <= 2200:
        recs.append(f"Rainfall ({r}mm) is in the optimal range for tea. Good moisture availability for healthy growth.")

    if hum < 60:
        recs.append(f"Humidity ({hum}%) is too low. Tea requires high humidity. Use micro-sprinklers to increase ambient moisture and apply organic mulch.")
    elif hum > 90:
        recs.append(f"Humidity ({hum}%) is excessively high. High risk of fungal diseases (blight, algal spot). Improve air circulation through strategic pruning.")
    elif 70 <= hum <= 80:
        recs.append(f"Humidity ({hum}%) is ideal for tea. Perfect conditions for tender shoot development.")

    if ph < 4.0:
        recs.append(f"Soil pH ({ph}) is extremely acidic. Apply dolomitic lime (500-800 kg/ha) to raise pH. Check for aluminum toxicity.")
    elif ph < 4.5:
        recs.append(f"Soil pH ({ph}) is too acidic. Apply agricultural lime at 300-500 kg/ha. Acidic soils reduce nutrient availability.")
    elif ph > 6.5:
        recs.append(f"Soil pH ({ph}) is too alkaline for tea. Tea prefers acidic soils. Apply elemental sulfur (100-200 kg/ha) or acidic organic matter.")
    elif ph > 6.0:
        recs.append(f"Soil pH ({ph}) is slightly high. Consider adding peat moss or pine needle mulch to gently acidify the soil.")
    elif 4.8 <= ph <= 5.5:
        recs.append(f"Soil pH ({ph}) is in the ideal range for tea. Optimal nutrient uptake conditions.")

    if fert < 150:
        recs.append(f"Fertilizer application ({fert} kg/ha) is insufficient. Tea is a heavy feeder. Increase to at least 200-250 kg/ha NPK split across the year.")
    elif fert > 400:
        recs.append(f"Fertilizer application ({fert} kg/ha) is very high. Risk of nutrient runoff and soil acidification. Consider soil testing before further application.")

    if sun < 4:
        recs.append(f"Sunshine hours ({sun}h) are too low. Tea needs 5-6 hours for photosynthesis. Prune overhead shade trees if light is insufficient.")
    elif sun > 8:
        recs.append(f"Excessive sunshine ({sun}h) may cause leaf burn and reduce quality. Ensure adequate shade tree coverage (30-40% shade recommended).")

    region_tips = {
        'Nuwara_Eliya': 'High elevation (upcountry) - focus on frost protection during dry months and windbreaks.',
        'Uda_Pussellawa': 'Mid-elevation - balance between sunlight and shade. Monitor for sudden weather changes.',
        'Uva': 'Eastern slopes - dry season irrigation is critical. Focus on water conservation techniques.',
        'Dimbula': 'Western slopes - high rainfall area. Prioritize drainage and disease prevention.',
        'Kandy': 'Central region - maintain traditional cultivation practices with modern pest management.',
        'Ruhuna': 'Low country - higher temperatures require increased shade and irrigation management.',
        'Sabaragamuwa': 'Wet zone - excellent natural conditions. Focus on quality optimization over quantity.',
        'Low_Country': 'Low elevation - fast growth but lower quality. Focus on processing techniques to enhance value.'
    }
    if region in region_tips:
        recs.append(f"Region tip ({region}): {region_tips[region]}")

    if not recs:
        return "All environmental conditions are optimal. Continue current best practices and maintain regular monitoring schedule."

    return " ".join(recs)

def predict_yield(data):
    if models['yield']:
        try:
            region = data.get('region', 'Nuwara_Eliya')
            feats = {'temperature_c': data.get('temperature', 22), 'rainfall_mm': data.get('rainfall', 1800),
                     'humidity_percent': data.get('humidity', 75), 'soil_ph': data.get('soil_ph', 5.2),
                     'fertilizer_kg_ha': data.get('fertilizer', 250), 'sunshine_hours': data.get('sunshine', 6)}
            for r in ['Nuwara_Eliya','Uda_Pussellawa','Uva','Dimbula','Kandy','Ruhuna','Sabaragamuwa','Low_Country']:
                feats[f'region_{r}'] = 1 if r == region else 0
            df = pd.DataFrame([feats])
            if features['yield']: df = df.reindex(columns=features['yield'], fill_value=0)
            X = scalers['yield'].transform(df) if scalers['yield'] else df.values
            pred = models['yield'].predict(X)[0]
            return {"predicted_yield_kg_ha": round(float(pred), 0), "confidence": "high",
                    "recommendation": yield_rec(pred, feats['temperature_c'], feats['rainfall_mm'], feats['soil_ph'], feats['humidity_percent'], feats['fertilizer_kg_ha'], feats['sunshine_hours'], region),
                    "model_used": "yield_model.pkl"}
        except Exception as e:
            print(f"Yield error: {e}")
            traceback.print_exc()

    # Simulation
    temp = data.get('temperature', 22); rainfall = data.get('rainfall', 1800)
    hum = data.get('humidity', 75); ph = data.get('soil_ph', 5.2)
    fert = data.get('fertilizer', 250); sun = data.get('sunshine', 6)
    region = data.get('region', 'Nuwara_Eliya')
    base = {'Nuwara_Eliya':1200,'Uda_Pussellawa':1250,'Uva':1400,'Dimbula':1450,
            'Kandy':1500,'Ruhuna':1600,'Sabaragamuwa':1550,'Low_Country':1700}.get(region, 1400)
    y = base * (1-abs(temp-22)/20) * min(rainfall/2000,1.2) * (hum/100) * (1-abs(ph-5.2)/3) * (fert/300) * (sun/7)
    y = max(400, min(3000, y * random.uniform(0.9, 1.1)))
    return {"predicted_yield_kg_ha": round(y, 0), "confidence": "medium",
            "recommendation": yield_rec(y, temp, rainfall, ph, hum, fert, sun, region), "model_used": "simulation",
            "note": "Yield model not loaded. Using simulation fallback."}

def predict_demand(data):
    """Use trained ML model for demand forecasting. Falls back to seasonal simulation."""
    year = data.get('year', datetime.now().year)
    month = data.get('month', datetime.now().month)
    exports = data.get('exports', 22000)
    region = data.get('region', 'Central Province')
    tea_type = data.get('tea_type', 'CTC')
    growth_rate = data.get('growth_rate', 8.0)
    historical_demand = data.get('historical_demand', exports * 1.08)

    # Try ML model first
    if models['demand'] is not None and features['demand'] is not None and PD_OK and NP_OK:
        try:
            feats = {
                'historical_demand': historical_demand,
                'exports': exports,
                'growth_rate': growth_rate,
                'quarter': (month - 1) // 3 + 1,
                'month_sin': np.sin(2 * np.pi * month / 12),
                'month_cos': np.cos(2 * np.pi * month / 12),
                'demand_lag_1': historical_demand * 0.98,
                'demand_lag_3': historical_demand * 0.95,
                'demand_lag_6': historical_demand * 0.92,
                'demand_ma_3': historical_demand,
                'demand_ma_6': historical_demand,
            }
            for r in DEMAND_REGIONS:
                feats[f'region_{r}'] = 1 if r == region else 0
            for t in DEMAND_TEA_TYPES:
                feats[f'type_{t}'] = 1 if t == tea_type else 0

            df = pd.DataFrame([feats])
            df = df.reindex(columns=features['demand'], fill_value=0)
            pred_demand = float(models['demand'].predict(df)[0])

            forecasts = []
            current_pred = pred_demand
            for i in range(1, 4):
                nm = ((month - 1 + i) % 12) + 1
                ny = year + ((month - 1 + i) // 12)
                next_feats = feats.copy()
                next_feats['month_sin'] = np.sin(2 * np.pi * nm / 12)
                next_feats['month_cos'] = np.cos(2 * np.pi * nm / 12)
                next_feats['quarter'] = (nm - 1) // 3 + 1
                next_feats['demand_lag_1'] = current_pred
                next_feats['demand_ma_3'] = current_pred
                next_feats['demand_ma_6'] = current_pred
                df_next = pd.DataFrame([next_feats])
                df_next = df_next.reindex(columns=features['demand'], fill_value=0)
                fc = float(models['demand'].predict(df_next)[0])
                current_pred = fc
                forecasts.append({
                    "year": ny, "month": nm,
                    "predicted_demand_mt": round(fc, 0),
                    "confidence_interval": [round(fc * 0.92, 0), round(fc * 1.08, 0)]
                })

            return {
                "current_demand_mt": round(pred_demand, 0),
                "forecast_next_3_months": forecasts,
                "trend": "stable",
                "model_used": "demand_model.pkl (ML)",
                "features_used": len(features['demand']),
                "note": "AI/ML prediction using trained GradientBoosting model"
            }
        except Exception as e:
            print(f"Demand ML prediction error: {e}")
            traceback.print_exc()
            print("Falling back to seasonal simulation...")

    # Seasonal simulation fallback
    seasonal = DEMAND_SEASONAL
    base = exports * 1.08 * seasonal.get(month, 1.0) * random.uniform(0.95, 1.05)
    forecasts = []
    for i in range(1, 4):
        nm = ((month - 1 + i) % 12) + 1
        ny = year + ((month - 1 + i) // 12)
        fc = base * seasonal.get(nm, 1.0) * random.uniform(0.97, 1.03)
        forecasts.append({"year": ny, "month": nm, "predicted_demand_mt": round(fc, 0),
                          "confidence_interval": [round(fc * 0.9, 0), round(fc * 1.1, 0)]})

    return {
        "current_demand_mt": round(base, 0),
        "forecast_next_3_months": forecasts,
        "trend": "stable",
        "model_used": "simulation",
        "note": "Demand ML model not loaded or error occurred. Using seasonal simulation fallback."
    }

def predict_grade(img_path):
    if models['grade'] and KERAS_OK and PIL_OK:
        try:
            img = Image.open(img_path).convert('RGB').resize((224, 224))
            arr = np.array(img) / 255.0
            arr = np.expand_dims(arr, 0)
            pred = models['grade'].predict(arr, verbose=0)

            num_outputs = len(pred[0])
            gid = int(np.argmax(pred[0]))
            conf = float(pred[0][gid])

            # Safe class lookup with fallback
            grade_name = GRADE_CLASSES.get(gid, f"Grade_{gid}")
            desc = GRADE_DESCRIPTIONS.get(gid, f"Sri Lankan tea grade: {grade_name}")
            score = GRADE_QUALITY_SCORES.get(gid, 50)

            # Build all probabilities
            all_probs = {}
            for i in range(num_outputs):
                name = GRADE_CLASSES.get(i, f"Class_{i}")
                all_probs[name] = round(float(pred[0][i]) * 100, 2)

            return {
                "prediction": grade_name,
                "grade_id": gid,
                "confidence": round(conf * 100, 2),
                "quality_description": desc,
                "quality_score": score,
                "all_probabilities": all_probs,
                "model_used": "grade_model.h5",
                "num_classes": num_outputs,
                "note": "AI model active"
            }
        except Exception as e:
            print(f"Grade model error: {e}")
            traceback.print_exc()

    # Simulation fallback
    img_hash = hash(str(img_path)) if img_path else int(datetime.now().timestamp() * 1000)
    random.seed(img_hash % 10000)
    max_id = max(GRADE_CLASSES.keys()) if GRADE_CLASSES else 5
    gid = random.randint(0, max_id)
    conf = round(random.uniform(70, 95), 2)
    random.seed()

    grade_name = GRADE_CLASSES.get(gid, f"Grade_{gid}")
    desc = GRADE_DESCRIPTIONS.get(gid, f"Tea grade {grade_name}")
    score = GRADE_QUALITY_SCORES.get(gid, 50)

    return {
        "prediction": grade_name,
        "grade_id": gid,
        "confidence": conf,
        "quality_description": desc,
        "quality_score": score,
        "model_used": "simulation",
        "note": "Grade model not loaded or error occurred. Using simulation fallback."
    }

# ========== FLASK ROUTES ==========

if FLASK_OK:
    app = Flask(__name__)
    CORS(app)
    app.config['UPLOAD_FOLDER'] = str(UPLOADS)
    app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024
else:
    app = None

def allowed(f): return '.' in f and f.rsplit('.', 1)[1].lower() in {'png','jpg','jpeg'}

@app.route('/health', methods=['GET'])
def health():
    return jsonify({
        "status": "healthy",
        "timestamp": datetime.now().isoformat(),
        "models": {n: {"loaded": m is not None} for n, m in models.items()},
        "grade_classes_loaded": grade_config['num_classes'],
        "grade_class_names": list(GRADE_CLASSES.values())[:10] if GRADE_CLASSES else []
    })

@app.route('/predict/disease', methods=['POST'])
def disease_ep():
    if 'image' not in request.files: return jsonify({"error": "No image"}), 400
    f = request.files['image']
    if f and allowed(f.filename):
        path = UPLOADS / secure_filename(f.filename); f.save(str(path))
        r = predict_disease(str(path)); os.remove(str(path)); return jsonify(r)
    return jsonify({"error": "Invalid file"}), 400

@app.route('/predict/yield', methods=['POST'])
def yield_ep():
    d = request.get_json()
    if not d: return jsonify({"error": "No data"}), 400
    return jsonify(predict_yield(d))

@app.route('/predict/demand', methods=['POST'])
def demand_ep():
    d = request.get_json()
    if not d: return jsonify({"error": "No data"}), 400
    return jsonify(predict_demand(d))

@app.route('/predict/grade', methods=['POST'])
def grade_ep():
    if 'image' not in request.files: return jsonify({"error": "No image"}), 400
    f = request.files['image']
    if f and allowed(f.filename):
        path = UPLOADS / secure_filename(f.filename); f.save(str(path))
        r = predict_grade(str(path)); os.remove(str(path)); return jsonify(r)
    return jsonify({"error": "Invalid file"}), 400

# ========== MAIN ==========

def banner():
    print("="*70)
    print("  TEAMIND - AI Flask API Server (FINAL FIXED)")
    print("="*70)
    print(f"  Python: {sys.version.split()[0]} | NumPy: {NP_OK} | Pillow: {PIL_OK} | TF: {TF_OK} | Keras: {KERAS_OK}")
    print("="*70)
    print("  POST /predict/disease  - Disease detection (image)")
    print("  POST /predict/yield    - Yield prediction (JSON)")
    print("  POST /predict/demand   - Demand forecast (JSON) [ML MODEL]")
    print("  POST /predict/grade    - Grade classification (image) [34 CLASSES]")
    print("  GET  /health           - Status check")
    print("="*70)

if __name__ == "__main__":
    banner(); load_all()
    if app: app.run(host='0.0.0.0', port=5000, debug=False)
    else: print("Flask not installed. Run: pip install Flask Flask-CORS")