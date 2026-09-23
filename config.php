<?php
/**
 * TEAMIND - Main Configuration
 * Minimal version: Dark Mode + Password Change only
 * Table: dark_mode (renamed from user_settings)
 */

session_start();

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Colombo');

// Base URL (adjust as needed)
define('BASE_URL', 'http://localhost/teamind/');
define('SITE_NAME', 'TEAMIND');
define('SITE_TAGLINE', 'AI Powered Tea Management');

// Upload settings
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png']);

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('BCRYPT_COST', 12);

// Flask API settings
define('FLASK_API_URL', 'http://localhost:5000');

// Include database config
require_once __DIR__ . '/database.php';

// ============================================
// DARK MODE FUNCTIONS (dark_mode table)
// ============================================

/**
 * Get user's dark mode setting from database
 * Falls back to session, then default (0 = light)
 * @param int $userId
 * @return int 0 = light, 1 = dark
 */
function getDarkMode($userId) {
    // Check session first for performance
    if (isset($_SESSION['dark_mode'])) {
        return $_SESSION['dark_mode'];
    }

    // Try to get from database (dark_mode table)
    try {
        $result = fetchOne(
            "SELECT dark_mode FROM dark_mode WHERE user_id = ?",
            [$userId]
        );

        if ($result) {
            $_SESSION['dark_mode'] = (int)$result['dark_mode'];
            return $_SESSION['dark_mode'];
        }

        // No record exists, create default
        executeQuery(
            "INSERT INTO dark_mode (user_id, dark_mode) VALUES (?, 0)",
            [$userId]
        );
    } catch (Exception $e) {
        // Table doesn't exist yet, use session/default
        error_log("getDarkMode error: " . $e->getMessage());
    }

    $_SESSION['dark_mode'] = 0;
    return 0;
}

/**
 * Set user's dark mode in database
 * @param int $userId
 * @param int $mode 0 = light, 1 = dark
 * @return bool
 */
function setDarkMode($userId, $mode) {
    try {
        // Ensure record exists
        $exists = fetchOne("SELECT id FROM dark_mode WHERE user_id = ?", [$userId]);

        if (!$exists) {
            executeQuery(
                "INSERT INTO dark_mode (user_id, dark_mode) VALUES (?, ?)",
                [$userId, $mode]
            );
        } else {
            executeQuery(
                "UPDATE dark_mode SET dark_mode = ? WHERE user_id = ?",
                [$mode, $userId]
            );
        }

        $_SESSION['dark_mode'] = $mode;
        return true;
    } catch (Exception $e) {
        error_log("setDarkMode error: " . $e->getMessage());
        $_SESSION['dark_mode'] = $mode;
        return false;
    }
}

/**
 * Toggle dark mode and save to database
 * @param int $userId
 * @return int New dark_mode value
 */
function toggleDarkMode($userId) {
    $current = getDarkMode($userId);
    $newMode = $current ? 0 : 1;
    setDarkMode($userId, $newMode);
    return $newMode;
}

// ============================================
// PASSWORD CHANGE FUNCTIONS
// ============================================

/**
 * Change user password with validation and history tracking
 * @param int $userId
 * @param string $currentPassword
 * @param string $newPassword
 * @return array ['success' => bool, 'message' => string]
 */
function changeUserPassword($userId, $currentPassword, $newPassword) {
    $user = getCurrentUser();

    if (!$user) {
        return ['success' => false, 'message' => 'User not found.'];
    }

    // Validate current password
    if (!password_verify($currentPassword, $user['password'])) {
        return ['success' => false, 'message' => 'Current password is incorrect.'];
    }

    // Validate new password length
    if (strlen($newPassword) < 6) {
        return ['success' => false, 'message' => 'New password must be at least 6 characters.'];
    }

    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);

    // Save old password to history (if table exists)
    try {
        executeQuery(
            "INSERT INTO password_history (user_id, old_password_hash, ip_address) 
             VALUES (?, ?, ?)",
            [$userId, $user['password'], $_SERVER['REMOTE_ADDR'] ?? 'unknown']
        );
    } catch (Exception $e) {
        // password_history table might not exist yet, continue anyway
        error_log("Password history save skipped: " . $e->getMessage());
    }

    // Update password in users table
    executeQuery(
        "UPDATE users SET password = ? WHERE id = ?",
        [$hashedPassword, $userId]
    );

    // Log activity
    logActivity($userId, 'password_change', 'Password changed successfully');

    return ['success' => true, 'message' => 'Password changed successfully!'];
}

// ============================================
// ORIGINAL FUNCTIONS (unchanged)
// ============================================

/**
 * Log user activity
 */
function logActivity($userId, $action, $description = '') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    executeQuery(
        "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)",
        [$userId, $action, $description, $ip, $userAgent]
    );
}

/**
 * Create notification
 */
function createNotification($userId, $title, $message, $type = 'info') {
    executeQuery(
        "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)",
        [$userId, $title, $message, $type]
    );
}

/**
 * Get notification link based on title
 */
function getNotificationLink($title) {
    if (strpos($title, 'Disease') !== false) return 'disease.php';
    if (strpos($title, 'Yield') !== false) return 'yield.php';
    if (strpos($title, 'Grade') !== false) return 'grade.php';
    if (strpos($title, 'Demand') !== false) return 'demand.php';
    return 'dashboard.php';
}

/**
 * Get unread notification count
 */
function getUnreadCount($userId) {
    $result = fetchOne(
        "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND status = 'unread'",
        [$userId]
    );
    return $result['count'] ?? 0;
}

/**
 * Get system setting
 */
function getSetting($key, $default = '') {
    $result = fetchOne("SELECT setting_value FROM system_settings WHERE setting_key = ?", [$key]);
    return $result['setting_value'] ?? $default;
}

/**
 * Format number
 */
function formatNumber($number, $decimals = 0) {
    return number_format($number, $decimals);
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    if (empty($date) || $date == '0000-00-00 00:00:00') {
        return 'N/A';
    }
    return date($format, strtotime($date));
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate random string
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Redirect with message
 */
function redirect($url, $message = '', $type = 'success') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: " . BASE_URL . $url);
    exit();
}

/**
 * Get flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php', 'Please login to continue.', 'warning');
    }
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_destroy();
        redirect('login.php', 'Session expired. Please login again.', 'warning');
    }
    $_SESSION['last_activity'] = time();
}

/**
 * Require admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect('dashboard.php', 'Access denied. Admin privileges required.', 'danger');
    }
}

/**
 * Get current user
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

/**
 * Upload image file
 */
function uploadImage($file, $directory) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds maximum allowed (10MB)'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);

    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG and PNG allowed.'];
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = generateToken(16) . '.' . strtolower($ext);
    $uploadPath = UPLOAD_PATH . $directory . '/' . $filename;

    if (!is_dir(UPLOAD_PATH . $directory)) {
        mkdir(UPLOAD_PATH . $directory, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => true, 'filename' => $filename, 'path' => 'uploads/' . $directory . '/' . $filename];
    }

    return ['success' => false, 'message' => 'Failed to upload file'];
}