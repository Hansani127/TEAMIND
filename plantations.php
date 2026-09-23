<?php
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Plantations';
$pageSubtitle = 'Manage your tea plantations';

$userId = $_SESSION['user_id'];

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle delete
if ($action === 'delete' && isset($_GET['id'])) {
    $plantationId = intval($_GET['id']);
    executeQuery("DELETE FROM plantations WHERE plantation_id = ? AND user_id = ?", [$plantationId, $userId]);
    logActivity($userId, 'delete_plantation', "Deleted plantation ID: $plantationId");
    redirect('plantations.php', 'Plantation deleted successfully.', 'success');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plantationId = intval($_POST['plantation_id'] ?? 0);
    $name = trim($_POST['plantation_name'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $estate = trim($_POST['estate'] ?? '');
    $teaType = $_POST['tea_type'] ?? 'CTC';
    $area = floatval($_POST['area'] ?? 0);
    $latitude = floatval($_POST['latitude'] ?? 0);
    $longitude = floatval($_POST['longitude'] ?? 0);
    $soilType = trim($_POST['soil_type'] ?? '');
    $elevation = intval($_POST['elevation'] ?? 0);

    if (empty($name) || empty($district)) {
        $error = 'Plantation name and district are required.';
    } else {
        if ($plantationId > 0) {
            // Update
            executeQuery(
                "UPDATE plantations SET plantation_name = ?, district = ?, estate = ?, tea_type = ?, area = ?, latitude = ?, longitude = ?, soil_type = ?, elevation = ? WHERE plantation_id = ? AND user_id = ?",
                [$name, $district, $estate, $teaType, $area, $latitude, $longitude, $soilType, $elevation, $plantationId, $userId]
            );
            logActivity($userId, 'update_plantation', "Updated plantation: $name");
            redirect('plantations.php', 'Plantation updated successfully.', 'success');
        } else {
            // Insert
            executeQuery(
                "INSERT INTO plantations (user_id, plantation_name, district, estate, tea_type, area, latitude, longitude, soil_type, elevation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$userId, $name, $district, $estate, $teaType, $area, $latitude, $longitude, $soilType, $elevation]
            );
            logActivity($userId, 'create_plantation', "Created plantation: $name");
            redirect('plantations.php', 'Plantation created successfully.', 'success');
        }
    }
}

// Fetch plantations
$plantations = fetchAll("SELECT * FROM plantations WHERE user_id = ? ORDER BY created_at DESC", [$userId]);

// Fetch single plantation for edit
$editPlantation = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editPlantation = fetchOne("SELECT * FROM plantations WHERE plantation_id = ? AND user_id = ?", [intval($_GET['id']), $userId]);
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/navbar.php';
?>

<div class="content-area">
    <nav aria-label="breadcrumb" style="margin-bottom: 20px;">
        <ol class="breadcrumb" style="font-size: 13px; margin: 0;">
            <li class="breadcrumb-item"><a href="dashboard.php" style="color: var(--primary-green); text-decoration: none;">Dashboard</a></li>
            <li class="breadcrumb-item active" style="color: var(--text-muted);">Plantations</li>
        </ol>
    </nav>

    <?php if ($error): ?>
    <div class="alert-custom alert-danger-custom animate-fade-in">
        <i class="bi bi-exclamation-circle"></i>
        <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
    <div class="alert-custom alert-<?php echo $flash['type']; ?>-custom animate-fade-in">
        <i class="bi bi-check-circle"></i>
        <?php echo $flash['message']; ?>
    </div>
    <?php endif; ?>

    <?php if ($action === 'add' || $action === 'edit'): ?>
    <!-- Add/Edit Form -->
    <div class="dashboard-card animate-fade-in">
        <div class="section-header">
            <h3><i class="bi bi-<?php echo $action === 'edit' ? 'pencil' : 'plus-circle'; ?>" style="margin-right: 8px; color: var(--primary-green);"></i><?php echo $action === 'edit' ? 'Edit' : 'Add'; ?> Plantation</h3>
            <a href="plantations.php" class="btn-outline-custom" style="padding: 8px 16px; font-size: 12px;">
                <i class="bi bi-arrow-left" style="margin-right: 4px;"></i>Back
            </a>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="plantation_id" value="<?php echo $editPlantation['plantation_id'] ?? 0; ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Plantation Name <span style="color: #ef4444;">*</span></label>
                    <div class="input-wrapper" style="position: relative;">
                        <input type="text" name="plantation_name" class="form-control" style="padding: 12px 14px 12px 44px; border-radius: 10px; font-size: 14px; border: 1.5px solid var(--border-color);" placeholder="e.g., Kotagala Estate" required value="<?php echo htmlspecialchars($editPlantation['plantation_name'] ?? ''); ?>">
                        <i class="bi bi-geo-alt" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 18px;"></i>
                    </div>
                </div>

                <div class="col-md-6">
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">District <span style="color: #ef4444;">*</span></label>
                    <div class="input-wrapper" style="position: relative;">
                        <input type="text" name="district" class="form-control" style="padding: 12px 14px 12px 44px; border-radius: 10px; font-size: 14px; border: 1.5px solid var(--border-color);" placeholder="e.g., Nuwara Eliya" required value="<?php echo htmlspecialchars($editPlantation['district'] ?? ''); ?>">
                        <i class="bi bi-map" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 18px;"></i>
                    </div>
                </div>

                <div class="col-md-6">
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Estate Name</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input type="text" name="estate" class="form-control" style="padding: 12px 14px 12px 44px; border-radius: 10px; font-size: 14px; border: 1.5px solid var(--border-color);" placeholder="e.g., Loolecondera" value="<?php echo htmlspecialchars($editPlantation['estate'] ?? ''); ?>">
                        <i class="bi bi-building" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 18px;"></i>
                    </div>
                </div>

                <div class="col-md-6">
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Tea Type</label>
                    <select name="tea_type" class="form-select" style="border-radius: 10px; font-size: 14px; padding: 12px 14px; border: 1.5px solid var(--border-color);">
                        <option value="CTC" <?php echo ($editPlantation['tea_type'] ?? '') === 'CTC' ? 'selected' : ''; ?>>CTC</option>
                        <option value="Orthodox" <?php echo ($editPlantation['tea_type'] ?? '') === 'Orthodox' ? 'selected' : ''; ?>>Orthodox</option>
                        <option value="Green Tea" <?php echo ($editPlantation['tea_type'] ?? '') === 'Green Tea' ? 'selected' : ''; ?>>Green Tea</option>
                        <option value="White Tea" <?php echo ($editPlantation['tea_type'] ?? '') === 'White Tea' ? 'selected' : ''; ?>>White Tea</option>
                        <option value="Oolong" <?php echo ($editPlantation['tea_type'] ?? '') === 'Oolong' ? 'selected' : ''; ?>>Oolong</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Area (acres)</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input type="number" name="area" step="0.01" class="form-control" style="padding: 12px 14px 12px 44px; border-radius: 10px; font-size: 14px; border: 1.5px solid var(--border-color);" placeholder="e.g., 50.5" value="<?php echo $editPlantation['area'] ?? ''; ?>">
                        <i class="bi bi-rulers" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 18px;"></i>
                    </div>
                </div>

                <div class="col-md-4">
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Elevation (m)</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input type="number" name="elevation" class="form-control" style="padding: 12px 14px 12px 44px; border-radius: 10px; font-size: 14px; border: 1.5px solid var(--border-color);" placeholder="e.g., 1800" value="<?php echo $editPlantation['elevation'] ?? ''; ?>">
                        <i class="bi bi-arrow-up" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 18px;"></i>
                    </div>
                </div>

                <div class="col-md-4">
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Soil Type</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input type="text" name="soil_type" class="form-control" style="padding: 12px 14px 12px 44px; border-radius: 10px; font-size: 14px; border: 1.5px solid var(--border-color);" placeholder="e.g., Loamy" value="<?php echo htmlspecialchars($editPlantation['soil_type'] ?? ''); ?>">
                        <i class="bi bi-layers" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 18px;"></i>
                    </div>
                </div>

                <div class="col-md-6">
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Latitude</label>
                    <input type="number" name="latitude" step="0.00000001" class="form-control" style="padding: 12px 14px; border-radius: 10px; font-size: 14px; border: 1.5px solid var(--border-color);" placeholder="e.g., 6.9271" value="<?php echo $editPlantation['latitude'] ?? ''; ?>">
                </div>

                <div class="col-md-6">
                    <label style="font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block;">Longitude</label>
                    <input type="number" name="longitude" step="0.00000001" class="form-control" style="padding: 12px 14px; border-radius: 10px; font-size: 14px; border: 1.5px solid var(--border-color);" placeholder="e.g., 79.8612" value="<?php echo $editPlantation['longitude'] ?? ''; ?>">
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-primary-custom">
                    <i class="bi bi-check-lg" style="margin-right: 6px;"></i><?php echo $action === 'edit' ? 'Update' : 'Create'; ?> Plantation
                </button>
                <a href="plantations.php" class="btn-outline-custom">Cancel</a>
            </div>
        </form>
    </div>

    <?php else: ?>
    <!-- List View -->
    <div class="dashboard-card animate-fade-in">
        <div class="section-header">
            <h3><i class="bi bi-geo-alt" style="margin-right: 8px; color: var(--primary-green);"></i>Your Plantations</h3>
            <a href="plantations.php?action=add" class="btn-primary-custom" style="padding: 8px 16px; font-size: 12px;">
                <i class="bi bi-plus-lg" style="margin-right: 4px;"></i>Add Plantation
            </a>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Plantation</th>
                        <th>District</th>
                        <th>Tea Type</th>
                        <th>Area</th>
                        <th>Elevation</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($plantations)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5" style="color: var(--text-muted);">
                            <i class="bi bi-geo-alt" style="font-size: 32px; display: block; margin-bottom: 12px;"></i>
                            No plantations added yet.<br>
                            <a href="plantations.php?action=add" style="color: var(--primary-green); font-weight: 600;">Add your first plantation</a>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($plantations as $p): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($p['plantation_name']); ?></strong>
                            <?php if ($p['estate']): ?>
                            <div style="font-size: 11px; color: var(--text-muted);"><?php echo htmlspecialchars($p['estate']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($p['district']); ?></td>
                        <td>
                            <span class="badge-custom badge-info"><?php echo $p['tea_type']; ?></span>
                        </td>
                        <td><?php echo $p['area'] ? $p['area'] . ' acres' : '-'; ?></td>
                        <td><?php echo $p['elevation'] ? $p['elevation'] . ' m' : '-'; ?></td>
                        <td style="color: var(--text-muted); font-size: 12px;"><?php echo formatDate($p['created_at']); ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="plantations.php?action=edit&id=<?php echo $p['plantation_id']; ?>" class="btn btn-sm" style="padding: 4px 8px; border-radius: 6px; background: rgba(59,130,246,0.1); color: var(--info); border: none;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="plantations.php?action=delete&id=<?php echo $p['plantation_id']; ?>" class="btn btn-sm" style="padding: 4px 8px; border-radius: 6px; background: rgba(239,68,68,0.1); color: var(--danger); border: none;" onclick="return confirm('Are you sure you want to delete this plantation?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
