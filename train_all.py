#!/usr/bin/env python3
"""
TEAMIND - Master Training Script
Runs training for all 4 AI modules sequentially.

Usage:
    python training_scripts/train_all.py --generate_all --epochs 50
"""

import os
import sys
import argparse
import subprocess
import json
from pathlib import Path
from datetime import datetime

def run_script(script_name, args_list):
    """Run a training script and capture output."""
    script_path = Path(".") / script_name

    if not script_path.exists():
        print(f"ERROR: {script_path} not found!")
        return False

    print("\n" + "=" * 70)
    print(f"  RUNNING: {script_name}")
    print("=" * 70)

    cmd = [sys.executable, str(script_path)] + args_list

    try:
        result = subprocess.run(cmd, check=True)
        print(f"\n✓ {script_name} completed successfully")
        return True
    except subprocess.CalledProcessError as e:
        print(f"\n✗ {script_name} failed with code {e.returncode}")
        return False

def main():
    parser = argparse.ArgumentParser(description="Train all TEAMIND AI models")
    parser.add_argument("--generate_all", action="store_true", help="Generate all synthetic datasets")
    parser.add_argument("--epochs", type=int, default=50, help="Epochs for image models")
    parser.add_argument("--batch_size", type=int, default=32, help="Batch size")
    parser.add_argument("--skip_disease", action="store_true", help="Skip disease training")
    parser.add_argument("--skip_yield", action="store_true", help="Skip yield training")
    parser.add_argument("--skip_demand", action="store_true", help="Skip demand training")
    parser.add_argument("--skip_grade", action="store_true", help="Skip grade training")
    args = parser.parse_args()

    print("=" * 70)
    print("  TEAMIND - Master Training Pipeline")
    print("=" * 70)
    print(f"  Started: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("=" * 70)

    results = {}

    # 1. Disease Detection
    if not args.skip_disease:
        disease_args = ["--epochs", str(args.epochs), "--batch_size", str(args.batch_size)]
        if args.generate_all:
            disease_args.append("--generate_data")
        results['disease'] = run_script("train_disease.py", disease_args)

    # 2. Yield Prediction
    if not args.skip_yield:
        yield_args = []
        if args.generate_all:
            yield_args.append("--generate_data")
        results['yield'] = run_script("train_yield.py", yield_args)

    # 3. Demand Forecasting
    if not args.skip_demand:
        demand_args = []
        if args.generate_all:
            demand_args.append("--generate_data")
        results['demand'] = run_script("train_demand.py", demand_args)

    # 4. Grade Classification
    if not args.skip_grade:
        grade_args = ["--epochs", str(args.epochs), "--batch_size", str(args.batch_size)]
        if args.generate_all:
            grade_args.append("--generate_data")
        results['grade'] = run_script("train_grade.py", grade_args)

    # Summary
    print("\n" + "=" * 70)
    print("  TRAINING SUMMARY")
    print("=" * 70)
    for model, success in results.items():
        status = "✓ SUCCESS" if success else "✗ FAILED"
        print(f"  {model.capitalize():<15} {status}")

    # Save summary
    summary = {
        'training_date': datetime.now().isoformat(),
        'results': results,
        'config': vars(args)
    }

    summary_path = Path("models") / "training_summary.json"
    summary_path.parent.mkdir(parents=True, exist_ok=True)
    with open(summary_path, "w") as f:
        json.dump(summary, f, indent=2)

    print(f"\n✓ Summary saved: {summary_path}")
    print("\n" + "=" * 70)
    print("  Next Steps:")
    print("  1. Copy .h5 and .pkl files from training_scripts/models/ to python/models/")
    print("  2. Restart the Flask API: python app.py")
    print("  3. Test predictions via the web interface")
    print("=" * 70)

if __name__ == "__main__":
    main()
