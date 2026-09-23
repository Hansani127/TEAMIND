<?php
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Notifications';
$pageSubtitle = 'All your notifications';

$userId = $_SESSION['user_id'];

// Handle mark all read
if (isset($_GET['action']) && $_GET['action'] === 'mark_all_read') {
    executeQuery("UPDATE notifications SET status = 'read' WHERE user_id = ?", [$userId]);
    redirect('notifications.php', 'All notifications marked as read.', 'success');
}

// Handle mark single read
if (isset($_GET['action']) && $_GET['action'] === 'mark_read' && isset($_GET['id'])) {
    $notifId = intval($_GET['id']);
    executeQuery("UPDATE notifications SET status = 'read' WHERE id = ? AND user_id = ?", [$notifId, $userId]);
    redirect('notifications.php', 'Notification marked as read.', 'success');
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $notifId = intval($_GET['id']);
    executeQuery("DELETE FROM notifications WHERE id = ? AND user_id = ?", [$notifId, $userId]);
    redirect('notifications.php', 'Notification deleted.', 'success');
}

// Fetch all notifications
$notifications = fetchAll(
    "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC",
    [$userId]
);

// Count unread
$unreadResult = fetchOne(
    "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND status = 'unread'",
    [$userId]
);
$unreadCount = $unreadResult ? ($unreadResult['count'] ?? 0) : 0;

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/navbar.php';
?>

<div class="content-area">
    <nav aria-label="breadcrumb" style="margin-bottom: 20px;">
        <ol class="breadcrumb" style="font-size: 13px; margin: 0;">
            <li class="breadcrumb-item"><a href="dashboard.php" style="color: var(--primary-green); text-decoration: none;">Dashboard</a></li>
            <li class="breadcrumb-item active" style="color: var(--text-muted);">Notifications</li>
        </ol>
    </nav>

    <div class="dashboard-card animate-fade-in">
        <div class="section-header">
            <h3><i class="bi bi-bell" style="margin-right: 8px; color: var(--primary-green);"></i>Notifications</h3>
            <div class="d-flex gap-2">
                <?php if ($unreadCount > 0) { ?>
                <a href="notifications.php?action=mark_all_read" class="btn-outline-custom" style="padding: 8px 16px; font-size: 12px;">
                    <i class="bi bi-check-all" style="margin-right: 4px;"></i>Mark All Read
                </a>
                <?php } ?>
                <span class="badge-custom badge-info" style="font-size: 12px; padding: 8px 14px;">
                    <?php echo $unreadCount; ?> Unread
                </span>
            </div>
        </div>

        <?php if (empty($notifications)) { ?>
        <div class="text-center py-5" style="color: var(--text-muted);">
            <i class="bi bi-bell-slash" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
            <h5>No notifications yet</h5>
            <p style="font-size: 13px;">You will see notifications here when you use the AI features.</p>
        </div>
        <?php } else { ?>
        <div class="notification-list">
            <?php foreach ($notifications as $notif) { 
                $icon = 'bi-info-circle-fill';
                $color = '#3b82f6';
                if ($notif['type'] == 'success') { $icon = 'bi-check-circle-fill'; $color = '#10b981'; }
                elseif ($notif['type'] == 'warning') { $icon = 'bi-exclamation-triangle-fill'; $color = '#f59e0b'; }
                elseif ($notif['type'] == 'danger') { $icon = 'bi-x-circle-fill'; $color = '#ef4444'; }

                $bgClass = $notif['status'] === 'unread' ? 'unread' : '';

                $link = 'dashboard.php';
                if (strpos($notif['title'], 'Disease') !== false) $link = 'disease.php';
                elseif (strpos($notif['title'], 'Yield') !== false) $link = 'yield.php';
                elseif (strpos($notif['title'], 'Grade') !== false) $link = 'grade.php';
                elseif (strpos($notif['title'], 'Demand') !== false) $link = 'demand.php';
            ?>
            <div class="notification-item <?php echo $bgClass; ?>" style="padding: 16px; border-bottom: 1px solid var(--border-color); display: flex; align-items: flex-start; gap: 14px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: <?php echo $color; ?>15; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="bi <?php echo $icon; ?>" style="color: <?php echo $color; ?>; font-size: 18px;"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div class="d-flex justify-content-between align-items-start">
                        <a href="<?php echo $link; ?>" style="text-decoration: none; color: inherit; flex: 1;">
                            <div style="font-weight: 600; font-size: 14px; color: var(--text-dark); margin-bottom: 4px;">
                                <?php echo htmlspecialchars($notif['title']); ?>
                                <?php if ($notif['status'] === 'unread') { ?>
                                <span style="display: inline-block; width: 8px; height: 8px; background: var(--primary-green); border-radius: 50%; margin-left: 8px;"></span>
                                <?php } ?>
                            </div>
                            <div style="font-size: 13px; color: var(--text-muted); line-height: 1.5;">
                                <?php echo htmlspecialchars($notif['message']); ?>
                            </div>
                        </a>
                        <div class="d-flex align-items-center gap-2" style="flex-shrink: 0;">
                            <span style="font-size: 11px; color: var(--text-muted); white-space: nowrap;">
                                <?php echo formatDate($notif['created_at'], 'M d, Y H:i'); ?>
                            </span>
                            <?php if ($notif['status'] === 'unread') { ?>
                            <a href="notifications.php?action=mark_read&id=<?php echo $notif['id']; ?>" class="btn btn-sm" style="padding: 4px 8px; border-radius: 6px; background: rgba(16,185,129,0.1); color: var(--success); border: none;" title="Mark as read">
                                <i class="bi bi-check-lg"></i>
                            </a>
                            <?php } ?>
                            <a href="notifications.php?action=delete&id=<?php echo $notif['id']; ?>" class="btn btn-sm" style="padding: 4px 8px; border-radius: 6px; background: rgba(239,68,68,0.1); color: var(--danger); border: none;" title="Delete" onclick="return confirm('Delete this notification?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</div>

<style>
.notification-item:hover {
    background: rgba(26, 92, 46, 0.02);
}
.notification-item.unread {
    background: rgba(59, 130, 246, 0.03);
}
.notification-item.unread:hover {
    background: rgba(59, 130, 246, 0.06);
}
</style>

<?php require_once 'includes/footer.php'; ?>