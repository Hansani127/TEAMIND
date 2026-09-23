<?php
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Settings';
$pageSubtitle = 'Manage your preferences';

$userId = $_SESSION['user_id'];
$user = getCurrentUser();

// Get dark mode from database (falls back to session/default)
$darkMode = getDarkMode($userId);

$error = '';
$success = '';

// ============================================
// HANDLE FORM SUBMISSIONS
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // --- CHANGE PASSWORD ---
    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($current) || empty($new) || empty($confirm)) {
            $error = 'All password fields are required.';
        } elseif (strlen($new) < 6) {
            $error = 'New password must be at least 6 characters.';
        } elseif ($new !== $confirm) {
            $error = 'New passwords do not match.';
        } else {
            $result = changeUserPassword($userId, $current, $new);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
    }

    // --- DARK MODE ---
    if ($action === 'toggle_darkmode') {
        $newMode = toggleDarkMode($userId);
        $success = $newMode ? 'Dark mode enabled.' : 'Light mode enabled.';
        logActivity($userId, 'settings', ($newMode ? 'Enabled' : 'Disabled') . ' dark mode');

        // Refresh the page to apply theme
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/navbar.php';
?>

<div class="content-area">
    <nav aria-label="breadcrumb" style="margin-bottom: 20px;">
        <ol class="breadcrumb" style="font-size: 13px; margin: 0;">
            <li class="breadcrumb-item"><a href="dashboard.php" style="color: var(--primary-green); text-decoration: none;">Dashboard</a></li>
            <li class="breadcrumb-item active" style="color: var(--text-muted);">Settings</li>
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
        <!-- Dark Mode -->
        <div class="col-lg-6">
            <div class="dashboard-card animate-fade-in">
                <div class="section-header">
                    <h3><i class="bi bi-palette" style="margin-right: 8px; color: var(--primary-green);"></i>Appearance</h3>
                </div>

                <form method="POST" action="settings.php" id="darkModeForm">
                    <input type="hidden" name="action" value="toggle_darkmode">

                    <div class="setting-row">
                        <div class="setting-info">
                            <div class="setting-title">
                                <i class="bi bi-moon-stars" style="color: var(--accent-gold);"></i>
                                Dark Mode
                            </div>
                            <div class="setting-desc">Switch between light and dark theme</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="dark_mode" value="1" <?php echo $darkMode ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-lg-6">
            <div class="dashboard-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="section-header">
                    <h3><i class="bi bi-shield-lock" style="margin-right: 8px; color: var(--primary-green);"></i>Change Password</h3>
                </div>

                <form method="POST" action="settings.php" id="passwordForm">
                    <input type="hidden" name="action" value="change_password">

                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <div class="input-wrap">
                            <i class="bi bi-lock"></i>
                            <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <div class="input-wrap">
                            <i class="bi bi-key"></i>
                            <input type="password" name="new_password" class="form-control" placeholder="Min 6 characters" required minlength="6" id="newPassword">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Confirm New Password</label>
                        <div class="input-wrap">
                            <i class="bi bi-key-fill"></i>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter new password" required minlength="6" id="confirmPassword">
                        </div>
                        <div id="passwordMatch" style="font-size: 12px; margin-top: 4px; display: none;"></div>
                    </div>

                    <button type="submit" class="btn-save" id="btnSavePassword">
                        <i class="bi bi-check-lg"></i>
                        <span>Change Password</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-dark, #1a1a2e);
    margin-bottom: 6px;
}

.input-wrap {
    position: relative;
}

.input-wrap i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted, #6b7280);
    font-size: 16px;
    z-index: 2;
}

.form-control {
    width: 100%;
    padding: 11px 14px 11px 42px;
    border: 1.5px solid var(--border-color, #e5e7eb);
    border-radius: 10px;
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

.setting-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    background: var(--bg-light, #f8faf9);
    border-radius: 12px;
    border: 1px solid var(--border-color, #e5e7eb);
}

.setting-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.setting-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--text-dark, #1a1a2e);
    display: flex;
    align-items: center;
    gap: 8px;
}

.setting-title i {
    font-size: 18px;
}

.setting-desc {
    font-size: 12px;
    color: var(--text-muted, #6b7280);
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 28px;
    flex-shrink: 0;
    cursor: pointer;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #d1d5db;
    transition: .3s;
    border-radius: 28px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.toggle-switch input:checked + .toggle-slider {
    background-color: var(--primary-green, #1a5c2e);
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(22px);
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

.btn-save:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
    display: inline-block;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts
    var alertMsg = document.getElementById('msgAlert');
    if (alertMsg) {
        setTimeout(function() {
            alertMsg.style.transition = 'opacity 0.5s, transform 0.5s';
            alertMsg.style.opacity = '0';
            alertMsg.style.transform = 'translateY(-10px)';
            setTimeout(function() { alertMsg.style.display = 'none'; }, 500);
        }, 4000);
    }

    // Password form handling
    var passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function() {
            var btn = document.getElementById('btnSavePassword');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner"></span><span>Saving...</span>';
            }
        });
    }

    // Password match check
    var newPassword = document.getElementById('newPassword');
    var confirmPassword = document.getElementById('confirmPassword');
    var matchDiv = document.getElementById('passwordMatch');

    function checkMatch() {
        if (!newPassword || !confirmPassword || !matchDiv) return;
        if (confirmPassword.value === '') {
            matchDiv.style.display = 'none';
            return;
        }
        matchDiv.style.display = 'block';
        if (newPassword.value === confirmPassword.value) {
            matchDiv.innerHTML = '<i class="bi bi-check-circle" style="color: var(--primary-green);"></i> Passwords match';
            matchDiv.style.color = 'var(--primary-green)';
        } else {
            matchDiv.innerHTML = '<i class="bi bi-x-circle" style="color: #dc3545;"></i> Passwords do not match';
            matchDiv.style.color = '#dc3545';
        }
    }

    if (confirmPassword) confirmPassword.addEventListener('input', checkMatch);
    if (newPassword) newPassword.addEventListener('input', checkMatch);
});
</script>

<?php require_once 'includes/footer.php'; ?>