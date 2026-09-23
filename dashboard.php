<?php
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Dashboard';
$pageSubtitle = 'Welcome back, ' . ($_SESSION['user_name'] ?? 'User');

$userId = $_SESSION['user_id'];

// ===== YEAR FILTER - Calendar Year (Jan-Dec) =====
$currentYear = date('Y'); // 2026

// Get available years from actual data
$availableYears = fetchAll("SELECT DISTINCT YEAR(prediction_date) as year FROM yield_predictions WHERE user_id = ? ORDER BY year DESC", [$userId]);
$yearOptions = array_column($availableYears, 'year');

// If no data, default to current year
if (empty($yearOptions)) {
    $yearOptions = [$currentYear];
}

$selectedYear = isset($_GET['yield_year']) ? (int)$_GET['yield_year'] : $currentYear;
$selectedYear = in_array($selectedYear, $yearOptions) ? $selectedYear : $currentYear;

// Date range for selected year (Jan 1 - Dec 31)
$startDate = $selectedYear . '-01-01';
$endDate = $selectedYear . '-12-31';

// Fetch dashboard statistics
$totalPlantations = fetchOne("SELECT COUNT(*) as count FROM plantations WHERE user_id = ?", [$userId])['count'] ?? 0;

$latestDisease = fetchOne("SELECT disease_name, confidence, severity FROM disease_predictions WHERE user_id = ? ORDER BY prediction_date DESC LIMIT 1", [$userId]);
$diseaseRisk = $latestDisease ? ($latestDisease['severity'] === 'High' || $latestDisease['severity'] === 'Critical' ? 'High' : 'Low') : 'Low';

$latestYield = fetchOne("SELECT predicted_yield, confidence FROM yield_predictions WHERE user_id = ? ORDER BY prediction_date DESC LIMIT 1", [$userId]);
$predictedYield = $latestYield ? round($latestYield['predicted_yield']) : 0;

$latestDemand = fetchOne("SELECT forecasted_demand, growth_rate FROM demand_forecasts ORDER BY year DESC, month DESC LIMIT 1", []);
$marketDemand = $latestDemand ? ($latestDemand['growth_rate'] > 15 ? 'High' : 'Moderate') : 'Moderate';

// Fetch recent predictions
$recentPredictions = fetchAll("SELECT 'Disease Detection' as type, disease_name as details, CONCAT(confidence, '%') as result, prediction_date as date FROM disease_predictions WHERE user_id = ?
    UNION ALL
    SELECT 'Yield Prediction' as type, 'Kotagala Estate' as details, CONCAT(predicted_yield, ' kg') as result, prediction_date as date FROM yield_predictions WHERE user_id = ?
    UNION ALL
    SELECT 'Demand Forecast' as type, CONCAT('May ', year) as details, CONCAT(growth_rate, '%') as result, created_at as date FROM demand_forecasts
    UNION ALL
    SELECT 'Grade Classification' as type, CONCAT('Sample #', id) as details, grade as result, classification_date as date FROM tea_grade_classifications WHERE user_id = ?
    ORDER BY date DESC LIMIT 5", [$userId, $userId, $userId]);

// ===== CORRECTED YIELD TREND QUERY =====
// Group by month for the selected calendar year, show ALL 12 months
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
$yieldData = array_fill(0, 12, null); // null for missing months

foreach ($yieldTrend as $row) {
    $monthIndex = (int)$row['month_num'] - 1; // 0-based index
    $yieldData[$monthIndex] = round($row['yield_val']);
}

$hasRealData = !empty($yieldTrend);

// Disease distribution
$diseaseDist = fetchAll("SELECT disease_name, COUNT(*) as count FROM disease_predictions WHERE user_id = ? GROUP BY disease_name", [$userId]);

// Disease data for chart
$diseaseLabels = [];
$diseaseData = [];
$diseaseColors = ['#10b981', '#ef4444', '#f59e0b', '#6b7280'];
if (empty($diseaseDist)) {
    $diseaseLabels = ['Healthy', 'Blight', 'Brown Spot', 'Red Spot', 'Others'];
    $diseaseData = [62, 18, 10, 6, 4];
} else {
    foreach ($diseaseDist as $row) {
        $diseaseLabels[] = $row['disease_name'];
        $diseaseData[] = $row['count'];
    }
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/navbar.php';
?>

<div class="content-area">
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
    <div class="alert-custom alert-<?php echo $flash['type']; ?>-custom animate-fade-in">
        <i class="bi bi-check-circle"></i>
        <?php echo $flash['message']; ?>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card stat-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="stat-icon green">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $totalPlantations; ?></h3>
                    <div class="stat-label">Total Plantations</div>
                    <div class="stat-change up">
                        <i class="bi bi-arrow-up-short"></i> +8 this month
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card stat-card animate-fade-in" style="animation-delay: 0.2s;">
                <div class="stat-icon <?php echo $diseaseRisk === 'High' ? 'red' : 'green'; ?>">
                    <i class="bi bi-shield-<?php echo $diseaseRisk === 'High' ? 'exclamation' : 'check'; ?>"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $diseaseRisk; ?></h3>
                    <div class="stat-label">Disease Risk</div>
                    <div class="stat-change <?php echo $diseaseRisk === 'High' ? 'down' : 'up'; ?>">
                        <?php echo $diseaseRisk === 'High' ? '12% risk detected' : 'Low risk detected'; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card stat-card animate-fade-in" style="animation-delay: 0.3s;">
                <div class="stat-icon blue">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($predictedYield); ?> kg</h3>
                    <div class="stat-label">Predicted Yield</div>
                    <div class="stat-change up">
                        <i class="bi bi-arrow-up-short"></i> +12% vs last month
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card stat-card animate-fade-in" style="animation-delay: 0.4s;">
                <div class="stat-icon gold">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $marketDemand; ?></h3>
                    <div class="stat-label">Market Demand</div>
                    <div class="stat-change up">
                        <i class="bi bi-arrow-up-short"></i> +18% vs last month
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="dashboard-card animate-fade-in" style="animation-delay: 0.5s;">
                <div class="section-header">
                    <h3><i class="bi bi-graph-up" style="margin-right: 8px; color: var(--primary-green);"></i>Yield Trend</h3>
                    <!-- YEAR SELECTOR -->
                    <form method="get" action="" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                        <?php if (!$hasRealData): ?>
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
                
                <div style="height: 300px;">
                    <canvas id="yieldChart"></canvas>
                </div>
                
                <?php if (!$hasRealData): ?>
                <div style="margin-top: 8px; padding: 10px 12px; background: rgba(245,158,11,0.05); border-radius: 6px; border: 1px solid rgba(245,158,11,0.15);">
                    <p style="font-size: 12px; color: #92400e; margin: 0;">
                        <i class="bi bi-exclamation-triangle-fill" style="margin-right: 4px;"></i>
                        No yield predictions in <?php echo $selectedYear; ?>. 
                        <a href="yield.php" style="color: #92400e; text-decoration: underline; font-weight: 600;">Make a prediction</a> to see data here.
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

        <div class="col-lg-4">
            <div class="dashboard-card animate-fade-in" style="animation-delay: 0.6s;">
                <div class="section-header">
                    <h3><i class="bi bi-pie-chart" style="margin-right: 8px; color: var(--primary-green);"></i>Disease Distribution</h3>
                </div>
                <div style="height: 250px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="diseaseChart"></canvas>
                </div>
                <div class="mt-3">
                    <?php 
                    $totalDisease = array_sum($diseaseData);
                    foreach ($diseaseLabels as $i => $label): 
                        $pct = $totalDisease > 0 ? round(($diseaseData[$i] / $totalDisease) * 100) : 0;
                        $color = $diseaseColors[$i % count($diseaseColors)];
                    ?>
                    <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 12px;">
                        <span><span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: <?php echo $color; ?>; margin-right: 6px;"></span><?php echo $label; ?></span>
                        <span style="font-weight: 600;"><?php echo $pct; ?>%</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights & Recent Predictions -->
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="dashboard-card animate-fade-in" style="animation-delay: 0.7s;">
                <div class="section-header">
                    <h3><i class="bi bi-robot" style="margin-right: 8px; color: var(--primary-green);"></i>AI Insights & Recommendations</h3>
                </div>
                <div class="ai-insight">
                    <div class="ai-insight-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Blight disease risk is low in most plantations.</span>
                    </div>
                    <div class="ai-insight-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Predicted yield is expected to increase by 12% this month.</span>
                    </div>
                    <div class="ai-insight-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Market demand for CTC tea is increasing in May.</span>
                    </div>
                    <div class="ai-insight-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Recommended action: Regular field inspection in high humidity areas.</span>
                    </div>
                </div>
                <div style="margin-top: 16px; padding: 12px; background: linear-gradient(135deg, rgba(26,92,46,0.05) 0%, rgba(16,185,129,0.05) 100%); border-radius: 10px; border: 1px solid rgba(26,92,46,0.1);">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 60px; height: 60px; border-radius: 10px; background: linear-gradient(135deg, #1a5c2e 0%, #2d7a3e 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
    <i class="bi bi-tree-fill" style="font-size: 28px; color: #fff;"></i>
</div>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--primary-green);">Smart Monitoring Active</div>
                            <div style="font-size: 12px; color: var(--text-muted);">AI is analyzing your plantation data in real-time</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="dashboard-card animate-fade-in" style="animation-delay: 0.8s;">
                <div class="section-header">
                    <h3><i class="bi bi-clock-history" style="margin-right: 8px; color: var(--primary-green);"></i>Recent Predictions</h3>
                    <a href="reports.php">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Result</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentPredictions)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color: var(--text-muted);">
                                    <i class="bi bi-inbox" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                                    No predictions yet. Start using the AI modules!
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($recentPredictions as $pred): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $typeIcon = '';
                                    $typeColor = '';
                                    switch($pred['type']) {
                                        case 'Disease Detection': $typeIcon = 'shield-plus'; $typeColor = 'danger'; break;
                                        case 'Yield Prediction': $typeIcon = 'graph-up-arrow'; $typeColor = 'info'; break;
                                        case 'Demand Forecast': $typeIcon = 'bar-chart-line'; $typeColor = 'warning'; break;
                                        case 'Grade Classification': $typeIcon = 'award'; $typeColor = 'success'; break;
                                    }
                                    ?>
                                    <span class="badge-custom badge-<?php echo $typeColor; ?>" style="display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="bi bi-<?php echo $typeIcon; ?>"></i>
                                        <?php echo $pred['type']; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($pred['details']); ?></td>
                                <td><strong><?php echo htmlspecialchars($pred['result']); ?></strong></td>
                                <td style="color: var(--text-muted);"><?php echo formatDate($pred['date']); ?></td>
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
// Yield Trend Chart - Shows all 12 months, real data where available
const yieldCtx = document.getElementById('yieldChart').getContext('2d');
new Chart(yieldCtx, {
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
                // Only show points for months with data
                return context.raw !== null ? 5 : 0;
            },
            pointHoverRadius: function(context) {
                return context.raw !== null ? 7 : 0;
            },
            spanGaps: false // Don't connect null points
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

// Disease Distribution Chart
const diseaseCtx = document.getElementById('diseaseChart').getContext('2d');
new Chart(diseaseCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($diseaseLabels); ?>,
        datasets: [{
            data: <?php echo json_encode($diseaseData); ?>,
            backgroundColor: <?php echo json_encode(array_slice($diseaseColors, 0, count($diseaseLabels))); ?>,
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { display: false }
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>