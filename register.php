<?php
// register.php
// Customer Registration — PharmaSync

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'api/config/db.php';

$error = '';
$errors = [];

// If already logged in, redirect
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: admin_dashboard.php');
        exit;
    } elseif ($_SESSION['user_role'] === 'pharmacy') {
        header('Location: pharmacy_dashboard.php?pharma=' . $_SESSION['pharma_code']);
        exit;
    } elseif ($_SESSION['user_role'] === 'customer') {
        header('Location: customer_dashboard.php');
        exit;
    }
}

// Retain form values on error
$form = [
    'full_name' => '',
    'email'     => '',
    'username'  => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['full_name'] = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $form['email']     = isset($_POST['email']) ? trim($_POST['email']) : '';
    $form['username']  = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password          = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword   = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validate
    if (empty($form['full_name'])) {
        $errors[] = 'Full name is required.';
    }
    if (empty($form['email']) || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if (empty($form['username']) || strlen($form['username']) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        try {
            $db = getDBConnection('pharmasync_core');

            // Check for existing username across all tables
            $stmt = $db->prepare("SELECT COUNT(*) FROM customers WHERE username = :u");
            $stmt->execute(['u' => $form['username']]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'This username is already taken.';
            }

            $stmt = $db->prepare("SELECT COUNT(*) FROM admins WHERE username = :u");
            $stmt->execute(['u' => $form['username']]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'This username is already taken.';
            }

            $stmt = $db->prepare("SELECT COUNT(*) FROM pharmacies WHERE username = :u");
            $stmt->execute(['u' => $form['username']]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'This username is already taken.';
            }

            // Check for existing email
            $stmt = $db->prepare("SELECT COUNT(*) FROM customers WHERE email = :e");
            $stmt->execute(['e' => $form['email']]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'An account with this email already exists.';
            }

            if (empty($errors)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $db->prepare("INSERT INTO customers (full_name, username, email, password_hash) VALUES (:fn, :un, :em, :ph)");
                $stmt->execute([
                    'fn' => $form['full_name'],
                    'un' => $form['username'],
                    'em' => $form['email'],
                    'ph' => $hash,
                ]);

                header('Location: index.php?msg=registered');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — PharmaSync</title>
    <meta name="description" content="Register a customer account on PharmaSync to access location-based pharmacy features.">
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        .gateway-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 50%, #EFF6FF 100%);
            padding: 2rem 1rem;
            position: relative;
            overflow: hidden;
        }

        .gateway-wrapper::before,
        .gateway-wrapper::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: float 8s ease-in-out infinite;
        }
        .gateway-wrapper::before {
            width: 400px;
            height: 400px;
            background: rgba(37, 99, 235, 0.15);
            top: -100px;
            left: -100px;
        }
        .gateway-wrapper::after {
            width: 350px;
            height: 350px;
            background: rgba(59, 130, 246, 0.1);
            bottom: -80px;
            right: -80px;
            animation-delay: -4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
        }

        .gateway-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px -15px rgba(15, 23, 42, 0.15), 0 4px 25px -5px rgba(15, 23, 42, 0.08);
            max-width: 460px;
            width: 100%;
            border: 1px solid rgba(226, 232, 240, 0.8);
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .gateway-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10B981 0%, #3B82F6 50%, var(--primary) 100%);
        }

        .gateway-header {
            padding: 2.5rem 2.5rem 0.75rem;
            text-align: center;
        }

        .gateway-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.35rem;
            text-decoration: none;
        }

        .gateway-logo-text {
            font-size: 1.75rem;
            font-weight: 900;
            letter-spacing: -0.025em;
            color: var(--primary);
        }

        .gateway-logo-text .sync {
            color: #0F172A;
        }

        .gateway-subtitle {
            color: var(--text-muted);
            font-size: 0.88rem;
            font-weight: 500;
            margin: 0.25rem 0 0;
        }

        .gateway-form-area {
            padding: 1.5rem 2.5rem 1.5rem;
        }

        /* Alerts */
        .alert-banner {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.825rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
            animation: slideIn 0.3s ease-out;
        }

        .alert-banner-error {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }

        .alert-banner-error ul {
            margin: 0.25rem 0 0 1rem;
            padding: 0;
            list-style: disc;
        }

        .alert-banner-error ul li {
            margin-bottom: 0.15rem;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Input Groups */
        .input-group {
            position: relative;
            margin-bottom: 0.875rem;
        }

        .input-group svg {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            pointer-events: none;
            transition: var(--transition);
        }

        .input-field {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.75rem;
            border: 1.5px solid #E2E8F0;
            background: #F8FAFC;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            color: var(--text-main);
            outline: none;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .input-field:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
        }

        .input-field:focus + svg {
            color: var(--primary);
        }

        .input-field::placeholder {
            color: #94A3B8;
        }

        .input-hint {
            font-size: 0.72rem;
            color: #94A3B8;
            margin-top: 0.25rem;
            padding-left: 0.25rem;
        }

        /* Submit Button */
        .btn-register {
            width: 100%;
            padding: 0.85rem;
            font-size: 0.95rem;
            font-weight: 700;
            border-radius: 0.75rem;
            margin-top: 0.75rem;
            background: linear-gradient(135deg, var(--primary) 0%, #3B82F6 100%);
            color: white;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.01em;
        }

        .btn-register:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        /* Footer */
        .gateway-footer {
            padding: 0 2.5rem 2rem;
            text-align: center;
        }

        .gateway-footer-link {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .gateway-footer-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .gateway-footer-link a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        /* Password strength indicator */
        .password-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        @media (max-width: 480px) {
            .password-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="gateway-wrapper">
        <div class="gateway-card animate-fade">

            <!-- Header -->
            <div class="gateway-header">
                <a href="index.php" class="gateway-logo">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--primary)"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    <span class="gateway-logo-text">Pharma<span class="sync">Sync</span></span>
                </a>
                <p class="gateway-subtitle">Create your customer account</p>
            </div>

            <!-- Registration Form -->
            <div class="gateway-form-area">

                <?php if (!empty($errors)): ?>
                    <div class="alert-banner alert-banner-error">
                        <span>⚠️</span>
                        <div>
                            <ul>
                                <?php foreach ($errors as $err): ?>
                                    <li><?php echo htmlspecialchars($err); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php" id="registerForm">

                    <!-- Full Name -->
                    <div class="input-group">
                        <input type="text" name="full_name" class="input-field" placeholder="Full Name" required
                            autocomplete="name" id="regFullName"
                            value="<?php echo htmlspecialchars($form['full_name']); ?>">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>

                    <!-- Email -->
                    <div class="input-group">
                        <input type="email" name="email" class="input-field" placeholder="Email Address" required
                            autocomplete="email" id="regEmail"
                            value="<?php echo htmlspecialchars($form['email']); ?>">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>

                    <!-- Username -->
                    <div class="input-group">
                        <input type="text" name="username" class="input-field" placeholder="Username" required
                            autocomplete="username" id="regUsername" minlength="3"
                            value="<?php echo htmlspecialchars($form['username']); ?>">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94"></path>
                            <path d="M1 20s3-4 7-4 7 4 7 4"></path>
                            <circle cx="8" cy="9" r="4"></circle>
                        </svg>
                    </div>
                    <p class="input-hint">Min. 3 characters. Must be unique.</p>

                    <!-- Password Row -->
                    <div class="password-row" style="margin-top: 0.75rem;">
                        <div class="input-group">
                            <input type="password" name="password" class="input-field" placeholder="Password" required
                                autocomplete="new-password" id="regPassword" minlength="6">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>

                        <div class="input-group">
                            <input type="password" name="confirm_password" class="input-field" placeholder="Confirm" required
                                autocomplete="new-password" id="regConfirmPassword" minlength="6">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                    </div>
                    <p class="input-hint">Min. 6 characters.</p>

                    <button type="submit" class="btn-register" id="btnRegister">Create Account</button>
                </form>
            </div>

            <!-- Sign In link -->
            <div class="gateway-footer">
                <p class="gateway-footer-link">
                    Already have an account? <a href="index.php">Sign in</a>
                </p>
            </div>

        </div>
    </div>

</body>

</html>
