<?php
require_once 'config/config.php';

if (isLoggedIn()) {
    logActivity($_SESSION['user_id'], 'logout', 'User logged out');
}

session_destroy();
redirect('login.php', 'You have been logged out successfully.', 'success');
?>
