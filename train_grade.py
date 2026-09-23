#!/usr/bin/env python3
"""
TEAMIND - Tea Grade Classification Training Script
Trains a CNN model to classify Sri Lankan tea grades.

Dataset Sources:
- Custom dataset of Sri Lankan tea grade images
- Synthetic data generation for testing

Classes (6):
    0: BP1  - Broken Pekoe 1 (CTC - Grade A+)
    1: PF1  - Pekoe Fannings 1 (CTC - Grade A)
    2: BOP1 - Broken Orange Pekoe 1 (Orthodox - Grade B)
    3: BOPF - Broken Orange Pekoe Fannings (Orthodox - Grade C)
    4: D1   - Dust 1 (Orthodox - Grade D)
    5: D    - Dust (Orthodox - Lowest grade)

Usage:
    python training_scripts/train_grade.py --data_dir datasets/grade --epochs 50 --batch_size 32
"""

import os
import sys
import argparse
import json
import numpy as np
from pathlib import Path
from datetime import datetime

# Suppress TensorFlow warnings
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '2'

try:
    import tensorflow as tf
    import keras
    from keras import layers, models, callbacks, optimizers
    from keras.preprocessing.image import ImageDataGenerator
    from keras.applications import EfficientNetB0
    TF_AVAILABLE = True
except ImportError:
    print("ERROR: TensorFlow not installed. Run: pip install tensorflow")
    sys.exit(1)

# Configuration
IMG_SIZE = 224
BATCH_SIZE = 32
EPOCHS = 50
LEARNING_RATE = 0.001

CLASS_NAMES = [
    "BP1",
    "PF1",
    "BOP1",
    "BOPF",
    "D1",
    "D"
]

GRADE_DESCRIPTIONS = {
    "BP1": "Broken Pekoe 1 - CTC grade with bold round particles, full body, bright liquor. Largest CTC particles.",
    "PF1": "Pekoe Fannings 1 - CTC grade with strong tasting, granular particles. Smaller than BP1.",
    "BOP1": "Broken Orange Pekoe 1 - Orthodox semi-leaf with mild malty taste and bright infusion.",
    "BOPF": "Broken Orange Pekoe Fannings - Orthodox small particles, strong, fast-brewing, ideal for tea bags.",
    "D1": "Dust 1 - Orthodox clean dust, smallest particles, thick strong liquoring tea.",
    "D": "Dust - Orthodox standard dust, fine powder, strong and robust, rapid infusion."
}

QUALITY_SCORES = {
    "BP1": 77,
    "PF1": 71,
    "BOP1": 83,
    "BOPF": 72,
    "D1": 60,
    "D": 55
}

def create_data_generators(data_dir, img_size=IMG_SIZE, batch_size=BATCH_SIZE):
    """Create training and validation data generators with augmentation."""

    train_dir = Path(data_dir) / "train"
    val_dir = Path(data_dir) / "val"

    # If no val folder, we'll split from train
    if not val_dir.exists():
        print("No validation folder found. Will use 20% of training data for validation.")
        validation_split = 0.2
    else:
        validation_split = 0.0

    # Training data augmentation
    train_datagen = ImageDataGenerator(
        rescale=1./255,
        rotation_range=30,
        width_shift_range=0.2,
        height_shift_range=0.2,
        shear_range=0.2,
        zoom_range=0.2,
        horizontal_flip=True,
        vertical_flip=True,
        brightness_range=[0.8, 1.2],
        fill_mode='nearest',
        validation_split=validation_split
    )

    # Validation data (only rescaling)
    val_datagen = ImageDataGenerator(rescale=1./255)

    # Training generator
    if validation_split > 0:
        train_generator = train_datagen.flow_from_directory(
            train_dir,
            target_size=(img_size, img_size),
            batch_size=batch_size,
            class_mode='categorical',
            subset='training',
            shuffle=True,
            seed=42
        )
        val_generator = train_datagen.flow_from_directory(
            train_dir,
            target_size=(img_size, img_size),
            batch_size=batch_size,
            class_mode='categorical',
            subset='validation',
            shuffle=False,
            seed=42
        )
    else:
        train_generator = train_datagen.flow_from_directory(
            train_dir,
            target_size=(img_size, img_size),
            batch_size=batch_size,
            class_mode='categorical',
            shuffle=True
        )
        val_generator = val_datagen.flow_from_directory(
            val_dir,
            target_size=(img_size, img_size),
            batch_size=batch_size,
            class_mode='categorical',
            shuffle=False
        )

    return train_generator, val_generator

def create_model(num_classes=6, img_size=IMG_SIZE):
    """Create EfficientNetB0-based model with custom classification head."""

    # Use EfficientNetB0 as base (lightweight but powerful)
    base_model = EfficientNetB0(
        weights='imagenet',
        include_top=False,
        input_shape=(img_size, img_size, 3)
    )

    # Freeze base model layers initially
    base_model.trainable = False

    # Build model
    model = models.Sequential([
        base_model,
        layers.GlobalAveragePooling2D(),
        layers.BatchNormalization(),
        layers.Dropout(0.3),
        layers.Dense(256, activation='relu'),
        layers.BatchNormalization(),
        layers.Dropout(0.3),
        layers.Dense(128, activation='relu'),
        layers.BatchNormalization(),
        layers.Dropout(0.2),
        layers.Dense(num_classes, activation='softmax')
    ])

    return model

def train_model(model, train_gen, val_gen, epochs=EPOCHS, lr=LEARNING_RATE):
    """Train the model with callbacks."""

    # Compile
    model.compile(
        optimizer=optimizers.Adam(learning_rate=lr),
        loss='categorical_crossentropy',
        metrics=['accuracy', 'Precision', 'Recall']
    )

    # Callbacks
    checkpoint_cb = callbacks.ModelCheckpoint(
        'models/grade_model_best.keras',
        monitor='val_accuracy',
        save_best_only=True,
        mode='max',
        verbose=1
    )

    early_stop_cb = callbacks.EarlyStopping(
        monitor='val_loss',
        patience=10,
        restore_best_weights=True,
        verbose=1
    )

    reduce_lr_cb = callbacks.ReduceLROnPlateau(
        monitor='val_loss',
        factor=0.5,
        patience=5,
        min_lr=1e-7,
        verbose=1
    )

    # Train (Phase 1: frozen base)
    print("\n=== Phase 1: Training with frozen base ===")
    history1 = model.fit(
        train_gen,
        epochs=min(epochs // 2, 20),
        validation_data=val_gen,
        callbacks=[checkpoint_cb, early_stop_cb, reduce_lr_cb],
        verbose=1
    )

    # Fine-tuning: unfreeze top layers of base model
    print("\n=== Phase 2: Fine-tuning ===")
    model.layers[0].trainable = True

    # Freeze bottom layers, train top layers
    for layer in model.layers[0].layers[:-30]:
        layer.trainable = False

    model.compile(
        optimizer=optimizers.Adam(learning_rate=lr / 10),
        loss='categorical_crossentropy',
        metrics=['accuracy', 'Precision', 'Recall']
    )

    history2 = model.fit(
        train_gen,
        epochs=epochs,
        initial_epoch=len(history1.history['loss']),
        validation_data=val_gen,
        callbacks=[checkpoint_cb, early_stop_cb, reduce_lr_cb],
        verbose=1
    )

    # Combine histories
    combined_history = {}
    for key in history1.history:
        combined_history[key] = history1.history[key] + history2.history[key]

    return model, combined_history

def evaluate_model(model, val_gen):
    """Evaluate model and print metrics."""
    print("\n=== Model Evaluation ===")
    results = model.evaluate(val_gen, verbose=1)
    metrics = dict(zip(model.metrics_names, results))

    for metric, value in metrics.items():
        print(f"  {metric}: {value:.4f}")

    return metrics

def save_model(model, output_dir="models"):
    """Save model in multiple formats."""
    output_path = Path(output_dir)
    output_path.mkdir(parents=True, exist_ok=True)

    # Save as .keras (native TF 2.21 format)
    keras_path = output_path / "grade_model.keras"
    model.save(str(keras_path))
    print(f"\n✓ Model saved: {keras_path}")

    # Save as .h5 (for backward compatibility with app.py)
    h5_path = output_path / "grade_model.h5"
    try:
        model.save(str(h5_path))
        print(f"✓ Model saved: {h5_path}")
    except Exception as e:
        print(f"  Note: .h5 save skipped ({e})")

    # Save class mapping
    class_map = {i: name for i, name in enumerate(CLASS_NAMES)}
    with open(output_path / "grade_classes.json", "w") as f:
        json.dump(class_map, f, indent=2)
    print(f"✓ Class mapping saved: {output_path / 'grade_classes.json'}")

    # Save grade descriptions
    with open(output_path / "grade_descriptions.json", "w") as f:
        json.dump(GRADE_DESCRIPTIONS, f, indent=2)
    print(f"✓ Grade descriptions saved: {output_path / 'grade_descriptions.json'}")

    return keras_path

def generate_synthetic_dataset(output_dir="datasets/grade", num_samples_per_class=100):
    """Generate synthetic dataset for testing when real data is unavailable.

    Each grade has distinct visual characteristics:
    - BP1: Bold, round, dark brown/black particles (largest CTC)
    - PF1: Smaller, granular, dark particles (CTC)
    - BOP1: Semi-leaf, wiry, dark with some texture (Orthodox)
    - BOPF: Small broken pieces, uniform dark (Orthodox)
    - D1: Very fine, powdery, dark brown (Orthodox dust)
    - D: Fine powder, slightly lighter, dusty appearance
    """
    print("\n=== Generating Synthetic Tea Grade Dataset ===")

    output_path = Path(output_dir)

    for class_name in CLASS_NAMES:
        class_dir = output_path / "train" / class_name
        class_dir.mkdir(parents=True, exist_ok=True)

        for i in range(num_samples_per_class):
            # Create synthetic image with grade-specific color/texture patterns
            img = np.random.randint(0, 255, (IMG_SIZE, IMG_SIZE, 3), dtype=np.uint8)

            # Grade-specific visual patterns
            if class_name == "BP1":
                # Bold round particles - larger dark spots on brown background
                img[:, :, 0] = np.clip(np.random.randint(40, 80, (IMG_SIZE, IMG_SIZE)), 0, 255)
                img[:, :, 1] = np.clip(np.random.randint(30, 60, (IMG_SIZE, IMG_SIZE)), 0, 255)
                img[:, :, 2] = np.clip(np.random.randint(20, 50, (IMG_SIZE, IMG_SIZE)), 0, 255)
                # Add larger particle spots
                for _ in range(30):
                    cx, cy = np.random.randint(0, IMG_SIZE, 2)
                    r = np.random.randint(8, 20)
                    y, x = np.ogrid[:IMG_SIZE, :IMG_SIZE]
                    mask = (x - cx)**2 + (y - cy)**2 <= r**2
                    img[mask] = [60, 45, 35]

            elif class_name == "PF1":
                # Smaller granular particles
                img[:, :, 0] = np.clip(np.random.randint(50, 90, (IMG_SIZE, IMG_SIZE)), 0, 255)
                img[:, :, 1] = np.clip(np.random.randint(35, 65, (IMG_SIZE, IMG_SIZE)), 0, 255)
                img[:, :, 2] = np.clip(np.random.randint(25, 55, (IMG_SIZE, IMG_SIZE)), 0, 255)
                for _ in range(60):
                    cx, cy = np.random.randint(0, IMG_SIZE, 2)
                    r = np.random.randint(4, 12)
                    y, x = np.ogrid[:IMG_SIZE, :IMG_SIZE]
                    mask = (x - cx)**2 + (y - cy)**2 <= r**2
                    img[mask] = [70, 50, 40]

            elif class_name == "BOP1":
                # Semi-leaf, wiry texture with some variation
                img[:, :, 0] = np.clip(np.random.randint(45, 85, (IMG_SIZE, IMG_SIZE)), 0, 255)
                img[:, :, 1] = np.clip(np.random.randint(35, 70, (IMG_SIZE, IMG_SIZE)), 0, 255)
                img[:, :, 2] = np.clip(np.random.randint(25, 60, (IMG_SIZE, IMG_SIZE)), 0, 255)
                # Add wiry lines
                for _ in range(20):
                    x1, y1 = np.random.randint(0, IMG_SIZE, 2)
                    x2, y2 = x1 + np.random.randint(-30, 30), y1 + np.random.randint(-5, 5)
                    cv2 = None
                    try:
                        import cv2
                        cv2.line(img, (x1, y1), (x2, y2), [65, 50, 40], 2)
                    except:
                        pass

            elif class_name == "BOPF":
                # Small broken pieces, more uniform
                img[:, :, 0] = np.clip(np.random.randint(55, 95, (IMG_SIZE, IMG_SIZE)), 0, 255)
                img[:, :, 1] = np.clip(np.random.randint(40, 70, (IMG_SIZE, IMG_SIZE)), 0, 255)
                img[:, :, 2] = np.clip(np.random.randint(30, 60, (IMG_SIZE, IMG_SIZE)), 0, 255)
                for _ in range(80):
                    cx, cy = np.random.randint(0, IMG_SIZE, 2)
                    r = np.random.randint(3, 10)
                    y, x = np.ogrid[:IMG_SIZE, :IMG_SIZE]
                    mask = (x - cx)**2 + (y - cy)**2 <= r**2
                    img[mask] = [75, 55, 45]

            elif class_name == "D1":
                # Fine dust, very dark, powdery
                img[:, :, 0] = np.clip(np.random.randint(30, 70, (IMG_SIZE, IMG_SIZE)), 0, 255)
                img[:, :, 1] = np.clip(np.random.randint(20, 55, (IMG_SIZE, IMG_SIZE)), 0, 255)
                img[:, :, 2] = np.clip(np.random.randint(15, 45, (IMG_SIZE, IMG_SIZE)), 0, 255)
                # Very fine grain
                noise = np.random.randint(-10, 10, (IMG_SIZE, IMG_SIZE, 3))
                img = np.clip(img.astype(np.int16) + noise, 0, 255).astype(np.uint8)

            elif class_name == "D":
                # Standard dust, slightly lighter, more variation
                img[:, :, 0] = np.clip(np.random.randint(40, 80, (IMG_SIZE, IMG_SIZE)), 0, 255)
                img[:, :, 1] = np.clip(np.random.randint(30, 65, (IMG_SIZE, IMG_SIZE)), 0, 255)
                img[:, :, 2] = np.clip(np.random.randint(20, 55, (IMG_SIZE, IMG_SIZE)), 0, 255)
                noise = np.random.randint(-15, 15, (IMG_SIZE, IMG_SIZE, 3))
                img = np.clip(img.astype(np.int16) + noise, 0, 255).astype(np.uint8)

            # Save as JPEG
            from PIL import Image as PILImage
            pil_img = PILImage.fromarray(img)
            pil_img.save(class_dir / f"sample_{i:04d}.jpg", quality=85)

    print(f"✓ Generated {num_samples_per_class * len(CLASS_NAMES)} synthetic images")
    print(f"  Location: {output_path}")
    print(f"  Classes: {CLASS_NAMES}")
    return output_path

def main():
    parser = argparse.ArgumentParser(description="Train Sri Lankan Tea Grade Classification Model")
    parser.add_argument("--data_dir", default="datasets/grade", help="Dataset directory")
    parser.add_argument("--epochs", type=int, default=50, help="Number of epochs")
    parser.add_argument("--batch_size", type=int, default=32, help="Batch size")
    parser.add_argument("--lr", type=float, default=0.001, help="Learning rate")
    parser.add_argument("--generate_data", action="store_true", help="Generate synthetic dataset")
    parser.add_argument("--img_size", type=int, default=224, help="Image size")
    args = parser.parse_args()

    print("=" * 70)
    print("  TEAMIND - Sri Lankan Tea Grade Classification Training")
    print("=" * 70)
    print(f"  TensorFlow version: {tf.__version__}")
    print(f"  GPU available: {tf.config.list_physical_devices('GPU')}")
    print(f"  Image size: {args.img_size}x{args.img_size}")
    print(f"  Epochs: {args.epochs}")
    print(f"  Batch size: {args.batch_size}")
    print("=" * 70)

    # Generate synthetic data if requested or if no data exists
    data_path = Path(args.data_dir)
    if args.generate_data or not (data_path / "train").exists():
        generate_synthetic_dataset(args.data_dir)

    # Create data generators
    print("\n=== Loading Data ===")
    train_gen, val_gen = create_data_generators(args.data_dir, args.img_size, args.batch_size)
    print(f"  Training samples: {train_gen.samples}")
    print(f"  Validation samples: {val_gen.samples}")
    print(f"  Classes: {list(train_gen.class_indices.keys())}")

    # Create model
    print("\n=== Building Model ===")
    model = create_model(num_classes=len(CLASS_NAMES), img_size=args.img_size)
    model.summary()

    # Train
    print("\n=== Training ===")
    model, history = train_model(model, train_gen, val_gen, args.epochs, args.lr)

    # Evaluate
    metrics = evaluate_model(model, val_gen)

    # Save
    save_model(model)

    # Save training history
    history_path = Path("models") / "grade_history.json"
    with open(history_path, "w") as f:
        json.dump(history, f, indent=2)
    print(f"✓ Training history saved: {history_path}")

    print("\n" + "=" * 70)
    print("  Training Complete!")
    print("  Copy 'models/grade_model.h5' to 'python/models/'")
    print("=" * 70)

if __name__ == "__main__":
    main()