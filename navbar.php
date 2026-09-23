<?php
$user = getCurrentUser();
$unreadCount = isLoggedIn() ? getUnreadCount($_SESSION['user_id']) : 0;
?>

<!-- Top Navbar -->
<nav class="top-navbar">
    <div class="page-title">
        <h2><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h2>
        <p><?php echo isset($pageSubtitle) ? $pageSubtitle : 'Welcome back, ' . ($user['fullname'] ?? 'User'); ?></p>
    </div>

    <div class="navbar-actions">
        <button class="btn-icon" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>

        <button class="btn-icon" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <?php if ($unreadCount > 0): ?>
            <span class="badge"><?php echo $unreadCount; ?></span>
            <?php endif; ?>
        </button>

        <div class="dropdown-menu dropdown-menu-end" style="border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <div class="dropdown-header" style="padding: 12px 16px; font-weight: 600; font-size: 13px; border-bottom: 1px solid var(--border-color);">
                Notifications
            </div>
            <?php
            if (isLoggedIn()) {
                $notifications = fetchAll("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5", [$_SESSION['user_id']]);
                if (empty($notifications)) {
                    echo '<div class="dropdown-item text-center" style="padding: 20px; color: var(--text-muted); font-size: 13px;">No notifications</div>';
                } else {
                    foreach ($notifications as $notif) {
                        $icon = $notif['type'] == 'success' ? 'bi-check-circle' : ($notif['type'] == 'warning' ? 'bi-exclamation-triangle' : ($notif['type'] == 'danger' ? 'bi-x-circle' : 'bi-info-circle'));
                        echo '<div class="dropdown-item" style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); font-size: 13px;">
                            <i class="bi ' . $icon . '" style="margin-right: 8px;"></i>
                            <strong>' . htmlspecialchars($notif['title']) . '</strong><br>
                            <span style="color: var(--text-muted);">' . htmlspecialchars($notif['message']) . '</span>
                        </div>';
                    }
                }
            }
            ?>
            <div class="dropdown-footer text-center" style="padding: 10px;">
                <a href="notifications.php" style="font-size: 12px; color: var(--primary-green); text-decoration: none; font-weight: 600;">View All</a>
            </div>
        </div>

        <div class="user-dropdown dropdown-toggle" data-bs-toggle="dropdown">
            <img src="assets/images/default-avatar.png" alt="User" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($user['fullname'] ?? 'User'); ?>&background=1a5c2e&color=fff'">
            <div class="user-info d-none d-md-block">
                <div class="name"><?php echo htmlspecialchars($user['fullname'] ?? 'User'); ?></div>
                <div class="role"><?php echo ucfirst($user['role'] ?? 'Farmer'); ?></div>
            </div>
            <i class="bi bi-chevron-down" style="font-size: 12px; color: var(--text-muted);"></i>
        </div>

        <div class="dropdown-menu dropdown-menu-end" style="border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 10px 40px rgba(0,0,0,0.1); min-width: 200px;">
            <a class="dropdown-item" href="profile.php" style="padding: 10px 16px; font-size: 13px;">
                <i class="bi bi-person" style="margin-right: 8px;"></i> Profile
            </a>
            <a class="dropdown-item" href="settings.php" style="padding: 10px 16px; font-size: 13px;">
                <i class="bi bi-gear" style="margin-right: 8px;"></i> Settings
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="logout.php" style="padding: 10px 16px; font-size: 13px;">
                <i class="bi bi-box-arrow-left" style="margin-right: 8px;"></i> Logout
            </a>
        </div>
    </div>
</nav>