<?php
// index.php
// Unified Authentication Gateway — PharmaSync Middleware Console
// Single login form auto-detects role: admin, pharmacy, or customer.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'api/config/db.php';

$error = '';
$success = '';

// Check if user is already logged in and redirect to their respective dashboard
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

// Capture feedback messages from redirects
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'unauthorized') {
        $error = 'Access denied. You must be authenticated to access that portal.';
    } elseif ($_GET['error'] === 'invalid_session') {
        $error = 'Your session has expired or is invalid. Please log in again.';
    }
}
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'logged_out') {
        $success = 'You have logged out successfully. Have a nice day!';
    } elseif ($_GET['msg'] === 'registered') {
        $success = 'Account created successfully! Please sign in with your credentials.';
    }
}

// Handle unified login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error = 'Please fill in all credentials.';
    } else {
        try {
            $db = getDBConnection('pharmasync_core');
            $matched = false;

            // 1. Check admins table
            $stmt = $db->prepare("SELECT * FROM admins WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                if (isset($_POST['remember'])) {
                    setcookie('remember_user', $username, time() + (86400 * 30), "/");
                } else {
                    setcookie('remember_user', '', time() - 3600, "/");
                }
                $_SESSION['user_role'] = 'admin';
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_location'] = 'Central Administration';
                header('Location: admin_dashboard.php');
                exit;
            }

            // 2. Check pharmacies table
            $stmt = $db->prepare("SELECT * FROM pharmacies WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                if (isset($_POST['remember'])) {
                    setcookie('remember_user', $username, time() + (86400 * 30), "/");
                } else {
                    setcookie('remember_user', '', time() - 3600, "/");
                }
                $pharmaCode = $user['code'];
                $_SESSION['user_role'] = 'pharmacy';
                $_SESSION['pharma_code'] = $pharmaCode;
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_location'] = $user['address'];
                header('Location: pharmacy_dashboard.php?pharma=' . $pharmaCode);
                exit;
            }

            // 3. Check customers table
            $stmt = $db->prepare("SELECT * FROM customers WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                if (isset($_POST['remember'])) {
                    setcookie('remember_user', $username, time() + (86400 * 30), "/");
                } else {
                    setcookie('remember_user', '', time() - 3600, "/");
                }
                $_SESSION['user_role'] = 'customer';
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_location'] = 'Basud, Camarines Norte';
                if ($user['latitude'] && $user['longitude']) {
                    $_SESSION['user_lat'] = $user['latitude'];
                    $_SESSION['user_lng'] = $user['longitude'];
                }
                header('Location: customer_dashboard.php');
                exit;
            }

            // No match found
            $error = 'Invalid credentials. Please check your username and password.';

        } catch (Exception $e) {
            $error = 'Authentication failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — PharmaSync</title>
    <meta name="description" content="Sign in to PharmaSync — Middleware Coordination Console & Catalog for Basud, Camarines Norte pharmacies.">
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

        /* Subtle animated background orbs */
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
            right: -100px;
        }
        .gateway-wrapper::after {
            width: 350px;
            height: 350px;
            background: rgba(59, 130, 246, 0.1);
            bottom: -80px;
            left: -80px;
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
            max-width: 440px;
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
            background: linear-gradient(90deg, var(--primary) 0%, #3B82F6 50%, #60A5FA 100%);
        }

        .gateway-header {
            padding: 2.75rem 2.5rem 1rem;
            text-align: center;
        }

        .gateway-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.5rem;
        }

        .gateway-logo-text {
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: -0.025em;
            color: var(--primary);
        }

        .gateway-logo-text .sync {
            color: #0F172A;
        }

        .gateway-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0;
        }

        .gateway-form-area {
            padding: 1.75rem 2.5rem 2rem;
        }

        /* Alerts */
        .alert-banner {
            padding: 0.8rem 1.15rem;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
            animation: slideIn 0.3s ease-out;
        }

        .alert-banner-error {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }

        .alert-banner-success {
            background: #D1FAE5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Input Groups */
        .input-group {
            position: relative;
            margin-bottom: 1rem;
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
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            border: 1.5px solid #E2E8F0;
            background: #F8FAFC;
            border-radius: 0.75rem;
            font-size: 0.925rem;
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

        /* Submit Button */
        .btn-sign-in {
            width: 100%;
            padding: 0.875rem;
            font-size: 0.95rem;
            font-weight: 700;
            border-radius: 0.75rem;
            margin-top: 0.5rem;
            background: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.01em;
        }

        .btn-sign-in:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
        }

        .btn-sign-in:active {
            transform: translateY(0);
        }

        /* Divider */
        .gateway-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.25rem 0;
        }

        .gateway-divider::before,
        .gateway-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E2E8F0;
        }

        .gateway-divider span {
            font-size: 0.75rem;
            font-weight: 600;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Guest Button */
        .btn-guest {
            width: 100%;
            padding: 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 0.75rem;
            background: #F8FAFC;
            color: #475569;
            border: 1.5px solid #E2E8F0;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-guest:hover {
            background: #EFF6FF;
            border-color: #BFDBFE;
            color: var(--primary);
            transform: translateY(-1px);
        }

        /* Footer links */
        .gateway-footer {
            padding: 0 2.5rem 2.25rem;
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

    </style>
</head>

<body>

    <div class="gateway-wrapper">
        <div class="gateway-card animate-fade">

            <!-- Header / Branding -->
            <div class="gateway-header">
                <div class="gateway-logo">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--primary)"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    <span class="gateway-logo-text">Pharma<span class="sync">Sync</span></span>
                </div>
                <p class="gateway-subtitle">Middleware Coordination Console & Catalog</p>
            </div>

            <!-- Unified Login Form -->
            <div class="gateway-form-area">

                <?php if (!empty($error)): ?>
                    <div class="alert-banner alert-banner-error">
                        <span>⚠️</span>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert-banner alert-banner-success">
                        <span>✅</span>
                        <div><?php echo htmlspecialchars($success); ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php" id="loginForm">

                    <div class="input-group">
                        <input type="text" name="username" class="input-field" placeholder="Username" required
                            autocomplete="username" id="loginUsername"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : (isset($_COOKIE['remember_user']) ? htmlspecialchars($_COOKIE['remember_user']) : ''); ?>">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>

                    <div class="input-group">
                        <input type="password" name="password" class="input-field" placeholder="Password" required
                            autocomplete="current-password" id="loginPassword">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>

                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember" <?php echo isset($_COOKIE['remember_user']) ? 'checked' : ''; ?>>
                        <label for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="btn-sign-in" id="btnSignIn">Sign In</button>
                </form>

                <!-- Guest Access Divider -->
                <div class="gateway-divider">
                    <span>or</span>
                </div>

                <a href="customer_dashboard.php" class="btn-guest" id="btnGuest">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Continue as Guest
                </a>

            </div>

            <!-- Register link -->
            <div class="gateway-footer">
                <p class="gateway-footer-link">
                    Don't have an account? <a href="register.php">Create one</a>
                </p>
            </div>

        </div>
    </div>

</body>

</html>