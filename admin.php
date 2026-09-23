<?php
require_once 'config/config.php';
requireAdmin();

$pageTitle = 'Admin Panel';
$pageSubtitle = 'System administration and management';

$userId = $_SESSION['user_id'];

// Fetch statistics
$totalUsers = fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'farmer'")['count'] ?? 0;
$totalAdmins = fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'admin'")['count'] ?? 0;
$totalPlantations = fetchOne("SELECT COUNT(*) as count FROM plantations")['count'] ?? 0;
$totalDiseasePredictions = fetchOne("SELECT COUNT(*) as count FROM disease_predictions")['count'] ?? 0;
$totalYieldPredictions = fetchOne("SELECT COUNT(*) as count FROM yield_predictions")['count'] ?? 0;
$totalGradeClassifications = fetchOne("SELECT COUNT(*) as count FROM tea_grade_classifications")['count'] ?? 0;

// Fetch all users
$users = fetchAll("SELECT u.*, COUNT(p.plantation_id) as plantation_count FROM users u LEFT JOIN plantations p ON u.id = p.user_id GROUP BY u.id ORDER BY u.created_at DESC");

// Fetch recent activity logs
$activityLogs = fetchAll("SELECT al.*, u.fullname FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 20");

// Handle user actions
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $targetUserId = intval($_GET['user_id']);
    $action = $_GET['action'];

    if ($action === 'toggle_status') {
        $user = fetchOne("SELECT status FROM users WHERE id = ?", [$targetUserId]);
        $newStatus = ($user['status'] ?? 'active') === 'active' ? 'inactive' : 'active';
        executeQuery("UPDATE users SET status = ? WHERE id = ?", [$newStatus, $targetUserId]);
        logActivity($userId, 'admin_toggle_user', "Changed user $targetUserId status to $newStatus");
        redirect('admin.php', 'User status updated.', 'success');
    }

    if ($action === 'delete_user' && $targetUserId !== $userId) {
        executeQuery("DELETE FROM users WHERE id = ?", [$targetUserId]);
        logActivity($userId, 'admin_delete_user', "Deleted user $targetUserId");
        redirect('admin.php', 'User deleted.', 'success');
    }

    if ($action === 'make_admin' && $targetUserId !== $userId) {
        executeQuery("UPDATE users SET role = 'admin' WHERE id = ?", [$targetUserId]);
        logActivity($userId, 'admin_promote', "Promoted user $targetUserId to admin");
        redirect('admin.php', 'User promoted to admin.', 'success');
    }
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/navbar.php';
?>

<style>
/* Admin dashboard compact stat cards - inline to avoid CSS conflicts */
.admin-stat-row {
    --bs-gutter-x: 10px;
    --bs-gutter-y: 10px;
}
.admin-stat-row > [class*="col-"] {
    padding-right: calc(var(--bs-gutter-x) * 0.5);
    padding-left: calc(var(--bs-gutter-x) * 0.5);
}
.admin-stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 14px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    border: 1px solid rgba(0,0,0,0.04);
    height: 100%;
}
.admin-stat-card .admin-stat-icon {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.admin-stat-card .admin-stat-icon.blue {
    background: rgba(59, 130, 246, 0.08);
    color: #3b82f6;
}
.admin-stat-card .admin-stat-icon.gold {
    background: rgba(212, 168, 67, 0.08);
    color: #d4a843;
}
.admin-stat-card .admin-stat-icon.green {
    background: rgba(16, 185, 129, 0.08);
    color: #10b981;
}
.admin-stat-card .admin-stat-icon.red {
    background: rgba(239, 68, 68, 0.08);
    color: #ef4444;
}
.admin-stat-card .admin-stat-info {
    display: flex;
    flex-direction: column;
    gap: 1px;
    min-width: 0;
}
.admin-stat-card .admin-stat-info h3 {
    font-size: 20px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #1a1a2e;
}
.admin-stat-card .admin-stat-info .admin-stat-label {
    font-size: 11px;
    color: #6b7280;
    line-height: 1.3;
    white-space: nowrap;
}
@media (max-width: 575px) {
    .admin-stat-card {
        padding: 12px 10px;
        gap: 8px;
    }
    .admin-stat-card .admin-stat-icon {
        width: 32px;
        height: 32px;
        min-width: 32px;
        font-size: 14px;
    }
    .admin-stat-card .admin-stat-info h3 {
        font-size: 18px;
    }
    .admin-stat-card .admin-stat-info .admin-stat-label {
        font-size: 10px;
    }
}
</style>

<div class="content-area">
    <nav aria-label="breadcrumb" style="margin-bottom: 20px;">
        <ol class="breadcrumb" style="font-size: 13px; margin: 0;">
            <li class="breadcrumb-item"><a href="dashboard.php" style="color: var(--primary-green); text-decoration: none;">Dashboard</a></li>
            <li class="breadcrumb-item active" style="color: var(--text-muted);">Admin Panel</li>
        </ol>
    </nav>

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
    <div class="row admin-stat-row mb-4">
        <div class="col-xl-2 col-lg-4 col-md-4 col-6">
            <div class="admin-stat-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="admin-stat-icon blue">
                    <i class="bi bi-people"></i>
                </div>
                <div class="admin-stat-info">
                    <h3><?php echo $totalUsers; ?></h3>
                    <div class="admin-stat-label">Farmers</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-6">
            <div class="admin-stat-card animate-fade-in" style="animation-delay: 0.15s;">
                <div class="admin-stat-icon gold">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div class="admin-stat-info">
                    <h3><?php echo $totalAdmins; ?></h3>
                    <div class="admin-stat-label">Admins</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-6">
            <div class="admin-stat-card animate-fade-in" style="animation-delay: 0.2s;">
                <div class="admin-stat-icon green">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <div class="admin-stat-info">
                    <h3><?php echo $totalPlantations; ?></h3>
                    <div class="admin-stat-label">Plantations</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-6">
            <div class="admin-stat-card animate-fade-in" style="animation-delay: 0.25s;">
                <div class="admin-stat-icon red">
                    <i class="bi bi-shield-plus"></i>
                </div>
                <div class="admin-stat-info">
                    <h3><?php echo $totalDiseasePredictions; ?></h3>
                    <div class="admin-stat-label">Disease Scans</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-6">
            <div class="admin-stat-card animate-fade-in" style="animation-delay: 0.3s;">
                <div class="admin-stat-icon blue">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="admin-stat-info">
                    <h3><?php echo $totalYieldPredictions; ?></h3>
                    <div class="admin-stat-label">Yield Predictions</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-6">
            <div class="admin-stat-card animate-fade-in" style="animation-delay: 0.35s;">
                <div class="admin-stat-icon gold">
                    <i class="bi bi-award"></i>
                </div>
                <div class="admin-stat-info">
                    <h3><?php echo $totalGradeClassifications; ?></h3>
                    <div class="admin-stat-label">Grade Scans</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="dashboard-card animate-fade-in mb-4" style="animation-delay: 0.4s;">
        <div class="section-header">
            <h3><i class="bi bi-people" style="margin-right: 8px; color: var(--primary-green);"></i>Manage Users</h3>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Plantations</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($u['fullname']); ?>&background=1a5c2e&color=fff&size=40" alt="" style="width: 36px; height: 36px; border-radius: 10px;">
                                <div>
                                    <div style="font-weight: 600; font-size: 13px;"><?php echo htmlspecialchars($u['fullname']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size: 12px;"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td style="font-size: 12px;"><?php echo htmlspecialchars($u['phone'] ?? '-'); ?></td>
                        <td>
                            <span class="badge-custom badge-<?php echo $u['role'] === 'admin' ? 'warning' : 'info'; ?>">
                                <?php echo ucfirst($u['role']); ?>
                            </span>
                        </td>
                        <td><?php echo $u['plantation_count']; ?></td>
                        <td>
                            <span class="badge-custom badge-<?php echo $u['status'] === 'active' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($u['status']); ?>
                            </span>
                        </td>
                        <td style="font-size: 12px; color: var(--text-muted);"><?php echo formatDate($u['created_at']); ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="admin.php?action=toggle_status&user_id=<?php echo $u['id']; ?>" class="btn btn-sm" style="padding: 4px 8px; border-radius: 6px; background: rgba(59,130,246,0.1); color: var(--info); border: none;" title="Toggle Status">
                                    <i class="bi bi-toggle-<?php echo $u['status'] === 'active' ? 'on' : 'off'; ?>"></i>
                                </a>
                                <?php if ($u['role'] !== 'admin'): ?>
                                <a href="admin.php?action=make_admin&user_id=<?php echo $u['id']; ?>" class="btn btn-sm" style="padding: 4px 8px; border-radius: 6px; background: rgba(212,168,67,0.1); color: var(--accent-gold); border: none;" title="Make Admin" onclick="return confirm('Promote this user to admin?')">
                                    <i class="bi bi-shield-check"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($u['id'] !== $userId): ?>
                                <a href="admin.php?action=delete_user&user_id=<?php echo $u['id']; ?>" class="btn btn-sm" style="padding: 4px 8px; border-radius: 6px; background: rgba(239,68,68,0.1); color: var(--danger); border: none;" title="Delete" onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Activity Logs -->
    <div class="dashboard-card animate-fade-in" style="animation-delay: 0.5s;">
        <div class="section-header">
            <h3><i class="bi bi-clock-history" style="margin-right: 8px; color: var(--primary-green);"></i>Activity Logs</h3>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($activityLogs)): ?>
                    <tr><td colspan="5" class="text-center py-4" style="color: var(--text-muted);">No activity logs found.</td></tr>
                    <?php else: ?>
                    <?php foreach ($activityLogs as $log): ?>
                    <tr>
                        <td style="font-size: 12px; color: var(--text-muted);"><?php echo formatDate($log['created_at'], 'M d, Y H:i:s'); ?></td>
                        <td style="font-size: 12px;"><?php echo htmlspecialchars($log['fullname'] ?? 'System'); ?></td>
                        <td>
                            <span class="badge-custom badge-info" style="font-size: 11px;"><?php echo htmlspecialchars($log['action']); ?></span>
                        </td>
                        <td style="font-size: 12px;"><?php echo htmlspecialchars($log['description'] ?? '-'); ?></td>
                        <td style="font-size: 12px; color: var(--text-muted);"><?php echo $log['ip_address'] ?? '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>