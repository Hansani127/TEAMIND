<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME; ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">

    <style>
        :root {
            --primary-green: #1a5c2e;
            --primary-green-light: #2d7a3e;
            --primary-green-dark: #0d3d1a;
            --accent-gold: #d4a843;
            --accent-gold-light: #e8c76a;
            --sidebar-bg: #1a5c2e;
            --sidebar-width: 260px;
            --sidebar-collapsed: 70px;
            --bg-light: #f8faf9;
            --card-bg: #ffffff;
            --text-dark: #1a1a2e;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--primary-green-dark) 100%);
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed { width: var(--sidebar-collapsed); }

        .sidebar-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand .logo-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-brand .logo-icon i {
            font-size: 22px;
            color: #fff;
        }

        .sidebar-brand-text h4 {
            color: #fff;
            font-weight: 700;
            font-size: 18px;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .sidebar-brand-text span {
            color: rgba(255,255,255,0.7);
            font-size: 11px;
            font-weight: 400;
        }

        .sidebar-menu { padding: 16px 12px; }

        .sidebar-menu .menu-label {
            color: rgba(255,255,255,0.5);
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 12px;
            margin-bottom: 8px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 4px;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.12);
            color: #fff;
        }

        .sidebar-menu a.active {
            background: rgba(255,255,255,0.2);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .sidebar-menu a i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .sidebar-footer a:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        /* Main Content */
       .main-content,
.content-area,
.top-navbar {
    margin-left: var(--sidebar-width);
    transition: margin-left 0.3s ease;
    width: auto;
}

/* Ensure content fills the screen when collapsed */
.sidebar.collapsed {
    width: var(--sidebar-collapsed);
}

.sidebar.collapsed .sidebar-brand-text,
.sidebar.collapsed .sidebar-menu a span,
.sidebar.collapsed .sidebar-menu .menu-label,
.sidebar.collapsed .sidebar-footer a span {
    display: none;
}

.sidebar.collapsed .sidebar-brand {
    justify-content: center;
    padding: 24px 10px;
}

.sidebar.collapsed .sidebar-menu a {
    justify-content: center;
    padding: 12px;
}

.sidebar.collapsed .sidebar-footer a {
    justify-content: center;
}

        /* Sidebar collapsed state */
        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar.collapsed .sidebar-brand-text,
        .sidebar.collapsed .sidebar-menu a span,
        .sidebar.collapsed .sidebar-menu .menu-label,
        .sidebar.collapsed .sidebar-footer a span {
            display: none;
        }

        .sidebar.collapsed .sidebar-brand {
            justify-content: center;
            padding: 24px 10px;
        }

        .sidebar.collapsed .sidebar-menu a {
            justify-content: center;
            padding: 12px;
        }

        .sidebar.collapsed .sidebar-footer a {
            justify-content: center;
        }

        /* Top Navbar */
        .top-navbar {
            background: var(--card-bg);
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .page-title h2 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .page-title p {
            font-size: 13px;
            color: var(--text-muted);
            margin: 2px 0 0 0;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .navbar-actions .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .navbar-actions .btn-icon:hover {
            background: var(--bg-light);
            color: var(--primary-green);
        }

        .navbar-actions .btn-icon .badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--danger);
            color: #fff;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 10px;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px 6px 6px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .user-dropdown:hover { background: var(--bg-light); }

        .user-dropdown img {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            object-fit: cover;
        }

        .user-dropdown .user-info { line-height: 1.2; }

        .user-dropdown .user-info .name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .user-dropdown .user-info .role {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Content Area */
        .content-area { padding: 24px 28px; }

        /* Cards */
        .dashboard-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            height: 100%;
        }

        .dashboard-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .stat-card {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .stat-icon.green { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .stat-icon.red { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .stat-icon.blue { background: rgba(59, 130, 246, 0.1); color: var(--info); }
        .stat-icon.gold { background: rgba(212, 168, 67, 0.1); color: var(--accent-gold); }

        .stat-info h3 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .stat-info .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .stat-info .stat-change {
            font-size: 12px;
            font-weight: 600;
            margin-top: 4px;
        }

        .stat-change.up { color: var(--success); }
        .stat-change.down { color: var(--danger); }

        /* Section Headers */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .section-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .section-header a {
            font-size: 13px;
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 600;
        }

        /* Tables */
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .custom-table th {
            background: var(--bg-light);
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .custom-table td {
            padding: 14px 16px;
            font-size: 13px;
            color: var(--text-dark);
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .custom-table tr:hover td { background: rgba(26, 92, 46, 0.02); }

        .badge-custom {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .badge-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .badge-info { background: rgba(59, 130, 246, 0.1); color: var(--info); }

        /* Buttons */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26, 92, 46, 0.3);
            color: #fff;
        }

        .btn-outline-custom {
            background: transparent;
            color: var(--primary-green);
            border: 1.5px solid var(--primary-green);
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-outline-custom:hover {
            background: var(--primary-green);
            color: #fff;
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 16px;
            padding: 48px 24px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            background: var(--bg-light);
        }

        .upload-area:hover {
            border-color: var(--primary-green);
            background: rgba(26, 92, 46, 0.03);
        }

        .upload-area i {
            font-size: 48px;
            color: var(--primary-green);
            margin-bottom: 16px;
        }

        .upload-area h4 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .upload-area p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        /* Progress Bars */
        .progress-custom {
            height: 8px;
            border-radius: 4px;
            background: var(--bg-light);
            overflow: hidden;
        }

        .progress-custom .progress-bar {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        /* AI Insights */
        .ai-insight {
            background: linear-gradient(135deg, rgba(26, 92, 46, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%);
            border: 1px solid rgba(26, 92, 46, 0.1);
            border-radius: 12px;
            padding: 16px;
        }

        .ai-insight-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(26, 92, 46, 0.08);
        }

        .ai-insight-item:last-child { border-bottom: none; }

        .ai-insight-item i {
            color: var(--success);
            font-size: 16px;
            margin-top: 2px;
        }

        .ai-insight-item span {
            font-size: 13px;
            color: var(--text-dark);
            line-height: 1.5;
        }

        /* Result Cards */
        .result-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border-color);
        }

        .result-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .result-score {
            text-align: center;
            padding: 20px;
        }

        .result-score .score-value {
            font-size: 42px;
            font-weight: 800;
            color: var(--primary-green);
        }

        .result-score .score-label {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Grade Display */
        .grade-display {
            text-align: center;
            padding: 30px;
        }

        .grade-display .grade-letter {
            font-size: 64px;
            font-weight: 800;
            color: var(--primary-green);
            line-height: 1;
        }

        .grade-display .grade-label {
            font-size: 16px;
            font-weight: 600;
            color: var(--accent-gold);
            margin-top: 8px;
        }

        .grade-display .grade-stars {
            color: var(--accent-gold);
            font-size: 20px;
            margin-top: 8px;
        }

        /* Score Bars */
        .score-bar-item {
            margin-bottom: 14px;
        }

        .score-bar-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .score-bar-header span {
            font-size: 13px;
            font-weight: 500;
        }

        .score-bar-header .score-value {
            font-weight: 700;
            color: var(--primary-green);
        }

        /* Login Page */
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0d3d1a 0%, #1a5c2e 50%, #2d7a3e 100%);
            position: relative;
            overflow: hidden;
        }

        .login-page::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://images.unsplash.com/photo-1563911892437-1feda0179e1b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover;
            opacity: 0.15;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 0 20px;
        }

        .login-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.3);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-logo i {
            font-size: 48px;
            color: var(--primary-green);
        }

        .login-logo h2 {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-dark);
            margin-top: 12px;
        }

        .login-logo p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .form-floating-custom {
            position: relative;
            margin-bottom: 16px;
        }

        .form-floating-custom input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.2s;
            background: #fff;
        }

        .form-floating-custom input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(26, 92, 46, 0.1);
        }

        .form-floating-custom i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 18px;
        }

        .form-floating-custom input:focus + i,
        .form-floating-custom input:not(:placeholder-shown) + i {
            color: var(--primary-green);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(26, 92, 46, 0.35);
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
        }

        .login-footer a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
        }

        .login-footer p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 12px;
        }

        /* Alerts */
        .alert-custom {
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .alert-success-custom { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.2); }
        .alert-danger-custom { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); }
        .alert-warning-custom { background: rgba(245, 158, 11, 0.1); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.2); }
        .alert-info-custom { background: rgba(59, 130, 246, 0.1); color: var(--info); border: 1px solid rgba(59, 130, 246, 0.2); }

        /* Responsive */
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar.collapsed + .main-content { margin-left: 0; }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease forwards;
        }

        /* Make all menu links identical */
.sidebar-menu a {
    padding: 10px 16px;
    margin: 2px 12px;
}

/* Remove extra margin from menu labels */
.sidebar-menu .menu-label {
    padding: 16px 16px 8px 16px;
    margin: 0;
}

/* First menu label no extra top space */
.sidebar-menu .menu-label:first-child {
    padding-top: 8px;
}



        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }
    </style>
</head>
<body <?php echo (isset($_SESSION['dark_mode']) && $_SESSION['dark_mode']) ? 'class="dark-mode"' : ''; ?>>

<!-- Dark Mode Styles -->
<style>
body.dark-mode {
    background: #0f172a !important;
    color: #f1f5f9 !important;
}

body.dark-mode .dashboard-card {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #f1f5f9 !important;
}

body.dark-mode .top-navbar {
    background: #1e293b !important;
    border-color: #334155 !important;
}

body.dark-mode .sidebar {
    background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%) !important;
}

body.dark-mode .form-control,
body.dark-mode .form-select {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f1f5f9 !important;
}

body.dark-mode .form-control:focus,
body.dark-mode .form-select:focus {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
}

body.dark-mode .stat-info h3,
body.dark-mode .page-title h2,
body.dark-mode .section-header h3,
body.dark-mode .setting-title,
body.dark-mode .form-label,
body.dark-mode .info-value,
body.dark-mode .about-value {
    color: #f1f5f9 !important;
}

body.dark-mode .stat-label,
body.dark-mode .page-title p,
body.dark-mode .setting-desc,
body.dark-mode .info-label,
body.dark-mode .about-label,
body.dark-mode .text-muted {
    color: #94a3b8 !important;
}

body.dark-mode .custom-table th {
    background: #334155 !important;
    color: #94a3b8 !important;
    border-color: #475569 !important;
}

body.dark-mode .custom-table td {
    border-color: #334155 !important;
    color: #f1f5f9 !important;
}

body.dark-mode .custom-table tr:hover td {
    background: rgba(16, 185, 129, 0.05) !important;
}

body.dark-mode .upload-area {
    background: #1e293b !important;
    border-color: #334155 !important;
}

body.dark-mode .upload-area:hover {
    background: #334155 !important;
}

body.dark-mode .ai-insight {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(59, 130, 246, 0.08) 100%) !important;
    border-color: #334155 !important;
}

body.dark-mode .result-card {
    background: #1e293b !important;
    border-color: #334155 !important;
}

body.dark-mode .breadcrumb-item.active {
    color: #94a3b8 !important;
}

body.dark-mode .setting-row,
body.dark-mode .preview-box.light-preview {
    background: #334155 !important;
    border-color: #475569 !important;
}

body.dark-mode .preview-bar {
    background: #475569 !important;
}

body.dark-mode .input-wrap i {
    color: #94a3b8 !important;
}

body.dark-mode .btn-outline-custom {
    border-color: #475569 !important;
    color: #94a3b8 !important;
}

body.dark-mode .btn-outline-custom:hover {
    background: #334155 !important;
    color: #f1f5f9 !important;
}

body.dark-mode ::-webkit-scrollbar-thumb {
    background: #475569 !important;
}

body.dark-mode .dropdown-menu {
    background: #1e293b !important;
    border-color: #334155 !important;
}

body.dark-mode .dropdown-item {
    color: #f1f5f9 !important;
}

body.dark-mode .dropdown-item:hover {
    background: #334155 !important;
}

body.dark-mode .dropdown-header {
    color: #94a3b8 !important;
    border-color: #334155 !important;
}

body.dark-mode .dropdown-divider {
    border-color: #334155 !important;
}

body.dark-mode .user-dropdown:hover {
    background: #334155 !important;
}

body.dark-mode .navbar-actions .btn-icon {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #94a3b8 !important;
}

body.dark-mode .navbar-actions .btn-icon:hover {
    background: #334155 !important;
    color: #f1f5f9 !important;
}

body.dark-mode .alert-success-custom {
    background: rgba(16, 185, 129, 0.15) !important;
    border-color: rgba(16, 185, 129, 0.25) !important;
    color: #34d399 !important;
}

body.dark-mode .alert-danger-custom {
    background: rgba(239, 68, 68, 0.15) !important;
    border-color: rgba(239, 68, 68, 0.25) !important;
    color: #f87171 !important;
}

body.dark-mode .alert-warning-custom {
    background: rgba(245, 158, 11, 0.15) !important;
    border-color: rgba(245, 158, 11, 0.25) !important;
    color: #fbbf24 !important;
}

body.dark-mode .alert-info-custom {
    background: rgba(59, 130, 246, 0.15) !important;
    border-color: rgba(59, 130, 246, 0.25) !important;
    color: #60a5fa !important;
}

body.dark-mode .accordion-button {
    background: #334155 !important;
    color: #f1f5f9 !important;
}

body.dark-mode .accordion-button:not(.collapsed) {
    background: #1e293b !important;
}

body.dark-mode .accordion-body {
    background: #1e293b !important;
}

body.dark-mode .score-bar-header span:first-child {
    color: #94a3b8 !important;
}

body.dark-mode .grade-display .grade-label {
    color: #fbbf24 !important;
}

body.dark-mode .login-card,
body.dark-mode .register-card {
    background: rgba(30, 41, 59, 0.97) !important;
}

body.dark-mode .progress-custom {
    background: #334155 !important;
}

body.dark-mode .progress-custom .progress-bar {
    background: linear-gradient(90deg, #10b981, #34d399) !important;
}
</style>