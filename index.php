<?php
// index.php
// Premium Unified Authentication & Gateway Router for PharmaSync Middleware Console

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
if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out') {
    $success = 'You have logged out successfully. Have a nice day!';
}


// Handle login submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = isset($_POST['role']) ? $_POST['role'] : ''; // 'admin' or 'pharmacy'
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error = 'Please fill in all credentials.';
    } else {
        try {
            $db = getDBConnection('pharmasync_core');

            if ($role === 'admin') {
                $stmt = $db->prepare("SELECT * FROM admins WHERE username = :username");
                $stmt->execute(['username' => $username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password_hash'])) {
                    // Establish Admin Session
                    $_SESSION['user_role'] = 'admin';
                    $_SESSION['user_id'] = $admin['id'];
                    $_SESSION['user_name'] = $admin['name'];
                    $_SESSION['user_location'] = 'Central Administration';

                    header('Location: admin_dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid administrator credentials. Please check your username/password.';
                }
            } elseif ($role === 'pharmacy') {
                // Match by unique username directly
                $stmt = $db->prepare("SELECT * FROM pharmacies WHERE username = :username");
                $stmt->execute(['username' => $username]);
                $pharmacy = $stmt->fetch();

                if ($pharmacy && password_verify($password, $pharmacy['password_hash'])) {
                    // Automatically resolve the active pharmacy code context from the database row
                    $pharmaCode = $pharmacy['code'];

                    // Establish Pharmacy Session
                    $_SESSION['user_role'] = 'pharmacy';
                    $_SESSION['pharma_code'] = $pharmaCode;
                    $_SESSION['user_name'] = $pharmacy['name'];
                    $_SESSION['user_location'] = $pharmacy['address'];

                    header('Location: pharmacy_dashboard.php?pharma=' . $pharmaCode);
                    exit;
                } else {
                    $error = 'Invalid pharmacy credentials. Please check your username/password.';
                }
            } else {
                $error = 'Unknown authentication role specified.';
            }
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
    <title>Gateway Portal - PharmaSync</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        .gateway-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
            padding: 2rem 1rem;
        }

        .gateway-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.15);
            max-width: 480px;
            width: 100%;
            border: 1px solid rgba(226, 232, 240, 0.8);
            overflow: hidden;
            position: relative;
            transition: var(--transition);
        }

        .gateway-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary) 0%, #3B82F6 100%);
        }

        .gateway-header {
            padding: 3rem 2.5rem 1.5rem;
            text-align: center;
        }

        .gateway-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            background: #F1F5F9;
            padding: 0.35rem;
            border-radius: 0.75rem;
            margin: 0 2.5rem 2rem;
            border: 1px solid #E2E8F0;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 0.65rem 0;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 0.5rem;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
        }

        .tab-btn svg {
            width: 16px;
            height: 16px;
            stroke-width: 2.2;
        }

        .tab-btn:hover {
            color: var(--primary);
        }

        .tab-btn.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .gateway-forms {
            padding: 0 2.5rem 3rem;
        }

        .form-pane {
            display: none;
        }

        .form-pane.active {
            display: block;
        }

        .alert-banner {
            padding: 0.85rem 1.25rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
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

        .customer-card-box {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            padding: 1.75rem;
            border-radius: 1rem;
            text-align: center;
            box-shadow: inset 0 2px 4px rgba(37, 99, 235, 0.02);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Float Focus Effect for Form Fields */
        .premium-input-group {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .premium-input-group svg {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            transition: var(--transition);
        }

        .premium-input {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            border: 1px solid var(--border-color);
            background: #F8FAFC;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            color: var(--text-main);
            outline: none;
            transition: var(--transition);
        }

        .premium-input:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .premium-input:focus+svg {
            color: var(--primary);
        }

        .premium-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748B' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem;
            padding-right: 2.5rem;
        }

        .btn-full {
            width: 100%;
            padding: 0.875rem;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 0.75rem;
            margin-top: 0.5rem;
        }
    </style>
</head>

<body>

    <div class="gateway-wrapper">
        <div class="gateway-card animate-fade">

            <div class="gateway-header">
                <div style="display:inline-flex; align-items:center; gap:0.4rem; margin-bottom: 0.5rem;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--primary)"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    <span
                        style="font-size: 2.25rem; font-weight: 900; letter-spacing: -0.025em; color: var(--primary);">Pharma<span
                            style="color: black;">Sync</span></span>
                </div>
                <p style="color: var(--text-muted); font-size: 0.95rem; font-weight: 500;">Middleware Coordination
                    Console & Catalog</p>
            </div>

            <!-- Role Selector Tabs -->
            <div class="gateway-tabs">
                <button type="button" id="tabCustomer" class="tab-btn active" onclick="switchPane('customer')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Customer
                </button>
                <button type="button" id="tabPharmacy" class="tab-btn" onclick="switchPane('pharmacy')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <path d="M9 9h6v6H9z"></path>
                    </svg>
                    Pharmacy
                </button>
                <button type="button" id="tabAdmin" class="tab-btn" onclick="switchPane('admin')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                        <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                        <line x1="6" y1="6" x2="6.01" y2="6"></line>
                        <line x1="6" y1="18" x2="6.01" y2="18"></line>
                    </svg>
                    Admin
                </button>
            </div>

            <!-- Gateway Forms container -->
            <div class="gateway-forms">

                <!-- Feedback Messages -->
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

                <!-- 1. CUSTOMER GATEWAY -->
                <div id="paneCustomer" class="form-pane active">
                    <div class="customer-card-box">
                        <h3 style="font-weight: 800; color: var(--primary); font-size: 1.15rem; margin-bottom: 0.5rem;">
                            Public Resident Catalog</h3>
                        <p style="font-size: 0.9rem; color: #475569; line-height: 1.5; margin-bottom: 1.5rem;">Search
                            local Basud pharmacy inventory, explore real-time medicine locations, and leave community
                            inquiries.</p>
                        <a href="customer_dashboard.php" class="btn btn-primary btn-full"
                            style="text-decoration: none; justify-content: center;">Enter Public Catalog</a>
                    </div>
                </div>

                <!-- 2. PHARMACY LOGIN -->
                <div id="panePharmacy" class="form-pane">
                    <form method="POST" action="index.php">
                        <input type="hidden" name="role" value="pharmacy">

                        <div class="premium-input-group">
                            <input type="text" name="username" class="premium-input" placeholder="Pharmacy Username"
                                required autocomplete="username">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>

                        <div class="premium-input-group" style="margin-bottom: 1.5rem;">
                            <input type="password" name="password" class="premium-input" placeholder="Password" required
                                autocomplete="current-password">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">Access Inventory Portal</button>
                    </form>
                </div>

                <!-- 3. ADMIN LOGIN -->
                <div id="paneAdmin" class="form-pane">
                    <form method="POST" action="index.php">
                        <input type="hidden" name="role" value="admin">

                        <div class="premium-input-group">
                            <input type="text" name="username" class="premium-input" placeholder="Admin Username"
                                required autocomplete="username">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>

                        <div class="premium-input-group" style="margin-bottom: 1.5rem;">
                            <input type="password" name="password" class="premium-input" placeholder="Password" required
                                autocomplete="current-password">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">Open Executive Console</button>
                    </form>
                </div>

            </div>

        </div>
    </div>

    <script>
        function switchPane(role) {
            // Unactivate all tabs and forms
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.form-pane').forEach(form => form.classList.remove('active'));

            // Activate the selected tab
            if (role === 'customer') {
                document.getElementById('tabCustomer').classList.add('active');
                document.getElementById('paneCustomer').classList.add('active');
            } else if (role === 'pharmacy') {
                document.getElementById('tabPharmacy').classList.add('active');
                document.getElementById('panePharmacy').classList.add('active');
            } else if (role === 'admin') {
                document.getElementById('tabAdmin').classList.add('active');
                document.getElementById('paneAdmin').classList.add('active');
            }

            // Save active tab in local storage to keep state after validation reload
            localStorage.setItem('active_role_tab', role);
        }

        // Restore last active tab on page load
        window.addEventListener('DOMContentLoaded', () => {
            const lastActive = localStorage.getItem('active_role_tab');
            // If there's an active error or success banner, default to the tab where the action likely took place
            <?php if (!empty($error) || !empty($success)): ?>
                const currentRole = '<?php echo isset($_POST["role"]) ? $_POST["role"] : ""; ?>';
                if (currentRole === 'admin' || currentRole === 'pharmacy') {
                    switchPane(currentRole);
                    return;
                }
            <?php endif; ?>

            if (lastActive && ['customer', 'pharmacy', 'admin'].includes(lastActive)) {
                switchPane(lastActive);
            }
        });
    </script>
</body>

</html>