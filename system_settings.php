<?php
require_once 'config/config.php';
requireAdmin();

$pageTitle = 'System Settings';
$pageSubtitle = 'Manage application settings';

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// ============================================
// HANDLE FORM SUBMISSIONS
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // --- UPDATE SETTING ---
    if ($action === 'update_setting') {
        $key = $_POST['setting_key'] ?? '';
        $value = $_POST['setting_value'] ?? '';

        if (empty($key)) {
            $error = 'Setting key is required.';
        } else {
            try {
                executeQuery(
                    "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?",
                    [$value, $key]
                );
                $success = 'Setting updated successfully.';
                logActivity($userId, 'system_settings', "Updated setting: {$key}");
            } catch (Exception $e) {
                $error = 'Failed to update setting.';
            }
        }
    }

    // --- ADD NEW SETTING ---
    if ($action === 'add_setting') {
        $key = sanitize($_POST['new_key'] ?? '');
        $value = $_POST['new_value'] ?? '';

        if (empty($key) || empty($value)) {
            $error = 'Setting key and value are required.';
        } else {
            try {
                executeQuery(
                    "INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)",
                    [$key, $value]
                );
                $success = 'New setting added successfully.';
                logActivity($userId, 'system_settings', "Added new setting: {$key}");
            } catch (Exception $e) {
                $error = 'Setting key already exists or invalid data.';
            }
        }
    }

    // --- DELETE SETTING ---
    if ($action === 'delete_setting') {
        $key = $_POST['delete_key'] ?? '';
        if (!empty($key)) {
            try {
                executeQuery(
                    "DELETE FROM system_settings WHERE setting_key = ?",
                    [$key]
                );
                $success = 'Setting deleted successfully.';
                logActivity($userId, 'system_settings', "Deleted setting: {$key}");
            } catch (Exception $e) {
                $error = 'Failed to delete setting.';
            }
        }
    }
}

// Get all settings (simple, no grouping)
try {
    $settings = fetchAll("SELECT * FROM system_settings ORDER BY setting_key");
} catch (Exception $e) {
    $settings = [];
    $error = 'Could not load settings: ' . $e->getMessage();
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/navbar.php';
?>

<div class="content-area">
    <nav aria-label="breadcrumb" style="margin-bottom: 20px;">
        <ol class="breadcrumb" style="font-size: 13px; margin: 0;">
            <li class="breadcrumb-item"><a href="dashboard.php" style="color: var(--primary-green); text-decoration: none;">Dashboard</a></li>
            <li class="breadcrumb-item active" style="color: var(--text-muted);">System Settings</li>
        </ol>
    </nav>

    <?php if ($error): ?>
    <div class="alert-custom alert-danger-custom animate-fade-in" id="msgAlert">
        <i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert-custom alert-success-custom animate-fade-in" id="msgAlert">
        <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success); ?>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="dashboard-card animate-fade-in">
                <div class="section-header" style="display: flex; align-items: center; justify-content: space-between;">
                    <h3><i class="bi bi-gear" style="margin-right: 8px; color: var(--primary-green);"></i>System Settings</h3>
                    <span class="badge-custom badge-info" style="font-size: 11px;"><?php echo count($settings); ?> settings</span>
                </div>

                <div class="table-responsive">
                    <table class="table" style="font-size: 13px;">
                        <thead>
                            <tr style="background: var(--bg-light);">
                                <th style="padding: 12px; font-weight: 600; color: var(--text-dark); border-bottom: 2px solid var(--border-color);">Setting Key</th>
                                <th style="padding: 12px; font-weight: 600; color: var(--text-dark); border-bottom: 2px solid var(--border-color);">Value</th>
                                <th style="padding: 12px; font-weight: 600; color: var(--text-dark); border-bottom: 2px solid var(--border-color); width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($settings as $setting): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 12px; font-weight: 600; color: var(--text-dark);">
                                    <?php echo htmlspecialchars($setting['setting_key']); ?>
                                </td>
                                <td style="padding: 12px;">
                                    <form method="POST" action="system_settings.php" style="display: flex; gap: 8px; align-items: center;">
                                        <input type="hidden" name="action" value="update_setting">
                                        <input type="hidden" name="setting_key" value="<?php echo htmlspecialchars($setting['setting_key']); ?>">
                                        <input type="text" name="setting_value" 
                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>" 
                                               class="form-control" 
                                               style="padding: 8px 12px; font-size: 13px; min-width: 250px;">
                                        <button type="submit" class="btn-save" style="padding: 8px 16px; font-size: 12px;">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                </td>
                                <td style="padding: 12px;">
                                    <form method="POST" action="system_settings.php" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_setting">
                                        <input type="hidden" name="delete_key" value="<?php echo htmlspecialchars($setting['setting_key']); ?>">
                                        <button type="submit" class="btn-icon" onclick="return confirm('Delete this setting?')" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 4px 8px;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (empty($settings)): ?>
                <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
                    No settings found in database.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add New Setting -->
        <div class="col-lg-4">
            <div class="dashboard-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="section-header">
                    <h3><i class="bi bi-plus-circle" style="margin-right: 8px; color: var(--primary-green);"></i>Add Setting</h3>
                </div>

                <form method="POST" action="system_settings.php">
                    <input type="hidden" name="action" value="add_setting">

                    <div class="mb-3">
                        <label class="form-label">Setting Key</label>
                        <input type="text" name="new_key" class="form-control" style="padding: 10px 14px;" placeholder="e.g., site_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Setting Value</label>
                        <input type="text" name="new_value" class="form-control" style="padding: 10px 14px;" placeholder="e.g., TEAMIND" required>
                    </div>

                    <button type="submit" class="btn-save" style="width: 100%;">
                        <i class="bi bi-plus-lg"></i> Add Setting
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    text-align: left;
    vertical-align: middle;
}

.form-control {
    width: 100%;
    border: 1.5px solid var(--border-color, #e5e7eb);
    border-radius: 8px;
    font-size: 14px;
    color: var(--text-dark, #1a1a2e);
    background: #fff;
    transition: all 0.2s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-green, #1a5c2e);
    box-shadow: 0 0 0 3px rgba(26, 92, 46, 0.08);
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-dark, #1a1a2e);
    margin-bottom: 6px;
}

.btn-save {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 28px;
    background: linear-gradient(135deg, #1a5c2e 0%, #2d7a3e 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 4px 14px rgba(26, 92, 46, 0.25);
}

.btn-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(26, 92, 46, 0.35);
}

.btn-icon:hover {
    background: rgba(220, 53, 69, 0.1) !important;
    border-radius: 4px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var alertMsg = document.getElementById('msgAlert');
    if (alertMsg) {
        setTimeout(function() {
            alertMsg.style.transition = 'opacity 0.5s, transform 0.5s';
            alertMsg.style.opacity = '0';
            alertMsg.style.transform = 'translateY(-10px)';
            setTimeout(function() { alertMsg.style.display = 'none'; }, 500);
        }, 4000);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>