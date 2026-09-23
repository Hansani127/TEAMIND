<?php
// Determine current page for active state
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon">
            <i class="bi bi-flower1"></i>
        </div>
        <div class="sidebar-brand-text">
            <h4>TEAMIND</h4>
            <span>AI Powered Tea Management</span>
        </div>
    </div>

    <div class="sidebar-menu">
        <div class="menu-label">Main Menu</div>

        <a href="dashboard.php" class="<?php echo $currentPage == 'dashboard' ? 'active' : ''; ?>">
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>

        <a href="disease.php" class="<?php echo $currentPage == 'disease' ? 'active' : ''; ?>">
            <i class="bi bi-shield-plus"></i>
            <span>Disease Detection</span>
        </a>

        <a href="yield.php" class="<?php echo $currentPage == 'yield' ? 'active' : ''; ?>">
            <i class="bi bi-graph-up-arrow"></i>
            <span>Yield Prediction</span>
        </a>

        <a href="demand.php" class="<?php echo $currentPage == 'demand' ? 'active' : ''; ?>">
            <i class="bi bi-bar-chart-line"></i>
            <span>Demand Forecast</span>
        </a>

        <a href="grade.php" class="<?php echo $currentPage == 'grade' ? 'active' : ''; ?>">
            <i class="bi bi-award"></i>
            <span>Tea Grading</span>
        </a>

        <a href="plantations.php" class="<?php echo $currentPage == 'plantations' ? 'active' : ''; ?>">
            <i class="bi bi-geo-alt"></i>
            <span>Plantations</span>
        </a>

        <a href="notifications.php" class="<?php echo $currentPage == 'notifications' ? 'active' : ''; ?>">
            <i class="bi bi-bell"></i>
            <span>Notifications</span>
        </a>

        <div class="menu-label">Management</div>

        <a href="reports.php" class="<?php echo $currentPage == 'reports' ? 'active' : ''; ?>">
            <i class="bi bi-file-earmark-text"></i>
            <span>Reports</span>
        </a>

        <a href="profile.php" class="<?php echo $currentPage == 'profile' ? 'active' : ''; ?>">
            <i class="bi bi-person"></i>
            <span>Profile</span>
        </a>

        <?php if (isAdmin()): ?>
        <a href="admin.php" class="<?php echo $currentPage == 'admin' ? 'active' : ''; ?>">
            <i class="bi bi-shield-lock"></i>
            <span>Admin Panel</span>
        </a>
        <a href="system_settings.php" class="<?php echo $currentPage == 'system_settings' ? 'active' : ''; ?>">
            <i class="bi bi-gear"></i>
            <span>System Settings</span>
        </a>
        <?php endif; ?>

        <a href="logout.php" style="margin-top: 8px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 18px;">
            <i class="bi bi-box-arrow-left"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>