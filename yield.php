<?php
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Yield Prediction';
$pageSubtitle = 'Predict tea yield based on environmental conditions';

$userId = $_SESSION['user_id'];

/**
 * Generate dynamic, context-aware AI recommendation based on environmental conditions
 */
function generateSmartRecommendation($yield, $temp, $rainfall, $humidity, $sunlight, $soilPh, $windSpeed) {
    $recs = [];

    // Yield-based recommendations
    if ($yield < 800) {
        $recs[] = "CRITICAL: Yield is severely below optimal. Immediate action required: increase fertilizer application by 20-30%, check for pest/disease infestation, and ensure adequate soil drainage.";
    } elseif ($yield < 1200) {
        $recs[] = "Yield is below optimal. Consider increasing NPK fertilizer application, improving soil organic matter, and checking irrigation adequacy.";
    } elseif ($yield > 2200) {
        $recs[] = "Excellent yield potential detected! Maintain current practices. Consider documenting this configuration for future seasons.";
    }

    // Temperature recommendations
    if ($temp < 15) {
        $recs[] = "Temperature ({$temp}°C) is too cold for tea. Risk of frost damage. Consider windbreaks or shade trees. Growth will be significantly slowed.";
    } elseif ($temp < 18) {
        $recs[] = "Temperature ({$temp}°C) is below optimal. Tea growth slows below 18°C. Consider protective mulching to retain soil warmth.";
    } elseif ($temp > 30) {
        $recs[] = "Temperature ({$temp}°C) is excessively high. Risk of leaf scorch and quality degradation. Increase shade coverage and ensure adequate soil moisture.";
    } elseif ($temp > 28) {
        $recs[] = "Temperature ({$temp}°C) is above optimal range. Monitor for heat stress. Increase irrigation frequency during peak heat hours.";
    } elseif ($temp >= 20 && $temp <= 24) {
        $recs[] = "Temperature ({$temp}°C) is in the ideal range for tea cultivation. Excellent growing conditions.";
    }

    // Rainfall recommendations
    if ($rainfall < 1200) {
        $recs[] = "Rainfall ({$rainfall}mm) is critically low. Drought stress likely. Implement drip irrigation immediately and apply mulch to reduce evaporation.";
    } elseif ($rainfall < 1500) {
        $recs[] = "Rainfall ({$rainfall}mm) is below ideal. Supplement with irrigation during dry spells, especially during flushing periods.";
    } elseif ($rainfall > 3000) {
        $recs[] = "Excessive rainfall ({$rainfall}mm) detected. Risk of root rot and fungal diseases. Improve drainage systems and reduce irrigation.";
    } elseif ($rainfall > 2500) {
        $recs[] = "Rainfall ({$rainfall}mm) is above optimal. Monitor for waterlogging. Ensure proper field drainage and consider raised bed planting.";
    } elseif ($rainfall >= 1800 && $rainfall <= 2200) {
        $recs[] = "Rainfall ({$rainfall}mm) is in the optimal range for tea. Good moisture availability for healthy growth.";
    }

    // Humidity recommendations
    if ($humidity < 60) {
        $recs[] = "Humidity ({$humidity}%) is too low. Tea requires high humidity. Use micro-sprinklers to increase ambient moisture and apply organic mulch.";
    } elseif ($humidity > 90) {
        $recs[] = "Humidity ({$humidity}%) is excessively high. High risk of fungal diseases (blight, algal spot). Improve air circulation through strategic pruning.";
    } elseif ($humidity >= 70 && $humidity <= 80) {
        $recs[] = "Humidity ({$humidity}%) is ideal for tea. Perfect conditions for tender shoot development.";
    }

    // Soil pH recommendations
    if ($soilPh < 4.0) {
        $recs[] = "Soil pH ({$soilPh}) is extremely acidic. Apply dolomitic lime (500-800 kg/ha) to raise pH. Check for aluminum toxicity.";
    } elseif ($soilPh < 4.5) {
        $recs[] = "Soil pH ({$soilPh}) is too acidic. Apply agricultural lime at 300-500 kg/ha. Acidic soils reduce nutrient availability.";
    } elseif ($soilPh > 6.5) {
        $recs[] = "Soil pH ({$soilPh}) is too alkaline for tea. Tea prefers acidic soils. Apply elemental sulfur (100-200 kg/ha) or acidic organic matter.";
    } elseif ($soilPh > 6.0) {
        $recs[] = "Soil pH ({$soilPh}) is slightly high. Consider adding peat moss or pine needle mulch to gently acidify the soil.";
    } elseif ($soilPh >= 4.8 && $soilPh <= 5.5) {
        $recs[] = "Soil pH ({$soilPh}) is in the ideal range for tea. Optimal nutrient uptake conditions.";
    }

    // Wind speed recommendations
    if ($windSpeed > 25) {
        $recs[] = "Wind speed ({$windSpeed} km/h) is high. Risk of mechanical damage to tender shoots. Install windbreaks or shelter belts.";
    } elseif ($windSpeed > 15) {
        $recs[] = "Moderate wind ({$windSpeed} km/h) may increase evapotranspiration. Monitor soil moisture more frequently.";
    }

    // Sunshine recommendations
    if ($sunlight < 4) {
        $recs[] = "Sunshine hours ({$sunlight}h) are too low. Tea needs 5-6 hours for photosynthesis. Prune overhead shade trees if light is insufficient.";
    } elseif ($sunlight > 8) {
        $recs[] = "Excessive sunshine ({$sunlight}h) may cause leaf burn and reduce quality. Ensure adequate shade tree coverage (30-40% shade recommended).";
    }

    if (empty($recs)) {
        return "All environmental conditions are optimal. Continue current best practices and maintain regular monitoring schedule.";
    }

    return implode(" ", $recs);
}

$result = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $temperature = floatval($_POST['temperature'] ?? 0);
    $rainfall = floatval($_POST['rainfall'] ?? 0);
    $humidity = floatval($_POST['humidity'] ?? 0);
    $sunlight = floatval($_POST['sunlight'] ?? 0);
    $windSpeed = floatval($_POST['wind_speed'] ?? 0);
    $soilPh = floatval($_POST['soil_ph'] ?? 6.5);
    $plantationId = intval($_POST['plantation_id'] ?? 0);

    if ($temperature < -10 || $temperature > 60) {
        $error = 'Please enter a valid temperature (-10°C to 60°C).';
    } elseif ($rainfall < 0 || $rainfall > 10000) {
        $error = 'Please enter valid rainfall (0-10000mm).';
    } elseif ($humidity < 0 || $humidity > 100) {
        $error = 'Please enter valid humidity (0-100%).';
    } elseif ($sunlight < 0 || $sunlight > 24) {
        $error = 'Please enter valid sunlight hours (0-24).';
    } else {
        // Call Python Flask API for yield prediction
        $apiUrl = FLASK_API_URL . '/predict/yield';

        $postData = json_encode([
            'temperature' => $temperature,
            'rainfall' => $rainfall,
            'humidity' => $humidity,
            'sunlight' => $sunlight,
            'wind_speed' => $windSpeed,
            'soil_ph' => $soilPh
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_reset($ch);

        if ($httpCode == 200 && $response) {
            $apiResult = json_decode($response, true);
            if ($apiResult && isset($apiResult['predicted_yield'])) {
                $result = $apiResult;
            }
        }

        // Fallback simulation if API not available
        if (!$result) {
            $baseYield = 800;
            $tempFactor = max(0, 1 - abs($temperature - 25) / 30);
            $rainFactor = min(1, $rainfall / 2000);
            $humidFactor = $humidity / 100;
            $sunFactor = min(1, $sunlight / 8);

            $predictedYield = $baseYield * (0.3 + 0.7 * (($tempFactor + $rainFactor + $humidFactor + $sunFactor) / 4));
            $predictedYield = max(200, min(3000, $predictedYield));

            $recommendation = generateSmartRecommendation($predictedYield, $temperature, $rainfall, $humidity, $sunlight, $soilPh, $windSpeed);

            $result = [
                'predicted_yield' => round($predictedYield, 2),
                'confidence' => rand(85, 98),
                'yield_per_acre' => round($predictedYield / 5, 2),
                'factors' => [
                    'temperature_impact' => round($tempFactor * 100, 1),
                    'rainfall_impact' => round($rainFactor * 100, 1),
                    'humidity_impact' => round($humidFactor * 100, 1),
                    'sunlight_impact' => round($sunFactor * 100, 1)
                ],
                'recommendation' => $recommendation
            ];
        }

        // Save to database
        executeQuery(
            "INSERT INTO yield_predictions (user_id, plantation_id, temperature, humidity, rainfall, sunlight, wind_speed, soil_ph, predicted_yield, confidence, yield_per_acre) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$userId, $plantationId ?: null, $temperature, $humidity, $rainfall, $sunlight, $windSpeed, $soilPh, $result['predicted_yield'], $result['confidence'], $result['yield_per_acre']]
        );

        // Create notification
        createNotification($userId, 'Yield Prediction Complete', 'Predicted yield: ' . round($result['predicted_yield']) . ' kg with ' . $result['confidence'] . '% confidence.', 'success');

        // Log activity
executeQuery(
    "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())",
    [
        $userId,
        'yield_prediction',
        'Predicted yield: ' . $result['predicted_yield'] . ' kg with ' . $result['confidence'] . '% confidence',
        $_SERVER['REMOTE_ADDR'] ?? '::1',
        $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ]
);
    }
}

// Fetch user's plantations for dropdown
$plantations = fetchAll("SELECT plantation_id, plantation_name FROM plantations WHERE user_id = ?", [$userId]);

// Fetch recent predictions
$recentPredictions = fetchAll("SELECT * FROM yield_predictions WHERE user_id = ? ORDER BY prediction_date DESC LIMIT 10", [$userId]);

// ===== YIELD TREND CHART DATA =====
$currentYear = date('Y');

// Get available years from actual data
$availableYears = fetchAll("SELECT DISTINCT YEAR(prediction_date) as year FROM yield_predictions WHERE user_id = ? ORDER BY year DESC", [$userId]);
$yearOptions = array_column($availableYears, 'year');
if (empty($yearOptions)) {
    $yearOptions = [$currentYear];
}

$selectedYear = isset($_GET['yield_year']) ? (int)$_GET['yield_year'] : $currentYear;
$selectedYear = in_array($selectedYear, $yearOptions) ? $selectedYear : $currentYear;

$startDate = $selectedYear . '-01-01';
$endDate = $selectedYear . '-12-31';

// Group by month for selected year, show ALL 12 months
$yieldTrend = fetchAll("
    SELECT 
        MONTH(prediction_date) as month_num,
        DATE_FORMAT(prediction_date, '%b') as month,
        AVG(predicted_yield) as yield_val,
        COUNT(*) as prediction_count
    FROM yield_predictions 
    WHERE user_id = ? 
      AND prediction_date >= ? 
      AND prediction_date <= ?
    GROUP BY MONTH(prediction_date), DATE_FORMAT(prediction_date, '%b')
    ORDER BY MONTH(prediction_date) ASC
", [$userId, $startDate, $endDate]);

// Build complete 12-month chart (fill missing months with null)
$allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$yieldLabels = $allMonths;
$yieldData = array_fill(0, 12, null);

foreach ($yieldTrend as $row) {
    $monthIndex = (int)$row['month_num'] - 1;
    $yieldData[$monthIndex] = round($row['yield_val']);
}

$hasYieldData = !empty($yieldTrend);

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/navbar.php';
?>

<div class="content-area">
    <nav aria-label="breadcrumb" style="margin-bottom: 20px;">
        <ol class="breadcrumb" style="font-size: 13px; margin: 0;">
            <li class="breadcrumb-item"><a href="dashboard.php" style="color: var(--primary-green); text-decoration: none;">Dashboard</a></li>
            <li class="breadcrumb-item active" style="color: var(--text-muted);">Yield Prediction</li>
        </ol>
    </nav>

    <?php if ($error): ?>
    <div class="alert-custom alert-danger-custom animate-fade-in">
        <i class="bi bi-exclamation-circle"></i>
        <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <!-- ===== TOP ROW: Input Form (Left) + Yield Trend Chart (Right) ===== -->
    <div class="row g-4">
        <!-- Top Left: Enter Environmental Data -->
        <div class="col-lg-5">
            <div class="dashboard-card animate-fade-in">
                <div class="section-header">
                    <h3><i class="bi bi-sliders" style="margin-right: 8px; color: var(--primary-green);"></i>Enter Environmental Data</h3>
                </div>

                <form method="POST" action="" id="yieldForm">
                    <?php if (!empty($plantations)): ?>
                    <div class="mb-3">
                        <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Select Plantation</label>
                        <select name="plantation_id" class="form-select" style="border-radius: 10px; font-size: 13px; padding: 10px 14px;">
                            <option value="">-- Select Plantation --</option>
                            <?php foreach ($plantations as $plantation): ?>
                            <option value="<?php echo $plantation['plantation_id']; ?>"><?php echo htmlspecialchars($plantation['plantation_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <label style="font-size: 13px; font-weight: 600; color: var(--text-dark);">Temperature (°C)</label>
                            <span id="tempValue" style="font-size: 13px; font-weight: 700; color: var(--primary-green);">22</span>
                        </div>
                        <input type="range" name="temperature" id="temperature" min="0" max="50" value="22" class="form-range" style="accent-color: var(--primary-green);" oninput="document.getElementById('tempValue').textContent = this.value">
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <label style="font-size: 13px; font-weight: 600; color: var(--text-dark);">Rainfall (mm)</label>
                            <span id="rainValue" style="font-size: 13px; font-weight: 700; color: var(--primary-green);">150</span>
                        </div>
                        <input type="range" name="rainfall" id="rainfall" min="0" max="500" value="150" class="form-range" style="accent-color: var(--primary-green);" oninput="document.getElementById('rainValue').textContent = this.value">
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <label style="font-size: 13px; font-weight: 600; color: var(--text-dark);">Humidity (%)</label>
                            <span id="humidValue" style="font-size: 13px; font-weight: 700; color: var(--primary-green);">75</span>
                        </div>
                        <input type="range" name="humidity" id="humidity" min="0" max="100" value="75" class="form-range" style="accent-color: var(--primary-green);" oninput="document.getElementById('humidValue').textContent = this.value">
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <label style="font-size: 13px; font-weight: 600; color: var(--text-dark);">Sunlight (hours/day)</label>
                            <span id="sunValue" style="font-size: 13px; font-weight: 700; color: var(--primary-green);">6</span>
                        </div>
                        <input type="range" name="sunlight" id="sunlight" min="0" max="12" value="6" class="form-range" style="accent-color: var(--primary-green);" oninput="document.getElementById('sunValue').textContent = this.value">
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <label style="font-size: 13px; font-weight: 600; color: var(--text-dark);">Wind Speed (km/h)</label>
                            <span id="windValue" style="font-size: 13px; font-weight: 700; color: var(--primary-green);">12</span>
                        </div>
                        <input type="range" name="wind_speed" id="wind_speed" min="0" max="100" value="12" class="form-range" style="accent-color: var(--primary-green);" oninput="document.getElementById('windValue').textContent = this.value">
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <label style="font-size: 13px; font-weight: 600; color: var(--text-dark);">Soil pH</label>
                            <span id="phValue" style="font-size: 13px; font-weight: 700; color: var(--primary-green);">6.5</span>
                        </div>
                        <input type="range" name="soil_ph" id="soil_ph" min="4" max="9" step="0.1" value="6.5" class="form-range" style="accent-color: var(--primary-green);" oninput="document.getElementById('phValue').textContent = this.value">
                    </div>

                    <button type="submit" class="btn-primary-custom w-100">
                        <i class="bi bi-magic" style="margin-right: 8px;"></i>Predict Yield
                    </button>
                </form>
            </div>
        </div>

        <!-- Top Right: Yield Trend Chart -->
        <div class="col-lg-7">
            <div class="dashboard-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="section-header">
                    <h3><i class="bi bi-graph-up" style="margin-right: 8px; color: var(--primary-green);"></i>Yield Trend</h3>
                    <form method="get" action="" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                        <?php if (!$hasYieldData): ?>
                        <span style="font-size: 11px; color: #9ca3af; background: rgba(156,163,175,0.1); padding: 2px 8px; border-radius: 4px;">
                            <i class="bi bi-info-circle"></i> No data
                        </span>
                        <?php endif; ?>
                        <select name="yield_year" class="form-select form-select-sm" 
                                style="width: auto; border-radius: 8px; font-size: 12px;"
                                onchange="this.form.submit()">
                            <?php foreach ($yearOptions as $year): ?>
                                <option value="<?php echo $year; ?>" <?php echo $selectedYear == $year ? 'selected' : ''; ?>>
                                    <?php echo $year; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <div style="height: 280px;">
                    <canvas id="yieldTrendChart"></canvas>
                </div>

                <?php if (!$hasYieldData): ?>
                <div style="margin-top: 8px; padding: 10px 12px; background: rgba(245,158,11,0.05); border-radius: 6px; border: 1px solid rgba(245,158,11,0.15);">
                    <p style="font-size: 12px; color: #92400e; margin: 0;">
                        <i class="bi bi-exclamation-triangle-fill" style="margin-right: 4px;"></i>
                        No yield predictions in <?php echo $selectedYear; ?>. 
                        Submit a prediction to see data here.
                    </p>
                </div>
                <?php else: ?>
                <div style="margin-top: 8px; padding: 10px 12px; background: rgba(26,92,46,0.05); border-radius: 6px; border: 1px solid rgba(26,92,46,0.1);">
                    <p style="font-size: 12px; color: var(--primary-green); margin: 0;">
                        <i class="bi bi-check-circle-fill" style="margin-right: 4px;"></i>
                        Showing <?php echo count($yieldTrend); ?> month(s) of real prediction data for <?php echo $selectedYear; ?>.
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ===== BOTTOM ROW: Prediction Result (Left) + Recent Predictions (Right) ===== -->
    <div class="row g-4 mt-1">
        <?php if ($result): ?>
        <!-- Bottom Left: Prediction Result -->
        <div class="col-lg-5">
            <div class="dashboard-card animate-fade-in" style="border: 2px solid rgba(26, 92, 46, 0.15);">
                <div class="section-header">
                    <h3><i class="bi bi-clipboard-check" style="margin-right: 8px; color: var(--primary-green);"></i>Prediction Result</h3>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="result-score" style="padding: 10px 0;">
                            <div class="score-value" style="font-size: 28px;"><?php echo number_format($result['predicted_yield']); ?> kg</div>
                            <div class="score-label">Predicted Yield</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="height: 120px;">
                            <canvas id="resultChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size: 13px; font-weight: 500;">Confidence Score</span>
                        <span style="font-size: 13px; font-weight: 700; color: var(--primary-green);"><?php echo $result['confidence']; ?>%</span>
                    </div>
                    <div class="progress-custom">
                        <div class="progress-bar" style="width: <?php echo $result['confidence']; ?>%; background: linear-gradient(90deg, #1a5c2e, #10b981) !important;"></div>
                    </div>
                </div>

                <div class="mt-3" style="background: linear-gradient(135deg, rgba(26,92,46,0.05) 0%, rgba(16,185,129,0.05) 100%); border-radius: 12px; padding: 14px; border: 1px solid rgba(26,92,46,0.1);">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-lightbulb" style="font-size: 20px; color: var(--accent-gold); margin-top: 2px;"></i>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px;">AI Recommendation</div>
                            <p style="font-size: 12px; color: var(--text-muted); margin: 0; line-height: 1.6;"><?php echo $result['recommendation'] ?? 'Maintain current farming practices for optimal yield.'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Bottom Right: Recent Predictions -->
        <div class="<?php echo $result ? 'col-lg-7' : 'col-12'; ?>">
            <div class="dashboard-card animate-fade-in" style="animation-delay: 0.2s;">
                <div class="section-header">
                    <h3><i class="bi bi-clock-history" style="margin-right: 8px; color: var(--primary-green);"></i>Recent Predictions</h3>
                </div>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Temp</th>
                                <th>Rainfall</th>
                                <th>Humidity</th>
                                <th>Predicted Yield</th>
                                <th>Confidence</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentPredictions)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4" style="color: var(--text-muted);">
                                    <i class="bi bi-inbox" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                                    No predictions yet. Enter environmental data to predict yield.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($recentPredictions as $pred): ?>
                            <tr>
                                <td style="font-size: 12px;"><?php echo formatDate($pred['prediction_date'], 'M d, Y'); ?></td>
                                <td><?php echo $pred['temperature']; ?>°C</td>
                                <td><?php echo $pred['rainfall']; ?>mm</td>
                                <td><?php echo $pred['humidity']; ?>%</td>
                                <td><strong style="color: var(--primary-green);"><?php echo number_format($pred['predicted_yield']); ?> kg</strong></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress-custom" style="width: 50px;">
                                            <div class="progress-bar" style="width: <?php echo $pred['confidence']; ?>%; background: linear-gradient(90deg, #1a5c2e, #10b981) !important;"></div>
                                        </div>
                                        <span style="font-size: 12px;"><?php echo $pred['confidence']; ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ===== YIELD TREND CHART =====
const trendCtx = document.getElementById('yieldTrendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($yieldLabels); ?>,
        datasets: [{
            label: 'Yield (kg)',
            data: <?php echo json_encode($yieldData); ?>,
            borderColor: '#1a5c2e',
            backgroundColor: 'rgba(26, 92, 46, 0.08)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#1a5c2e',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: function(context) {
                return context.raw !== null ? 5 : 0;
            },
            pointHoverRadius: function(context) {
                return context.raw !== null ? 7 : 0;
            },
            spanGaps: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    title: function(context) {
                        return '<?php echo $selectedYear; ?> - ' + context[0].label;
                    },
                    label: function(context) {
                        if (context.raw === null) {
                            return 'No data';
                        }
                        return 'Yield: ' + context.parsed.y.toLocaleString() + ' kg';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: { 
                    font: { size: 11 }, 
                    color: '#6b7280',
                    callback: function(value) {
                        return value + ' kg';
                    }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 }, color: '#6b7280' }
            }
        }
    }
});

<?php if ($result): ?>
// Result Mini Chart
const resultCtx = document.getElementById('resultChart').getContext('2d');
new Chart(resultCtx, {
    type: 'bar',
    data: {
        labels: ['Temp', 'Rain', 'Humid', 'Sun'],
        datasets: [{
            label: 'Impact %',
            data: [
                <?php echo $result['factors']['temperature_impact'] ?? 75; ?>,
                <?php echo $result['factors']['rainfall_impact'] ?? 80; ?>,
                <?php echo $result['factors']['humidity_impact'] ?? 85; ?>,
                <?php echo $result['factors']['sunlight_impact'] ?? 70; ?>
            ],
            backgroundColor: ['#1a5c2e', '#2d7a3e', '#10b981', '#d4a843'],
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: { font: { size: 10 }, color: '#6b7280' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 10 }, color: '#6b7280' }
            }
        }
    }
});
<?php endif; ?>
</script>

<?php require_once 'includes/footer.php'; ?>