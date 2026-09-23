<?php
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Profile';
$pageSubtitle = 'Manage your account';

$userId = $_SESSION['user_id'];
$user = getCurrentUser();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($fullname)) {
        $error = 'Full name is required.';
    } else {
        $profileImage = $user['profile_image'];

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['tmp_name']) {
            $upload = uploadImage($_FILES['profile_image'], 'profile');
            if ($upload['success']) {
                $profileImage = $upload['path'];
            }
        }

        executeQuery(
            "UPDATE users SET fullname = ?, phone = ?, profile_image = ? WHERE id = ?",
            [$fullname, $phone, $profileImage, $userId]
        );

        $_SESSION['user_name'] = $fullname;
        $success = 'Profile updated successfully.';
        logActivity($userId, 'update_profile', 'Updated profile');
        $user = getCurrentUser();
    }
}

$totalPredictions = fetchOne("SELECT 
    (SELECT COUNT(*) FROM disease_predictions WHERE user_id = ?) + 
    (SELECT COUNT(*) FROM yield_predictions WHERE user_id = ?) + 
    (SELECT COUNT(*) FROM tea_grade_classifications WHERE user_id = ?) as total", [$userId, $userId, $userId])['total'] ?? 0;

$totalPlantations = fetchOne("SELECT COUNT(*) as count FROM plantations WHERE user_id = ?", [$userId])['count'] ?? 0;

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/navbar.php';
?>

<div class="content-area">
    <nav aria-label="breadcrumb" style="margin-bottom: 20px;">
        <ol class="breadcrumb" style="font-size: 13px; margin: 0;">
            <li class="breadcrumb-item"><a href="dashboard.php" style="color: var(--primary-green); text-decoration: none;">Dashboard</a></li>
            <li class="breadcrumb-item active" style="color: var(--text-muted);">Profile</li>
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
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="dashboard-card animate-fade-in text-center">
                <div style="position: relative; display: inline-block; margin-bottom: 20px;">
                    <img src="<?php echo $user['profile_image'] ?? 'assets/images/default-avatar.png'; ?>" 
                         alt="Profile" 
                         style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(26,92,46,0.1);"
                         onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($user['fullname']); ?>&background=1a5c2e&color=fff&size=200'">
                    <div style="position: absolute; bottom: 4px; right: 4px; width: 32px; height: 32px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #fff;">
                        <i class="bi bi-camera" style="font-size: 14px; color: #fff;"></i>
                    </div>
                </div>

                <h4 style="font-weight: 700; color: var(--text-dark); margin-bottom: 4px;"><?php echo htmlspecialchars($user['fullname']); ?></h4>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;"><?php echo htmlspecialchars($user['email']); ?></p>
                <span class="badge-custom badge-success" style="font-size: 11px;"><?php echo ucfirst($user['role']); ?></span>

                <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <div class="row g-2">
                        <div class="col-6">
                            <div style="padding: 12px; background: var(--bg-light); border-radius: 10px;">
                                <div style="font-size: 20px; font-weight: 700; color: var(--primary-green);"><?php echo $totalPlantations; ?></div>
                                <div style="font-size: 11px; color: var(--text-muted);">Plantations</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="padding: 12px; background: var(--bg-light); border-radius: 10px;">
                                <div style="font-size: 20px; font-weight: 700; color: var(--primary-green);"><?php echo $totalPredictions; ?></div>
                                <div style="font-size: 11px; color: var(--text-muted);">Predictions</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 16px; text-align: left;">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">Member Since</div>
                    <div style="font-size: 13px; font-weight: 600; color: var(--text-dark);">
                        <i class="bi bi-calendar3" style="margin-right: 8px; color: var(--primary-green);"></i>
                        <?php echo formatDate($user['created_at'] ?? '', 'F d, Y'); ?>
                    </div>
                </div>

                <?php if ($user['last_login']): ?>
                <div style="margin-top: 12px; text-align: left;">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">Last Login</div>
                    <div style="font-size: 13px; font-weight: 600; color: var(--text-dark);">
                        <i class="bi bi-clock" style="margin-right: 8px; color: var(--primary-green);"></i>
                        <?php echo formatDate($user['last_login'], 'F d, Y H:i'); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Edit Profile -->
        <div class="col-lg-8">
            <div class="dashboard-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="section-header">
                    <h3><i class="bi bi-person" style="margin-right: 8px; color: var(--primary-green);"></i>Edit Profile</h3>
                </div>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Full Name</label>
                            <div class="input-wrapper" style="position: relative;">
                                <input type="text" name="fullname" class="form-control" style="padding: 12px 14px 12px 44px; border-radius: 10px; font-size: 14px; border: 1.5px solid var(--border-color);" required value="<?php echo htmlspecialchars($user['fullname']); ?>">
                                <i class="bi bi-person" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 18px;"></i>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Email Address</label>
                            <div class="input-wrapper" style="position: relative;">
                                <input type="email" class="form-control" style="padding: 12px 14px 12px 44px; border-radius: 10px; font-size: 14px; border: 1.5px solid var(--border-color); background: var(--bg-light);" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                <i class="bi bi-envelope" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 18px;"></i>
                            </div>
                            <small style="font-size: 11px; color: var(--text-muted);">Email cannot be changed</small>
                        </div>

                        <div class="col-md-6">
                            <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Phone Number</label>
                            <div class="input-wrapper" style="position: relative;">
                                <input type="tel" name="phone" class="form-control" style="padding: 12px 14px 12px 44px; border-radius: 10px; font-size: 14px; border: 1.5px solid var(--border-color);" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                <i class="bi bi-telephone" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 18px;"></i>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Profile Image</label>
                            <input type="file" name="profile_image" class="form-control" style="border-radius: 10px; font-size: 13px; padding: 10px 14px; border: 1.5px solid var(--border-color);" accept="image/jpeg,image/jpg,image/png">
                            <small style="font-size: 11px; color: var(--text-muted);">JPG, PNG up to 10MB</small>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn-primary-custom">
                            <i class="bi bi-check-lg" style="margin-right: 6px;"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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