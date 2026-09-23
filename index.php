<?php
require_once 'config/config.php';

// Redirect to dashboard if logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TEAMIND - AI Powered Tea Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #1a5c2e;
            --primary-green-light: #2d7a3e;
            --primary-green-dark: #0d3d1a;
            --accent-gold: #d4a843;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }

        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d3d1a 0%, #1a5c2e 50%, #2d7a3e 100%);
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://images.unsplash.com/photo-1563911892437-1feda0179e1b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover;
            opacity: 0.15;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(ellipse at 30% 50%, transparent 0%, rgba(13, 61, 26, 0.7) 100%);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .hero-logo-icon {
            width: 56px;
            height: 56px;
            background: rgba(255,255,255,0.15);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .hero-logo-icon i { font-size: 28px; color: #fff; }

        .hero-logo-text h1 {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            margin: 0;
            letter-spacing: 1px;
        }

        .hero-logo-text span {
            font-size: 12px;
            color: rgba(255,255,255,0.7);
            font-weight: 500;
        }

        .hero-title {
            font-size: 56px;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .hero-title span {
            background: linear-gradient(135deg, #d4a843 0%, #e8c76a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 18px;
            color: rgba(255,255,255,0.8);
            line-height: 1.7;
            margin-bottom: 36px;
            max-width: 500px;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            padding: 14px 32px;
            background: linear-gradient(135deg, #d4a843 0%, #e8c76a 100%);
            color: #0d3d1a;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            box-shadow: 0 8px 30px rgba(212, 168, 67, 0.3);
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(212, 168, 67, 0.4);
            color: #0d3d1a;
        }

        .btn-hero-outline {
            padding: 14px 32px;
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-hero-outline:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.5);
            color: #fff;
        }

        .features-section {
            padding: 80px 0;
            background: #f8faf9;
        }

        .feature-card {
            background: #fff;
            border-radius: 20px;
            padding: 36px 28px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .feature-icon.green { background: rgba(26, 92, 46, 0.1); color: var(--primary-green); }
        .feature-icon.blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .feature-icon.gold { background: rgba(212, 168, 67, 0.1); color: var(--accent-gold); }
        .feature-icon.red { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        .feature-card h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 10px;
        }

        .feature-card p {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin: 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 48px;
        }

        .section-title h2 {
            font-size: 36px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 12px;
        }

        .section-title p {
            font-size: 16px;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
        }

        .tech-stack {
            padding: 60px 0;
            background: #fff;
            border-top: 1px solid #e5e7eb;
        }

        .tech-item {
            text-align: center;
            padding: 20px;
        }

        .tech-item i {
            font-size: 32px;
            color: var(--primary-green);
            margin-bottom: 8px;
        }

        .tech-item h4 {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
            margin: 0;
        }

        .tech-item p {
            font-size: 12px;
            color: #6b7280;
            margin: 4px 0 0 0;
        }

        .footer {
            background: #0d3d1a;
            padding: 40px 0;
            text-align: center;
        }

        .footer p {
            color: rgba(255,255,255,0.6);
            font-size: 14px;
            margin: 0;
        }

        .footer a {
            color: #d4a843;
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 36px; }
            .hero-subtitle { font-size: 16px; }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 hero-content">
                    <div class="hero-logo">
                        <div class="hero-logo-icon">
                            <i class="bi bi-flower1"></i>
                        </div>
                        <div class="hero-logo-text">
                            <h1>TEAMIND</h1>
                            <span>AI Powered Tea Management</span>
                        </div>
                    </div>

                    <h2 class="hero-title">
                        Smart Decisions for<br>
                        <span>Tea Plantations</span>
                    </h2>

                    <p class="hero-subtitle">
                        AI-Based Smart Decision Support System for Tea Plantation Management. 
                        Detect diseases, predict yields, forecast demand, and grade tea quality — all in one platform.
                    </p>

                    <div class="hero-buttons">
                        <a href="register.php" class="btn-hero-primary">
                            <i class="bi bi-rocket-takeoff"></i>
                            Get Started
                        </a>
                        <a href="login.php" class="btn-hero-outline">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Sign In
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 d-none d-lg-block">
                    <div style="position: relative;">
                        <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(20px); border-radius: 24px; padding: 30px; border: 1px solid rgba(255,255,255,0.15);">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                                <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-shield-check" style="font-size: 22px; color: #fff;"></i>
                                </div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600; color: #fff;">Disease Detection</div>
                                    <div style="font-size: 11px; color: rgba(255,255,255,0.7);">7 Tea Leaf Conditions</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                                <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-graph-up-arrow" style="font-size: 22px; color: #fff;"></i>
                                </div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600; color: #fff;">Yield Prediction</div>
                                    <div style="font-size: 11px; color: rgba(255,255,255,0.7);">Environmental-Based Prediction</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                                <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-bar-chart-line" style="font-size: 22px; color: #fff;"></i>
                                </div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600; color: #fff;">Demand Forecast</div>
                                    <div style="font-size: 11px; color: rgba(255,255,255,0.7);">Market Trend Forecasting</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-award" style="font-size: 22px; color: #fff;"></i>
                                </div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600; color: #fff;">Tea Grading</div>
                                    <div style="font-size: 11px; color: rgba(255,255,255,0.7);">34 Tea Grade Categories</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Powerful AI Features</h2>
                <p>Everything you need to manage your tea plantation with the power of artificial intelligence</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon green">
                            <i class="bi bi-shield-plus"></i>
                        </div>
                        <h3>Disease Detection</h3>
                        <p>Upload a tea leaf image and our AI model analyses it to identify the detected leaf condition from seven supported classes.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon blue">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3>Yield Prediction</h3>
                        <p>Predict tea yield based on environmental factors like temperature, rainfall, humidity, and sunlight using ML models.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon gold">
                            <i class="bi bi-bar-chart-line"></i>
                        </div>
                        <h3>Demand Forecast</h3>
                        <p>Forecast market demand trends using historical data and AI to optimize production and maximize revenue.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon red">
                            <i class="bi bi-award"></i>
                        </div>
                        <h3>Tea Grading</h3>
                        <p>Automatically classify tea quality grades from sample images using computer vision and deep learning.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tech Stack -->
    <section class="tech-stack">
        <div class="container">
            <div class="section-title">
                <h2>Built With Modern Technology</h2>
                <p>Powered by industry-standard tools and frameworks</p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="tech-item">
                        <i class="bi bi-filetype-php"></i>
                        <h4>PHP 8</h4>
                        <p>Backend</p>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="tech-item">
                        <i class="bi bi-database"></i>
                        <h4>MySQL</h4>
                        <p>Database</p>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="tech-item">
                        <i class="bi bi-filetype-py"></i>
                        <h4>Python</h4>
                        <p>AI Models</p>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="tech-item">
                        <i class="bi bi-cpu"></i>
                        <h4>TensorFlow</h4>
                        <p>Deep Learning</p>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="tech-item">
                        <i class="bi bi-bootstrap"></i>
                        <h4>Bootstrap 5</h4>
                        <p>Frontend</p>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="tech-item">
                        <i class="bi bi-graph-up"></i>
                        <h4>Chart.js</h4>
                        <p>Visualization</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>
                <i class="bi bi-flower1" style="margin-right: 8px; color: #d4a843;"></i>
                TEAMIND - AI Based Smart Decision Support System for Tea Plantation Management
            </p>
            <p style="margin-top: 8px; font-size: 12px;">
                Developed by S.P.H.T. Premathilaka | University of Bedfordshire | 2026
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
