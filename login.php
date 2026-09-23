<?php
require_once 'config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $user = fetchOne("SELECT * FROM users WHERE email = ? AND status = 'active'", [$email]);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['last_activity'] = time();

            executeQuery("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
            logActivity($user['id'], 'login', 'User logged in successfully');
            redirect('dashboard.php', 'Welcome back, ' . $user['fullname'] . '!');
        } else {
            $error = 'Invalid email or password.';
            logActivity(null, 'login_failed', "Failed login attempt for email: $email");
        }
    }
}

$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TEAMIND</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #1a5c2e;
            --primary-green-light: #2d7a3e;
            --primary-green-dark: #0d3d1a;
            --accent-gold: #d4a843;
            --text-dark: #1a1a2e;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0d3d1a 0%, #1a5c2e 40%, #2d7a3e 100%);
            position: relative;
            overflow: hidden;
            padding: 16px;
        }

        .login-page::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://images.unsplash.com/photo-1563911892437-1feda0179e1b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover;
            opacity: 0.12;
        }

        .login-page::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(ellipse at center, transparent 0%, rgba(13, 61, 26, 0.6) 100%);
        }

        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            padding: 0;
        }

        .login-card {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 32px 32px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,255,255,0.1);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 24px;
        }

        .login-logo .logo-circle {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 4px 14px rgba(26, 92, 46, 0.3);
        }

        .login-logo .logo-circle i {
            font-size: 24px;
            color: #fff;
        }

        .login-logo h2 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .login-logo p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .form-group-custom {
            margin-bottom: 14px;
        }

        .form-group-custom label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 10px 36px 10px 40px;
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            font-size: 13px;
            transition: all 0.25s;
            background: #fff;
            color: var(--text-dark);
            height: 42px;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(26, 92, 46, 0.08);
        }

        .input-wrapper input::placeholder {
            color: #9ca3af;
            font-size: 12px;
        }

        .input-wrapper i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            transition: color 0.25s;
            pointer-events: none;
        }

        .input-wrapper input:focus ~ i.input-icon {
            color: var(--primary-green);
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            z-index: 2;
        }

        .password-toggle:hover { 
            color: var(--primary-green); 
            background: rgba(26, 92, 46, 0.05);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 4px 14px rgba(26, 92, 46, 0.25);
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 6px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26, 92, 46, 0.35);
        }

        .login-footer {
            text-align: center;
            margin-top: 16px;
        }

        .login-footer p {
            font-size: 12px;
            color: var(--text-muted);
        }

        .login-footer a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 700;
        }

        .alert-custom {
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
        }

        .alert-danger-custom {
            background: rgba(239, 68, 68, 0.08);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.15);
        }

        .alert-success-custom {
            background: rgba(16, 185, 129, 0.08);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.15);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeInUp 0.6s ease forwards;
        }

        @media (max-width: 480px) {
            .login-card { padding: 28px 24px; }
            .login-logo h2 { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-container animate-in">
            <div class="login-card">
                <div class="login-logo">
                    <div class="logo-circle">
                        <i class="bi bi-flower1"></i>
                    </div>
                    <h2>TEAMIND</h2>
                    <p>AI Powered Tea Management</p>
                </div>

                <?php if ($error): ?>
                <div class="alert-custom alert-danger-custom">
                    <i class="bi bi-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <?php 
                $flash = getFlashMessage();
                if ($flash): 
                ?>
                <div class="alert-custom alert-<?php echo $flash['type']; ?>-custom">
                    <i class="bi bi-check-circle"></i>
                    <?php echo $flash['message']; ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group-custom">
                        <label>Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" name="email" placeholder="Enter your email" required 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <i class="bi bi-envelope input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" placeholder="Enter your password" required>
                            <i class="bi bi-lock input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Sign In
                    </button>
                </form>

                <div class="login-footer">
                    <p>Don't have an account? <a href="register.php">Register</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        setTimeout(() => {
            document.querySelectorAll('.alert-custom').forEach(alert => {
                alert.style.transition = 'opacity 0.5s, transform 0.5s';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 500);
            });
        }, 4000);
    </script>
</body>
</html>