#!/usr/bin/env python3
"""
TEAMIND - Model Diagnostic Tool
Run this to inspect your trained models and verify they match the API.
"""

import os
import sys
import json
from pathlib import Path

BASE = Path(__file__).parent
MODELS_DIR = BASE / "models"

def check_file(path, label):
    if path.exists():
        size = path.stat().st_size
        print(f"  ✅ {label}: {path.name} ({size:,} bytes)")
        return True
    else:
        print(f"  ❌ {label}: {path.name} NOT FOUND")
        return False

print("=" * 70)
print("  TEAMIND - Model File Diagnostic")
print("=" * 70)

# Check all expected files
print("\n📁 Checking models/ folder...")
files = {
    "Disease Model": "disease_model.h5",
    "Yield Model": "yield_model.pkl",
    "Yield Scaler": "yield_scaler.pkl",
    "Yield Features": "yield_feature_names.json",
    "Demand Model": "demand_model.pkl",
    "Demand Features": "demand_features.json",
    "Demand Seasonal": "demand_seasonal.json",
    "Grade Model": "grade_model.h5",
    "Grade Classes": "grade_classes.json",
    "Grade Descriptions": "grade_descriptions.json",
}

found = {}
for label, fname in files.items():
    found[label] = check_file(MODELS_DIR / fname, label)

# Try to inspect Keras models
print("\n🔍 Inspecting Keras models...")
try:
    import tensorflow as tf
    print(f"  TensorFlow version: {tf.__version__}")

    # Disease model
    if found["Disease Model"]:
        try:
            m = tf.keras.models.load_model(str(MODELS_DIR / "disease_model.h5"))
            print(f"\n  📊 Disease Model:")
            print(f"     Input shape: {m.input_shape}")
            print(f"     Output shape: {m.output_shape}")
            print(f"     Layers: {len(m.layers)}")
            print(f"     Expected classes: {m.output_shape[-1] if m.output_shape else 'unknown'}")
        except Exception as e:
            print(f"     Error loading: {e}")

    # Grade model
    if found["Grade Model"]:
        try:
            m = tf.keras.models.load_model(str(MODELS_DIR / "grade_model.h5"))
            print(f"\n  📊 Grade Model:")
            print(f"     Input shape: {m.input_shape}")
            print(f"     Output shape: {m.output_shape}")
            print(f"     Layers: {len(m.layers)}")
            output_classes = m.output_shape[-1] if m.output_shape else 'unknown'
            print(f"     ⚠️  OUTPUT CLASSES: {output_classes}")
            if output_classes != 6:
                print(f"     ⚠️  MISMATCH: API expects 6 classes, model has {output_classes}!")
            else:
                print(f"     ✅ Output classes match API (6 classes)")
        except Exception as e:
            print(f"     Error loading: {e}")

except ImportError:
    print("  TensorFlow not installed. Cannot inspect .h5 models.")

# Inspect pickle models
print("\n🔍 Inspecting pickle models...")
try:
    import pickle

    # Yield model
    if found["Yield Model"]:
        try:
            with open(MODELS_DIR / "yield_model.pkl", 'rb') as f:
                m = pickle.load(f)
            print(f"\n  📊 Yield Model:")
            print(f"     Type: {type(m).__name__}")
            if hasattr(m, 'n_features_in_'):
                print(f"     Features expected: {m.n_features_in_}")
        except Exception as e:
            print(f"     Error loading: {e}")

    # Demand model
    if found["Demand Model"]:
        try:
            with open(MODELS_DIR / "demand_model.pkl", 'rb') as f:
                m = pickle.load(f)
            print(f"\n  📊 Demand Model:")
            print(f"     Type: {type(m).__name__}")
            if hasattr(m, 'n_features_in_'):
                print(f"     Features expected: {m.n_features_in_}")
            if hasattr(m, 'feature_names_in_'):
                print(f"     Feature names: {list(m.feature_names_in_)[:5]}...")
        except Exception as e:
            print(f"     Error loading: {e}")

except Exception as e:
    print(f"  Error: {e}")

# Check feature files
print("\n📋 Checking feature mappings...")

if found["Demand Features"]:
    try:
        with open(MODELS_DIR / "demand_features.json") as f:
            feats = json.load(f)
        print(f"  Demand features ({len(feats)}): {feats[:5]}...")
    except Exception as e:
        print(f"  Error: {e}")
else:
    print("  ❌ demand_features.json missing - demand ML will not work!")

if found["Grade Classes"]:
    try:
        with open(MODELS_DIR / "grade_classes.json") as f:
            classes = json.load(f)
        print(f"  Grade classes ({len(classes)}): {classes}")
    except Exception as e:
        print(f"  Error: {e}")
else:
    print("  ⚠️  grade_classes.json missing - will use default 6-class mapping")

print("\n" + "=" * 70)
print("  SUMMARY")
print("=" * 70)

issues = []
if not found["Demand Model"]:
    issues.append("Demand model missing - demand will use simulation")
if not found["Demand Features"]:
    issues.append("Demand features missing - demand ML cannot work even if model exists")
if found.get("Grade Model") and found.get("Grade Classes"):
    # We already checked above
    pass
elif found.get("Grade Model") and not found.get("Grade Classes"):
    issues.append("Grade model exists but class mapping missing - may cause class mismatch errors")

if issues:
    print("\n  Issues found:")
    for i, issue in enumerate(issues, 1):
        print(f"  {i}. {issue}")
else:
    print("\n  ✅ All critical files present")

print("\n  To fix missing files, re-run training:")
print("     python train_demand.py --generate_data")
print("     python train_grade.py --generate_data")
print("=" * 70)