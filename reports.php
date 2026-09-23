<?php
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Reports';
$pageSubtitle = 'Generate and download system reports';

$userId = $_SESSION['user_id'];

$reportType = $_GET['type'] ?? 'all';

// Fetch data based on report type
$diseaseData = fetchAll("SELECT dp.*, p.plantation_name FROM disease_predictions dp LEFT JOIN plantations p ON dp.plantation_id = p.plantation_id WHERE dp.user_id = ? ORDER BY dp.prediction_date DESC", [$userId]);
$yieldData = fetchAll("SELECT yp.*, p.plantation_name FROM yield_predictions yp LEFT JOIN plantations p ON yp.plantation_id = p.plantation_id WHERE yp.user_id = ? ORDER BY yp.prediction_date DESC", [$userId]);
$gradeData = fetchAll("SELECT * FROM tea_grade_classifications WHERE user_id = ? ORDER BY classification_date DESC", [$userId]);

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/navbar.php';
?>

<div class="content-area">
    <nav aria-label="breadcrumb" style="margin-bottom: 20px;">
        <ol class="breadcrumb" style="font-size: 13px; margin: 0;">
            <li class="breadcrumb-item"><a href="dashboard.php" style="color: var(--primary-green); text-decoration: none;">Dashboard</a></li>
            <li class="breadcrumb-item active" style="color: var(--text-muted);">Reports</li>
        </ol>
    </nav>

    <!-- Report Type Tabs -->
    <div class="dashboard-card animate-fade-in mb-4">
        <div class="d-flex gap-2 flex-wrap">
            <a href="reports.php?type=all" class="btn <?php echo $reportType === 'all' ? 'btn-primary-custom' : 'btn-outline-custom'; ?>" style="padding: 8px 20px; font-size: 13px;">
                <i class="bi bi-grid" style="margin-right: 6px;"></i>All Reports
            </a>
            <a href="reports.php?type=disease" class="btn <?php echo $reportType === 'disease' ? 'btn-primary-custom' : 'btn-outline-custom'; ?>" style="padding: 8px 20px; font-size: 13px;">
                <i class="bi bi-shield-plus" style="margin-right: 6px;"></i>Disease
            </a>
            <a href="reports.php?type=yield" class="btn <?php echo $reportType === 'yield' ? 'btn-primary-custom' : 'btn-outline-custom'; ?>" style="padding: 8px 20px; font-size: 13px;">
                <i class="bi bi-graph-up-arrow" style="margin-right: 6px;"></i>Yield
            </a>
            <a href="reports.php?type=grade" class="btn <?php echo $reportType === 'grade' ? 'btn-primary-custom' : 'btn-outline-custom'; ?>" style="padding: 8px 20px; font-size: 13px;">
                <i class="bi bi-award" style="margin-right: 6px;"></i>Grade
            </a>
            <a href="reports.php?type=demand" class="btn <?php echo $reportType === 'demand' ? 'btn-primary-custom' : 'btn-outline-custom'; ?>" style="padding: 8px 20px; font-size: 13px;">
                <i class="bi bi-bar-chart-line" style="margin-right: 6px;"></i>Demand
            </a>
        </div>
    </div>

    <?php if ($reportType === 'all' || $reportType === 'disease'): ?>
    <!-- Disease Reports -->
    <div class="dashboard-card animate-fade-in mb-4">
        <div class="section-header">
            <h3><i class="bi bi-shield-plus" style="margin-right: 8px; color: var(--primary-green);"></i>Disease Detection Reports</h3>
            <button class="btn-primary-custom" style="padding: 8px 16px; font-size: 12px;" onclick="downloadCSV('disease')">
                <i class="bi bi-download" style="margin-right: 6px;"></i>Export CSV
            </button>
        </div>
        <div class="table-responsive">
            <table class="custom-table" id="diseaseTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Plantation</th>
                        <th>Disease</th>
                        <th>Confidence</th>
                        <th>Severity</th>
                        <th>Recommendation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($diseaseData)): ?>
                    <tr><td colspan="6" class="text-center py-4" style="color: var(--text-muted);">No disease detection data available.</td></tr>
                    <?php else: ?>
                    <?php foreach ($diseaseData as $d): ?>
                    <tr>
                        <td style="font-size: 12px;"><?php echo formatDate($d['prediction_date'], 'M d, Y H:i'); ?></td>
                        <td><?php echo htmlspecialchars($d['plantation_name'] ?? 'N/A'); ?></td>
                        <td><strong><?php echo htmlspecialchars($d['disease_name']); ?></strong></td>
                        <td><?php echo $d['confidence']; ?>%</td>
                        <td><span class="badge-custom badge-<?php echo $d['severity'] === 'Critical' ? 'danger' : ($d['severity'] === 'High' ? 'warning' : ($d['severity'] === 'Moderate' ? 'info' : 'success')); ?>"><?php echo $d['severity']; ?></span></td>
                        <td style="font-size: 12px; max-width: 300px;" class="text-truncate"><?php echo htmlspecialchars($d['recommendation'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($reportType === 'all' || $reportType === 'yield'): ?>
    <!-- Yield Reports -->
    <div class="dashboard-card animate-fade-in mb-4">
        <div class="section-header">
            <h3><i class="bi bi-graph-up-arrow" style="margin-right: 8px; color: var(--primary-green);"></i>Yield Prediction Reports</h3>
            <button class="btn-primary-custom" style="padding: 8px 16px; font-size: 12px;" onclick="downloadCSV('yield')">
                <i class="bi bi-download" style="margin-right: 6px;"></i>Export CSV
            </button>
        </div>
        <div class="table-responsive">
            <table class="custom-table" id="yieldTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Plantation</th>
                        <th>Temp</th>
                        <th>Humidity</th>
                        <th>Rainfall</th>
                        <th>Sunlight</th>
                        <th>Predicted Yield</th>
                        <th>Confidence</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($yieldData)): ?>
                    <tr><td colspan="8" class="text-center py-4" style="color: var(--text-muted);">No yield prediction data available.</td></tr>
                    <?php else: ?>
                    <?php foreach ($yieldData as $y): ?>
                    <tr>
                        <td style="font-size: 12px;"><?php echo formatDate($y['prediction_date'], 'M d, Y'); ?></td>
                        <td><?php echo htmlspecialchars($y['plantation_name'] ?? 'N/A'); ?></td>
                        <td><?php echo $y['temperature']; ?>°C</td>
                        <td><?php echo $y['humidity']; ?>%</td>
                        <td><?php echo $y['rainfall']; ?>mm</td>
                        <td><?php echo $y['sunlight']; ?>h</td>
                        <td><strong style="color: var(--primary-green);"><?php echo number_format($y['predicted_yield']); ?> kg</strong></td>
                        <td><?php echo $y['confidence']; ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($reportType === 'all' || $reportType === 'grade'): ?>
    <!-- Grade Reports -->
    <div class="dashboard-card animate-fade-in mb-4">
        <div class="section-header">
            <h3><i class="bi bi-award" style="margin-right: 8px; color: var(--primary-green);"></i>Tea Grade Classification Reports</h3>
            <button class="btn-primary-custom" style="padding: 8px 16px; font-size: 12px;" onclick="downloadCSV('grade')">
                <i class="bi bi-download" style="margin-right: 6px;"></i>Export CSV
            </button>
        </div>
        <div class="table-responsive">
            <table class="custom-table" id="gradeTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Grade</th>
                        <th>Label</th>
                        <th>Overall Score</th>
                        <th>Appearance</th>
                        <th>Aroma</th>
                        <th>Taste</th>
                        <th>Texture</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($gradeData)): ?>
                    <tr><td colspan="8" class="text-center py-4" style="color: var(--text-muted);">No grade classification data available.</td></tr>
                    <?php else: ?>
                    <?php foreach ($gradeData as $g): ?>
                    <tr>
                        <td style="font-size: 12px;"><?php echo formatDate($g['classification_date'], 'M d, Y'); ?></td>
                        <td><strong style="color: var(--primary-green); font-size: 16px;"><?php echo $g['grade']; ?></strong></td>
                        <td><?php echo $g['grade_label']; ?></td>
                        <td><strong><?php echo $g['overall_score']; ?>%</strong></td>
                        <td><?php echo $g['appearance_score']; ?>%</td>
                        <td><?php echo $g['aroma_score']; ?>%</td>
                        <td><?php echo $g['taste_score']; ?>%</td>
                        <td><?php echo $g['leaf_texture_score']; ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($reportType === 'all' || $reportType === 'demand'): ?>
    <!-- Demand Reports -->
    <div class="dashboard-card animate-fade-in mb-4">
        <div class="section-header">
            <h3><i class="bi bi-bar-chart-line" style="margin-right: 8px; color: var(--primary-green);"></i>Demand Forecast Reports</h3>
            <button class="btn-primary-custom" style="padding: 8px 16px; font-size: 12px;" onclick="downloadCSV('demand')">
                <i class="bi bi-download" style="margin-right: 6px;"></i>Export CSV
            </button>
        </div>
        <div class="table-responsive">
            <table class="custom-table" id="demandTable">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Region</th>
                        <th>Tea Type</th>
                        <th>Historical Demand</th>
                        <th>Forecasted Demand</th>
                        <th>Growth Rate</th>
                        <th>Revenue Forecast</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $demandData = fetchAll("SELECT * FROM demand_forecasts ORDER BY year ASC, month ASC");
                    $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    if (empty($demandData)): ?>
                    <tr><td colspan="8" class="text-center py-4" style="color: var(--text-muted);">No demand forecast data available.</td></tr>
                    <?php else: ?>
                    <?php foreach ($demandData as $d): ?>
                    <tr>
                        <td><?php echo $monthNames[$d['month']]; ?></td>
                        <td><?php echo $d['year']; ?></td>
                        <td><?php echo $d['region']; ?></td>
                        <td><?php echo $d['tea_type']; ?></td>
                        <td><?php echo number_format($d['historical_demand'] ?? 0); ?> MT</td>
                        <td><strong style="color: var(--primary-green);"><?php echo number_format($d['forecasted_demand']); ?> MT</strong></td>
                        <td><span class="badge-custom badge-success">+<?php echo $d['growth_rate']; ?>%</span></td>
                        <td>$<?php echo number_format($d['revenue_forecast'] ?? 0); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function downloadCSV(type) {
    const table = document.getElementById(type + 'Table');
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');

    rows.forEach(row => {
        let cols = row.querySelectorAll('td, th');
        let rowData = [];
        cols.forEach(col => {
            let text = col.innerText.replace(/"/g, '""').trim();
            rowData.push('"' + text + '"');
        });
        csv.push(rowData.join(','));
    });

    const csvContent = '﻿' + csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'teamind_' + type + '_report_' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php require_once 'includes/footer.php'; ?>
