<?php
require_once 'config/config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $existing = fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            $error = 'Email address is already registered.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
            executeQuery(
                "INSERT INTO users (fullname, email, phone, password, role) VALUES (?, ?, ?, ?, 'farmer')",
                [$fullname, $email, $phone, $hashedPassword]
            );
            $userId = lastInsertId();
            logActivity($userId, 'register', 'New user registered');
            createNotification($userId, 'Welcome to TEAMIND!', 'Your account has been created successfully. Start exploring the AI-powered features.', 'success');
            $success = 'Registration successful! Please login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | TEAMIND</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #1a5c2e;
            --primary-green-light: #2d7a3e;
            --text-dark: #1a1a2e;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }

        .register-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0d3d1a 0%, #1a5c2e 40%, #2d7a3e 100%);
            position: relative;
            padding: 8px;
        }

        .register-page::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://images.unsplash.com/photo-1563911892437-1feda0179e1b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover;
            opacity: 0.12;
        }

        .register-page::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(ellipse at center, transparent 0%, rgba(13, 61, 26, 0.6) 100%);
        }

        .register-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 400px;
        }

        .register-card {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 20px 28px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,255,255,0.1);
        }

        .register-logo {
            text-align: center;
            margin-bottom: 12px;
        }

        .register-logo .logo-circle {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 6px;
            box-shadow: 0 2px 8px rgba(26, 92, 46, 0.3);
        }

        .register-logo .logo-circle i { font-size: 20px; color: #fff; }
        .register-logo h2 { font-size: 18px; font-weight: 800; color: var(--text-dark); margin: 0; }
        .register-logo p { font-size: 11px; color: var(--text-muted); margin-top: 1px; }

        .form-group-custom { margin-bottom: 8px; }
        .form-group-custom label { display: block; font-size: 11px; font-weight: 600; color: var(--text-dark); margin-bottom: 2px; }
        .form-group-custom label .required { color: #ef4444; }

        .input-wrapper { position: relative; }
        
        /* Regular inputs (no eye icon) */
        .input-wrapper input {
            width: 100%;
            padding: 7px 12px 7px 34px;
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            font-size: 12px;
            transition: all 0.25s;
            background: #fff;
            color: var(--text-dark);
            height: 34px;
        }
        
        /* Password inputs (with eye icon on right) */
        .input-wrapper input[type="password"] {
            padding: 7px 38px 7px 34px;  /* extra right padding for eye icon */
        }
        
        .input-wrapper input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(26, 92, 46, 0.08);
        }
        .input-wrapper input::placeholder { color: #9ca3af; font-size: 12px; }
        
        .input-wrapper i.input-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
            transition: color 0.25s;
            pointer-events: none;
        }
        .input-wrapper input:focus ~ i.input-icon { color: var(--primary-green); }

        .password-toggle {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 15px;
            padding: 4px;
            width: 28px;
            height: 28px;
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

        .btn-register {
            width: 100%;
            padding: 9px;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 2px 8px rgba(26, 92, 46, 0.25);
            margin-top: 2px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .btn-register:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(26, 92, 46, 0.35);
        }

        .register-footer {
            text-align: center;
            margin-top: 10px;
        }
        .register-footer p { font-size: 11px; color: var(--text-muted); }
        .register-footer a { color: var(--primary-green); text-decoration: none; font-weight: 700; }

        .alert-custom {
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
        }
        .alert-danger-custom { background: rgba(239, 68, 68, 0.08); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.15); }
        .alert-success-custom { background: rgba(16, 185, 129, 0.08); color: #059669; border: 1px solid rgba(16, 185, 129, 0.15); }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeInUp 0.6s ease forwards; }
    </style>
</head>
<body>
    <div class="register-page">
        <div class="register-container animate-in">
            <div class="register-card">
                <div class="register-logo">
                    <div class="logo-circle">
                        <i class="bi bi-flower1"></i>
                    </div>
                    <h2>Create Account</h2>
                    <p>Join TEAMIND - AI Powered Tea Management</p>
                </div>

                <?php if ($error): ?>
                <div class="alert-custom alert-danger-custom">
                    <i class="bi bi-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="alert-custom alert-success-custom">
                    <i class="bi bi-check-circle"></i>
                    <?php echo $success; ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group-custom">
                        <label>Full Name <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="fullname" placeholder="Enter your full name" required
                                   value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
                            <i class="bi bi-person input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label>Email Address <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="email" name="email" placeholder="Enter your email" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <i class="bi bi-envelope input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label>Phone Number</label>
                        <div class="input-wrapper">
                            <input type="tel" name="phone" placeholder="Enter your phone number"
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            <i class="bi bi-telephone input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label>Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" placeholder="Min 6 characters" required>
                            <i class="bi bi-lock input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                                <i class="bi bi-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label>Confirm Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
                            <i class="bi bi-lock-fill input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', 'toggleIcon2')">
                                <i class="bi bi-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="bi bi-person-plus"></i>
                        Create Account
                    </button>
                </form>

                <div class="register-footer">
                    <p>Already have an account? <a href="login.php">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const password = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
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