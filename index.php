<?php
session_start();

$host = 'localhost';
$db   = 'pharmasync_core';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die("Connection failed: " . $e->getMessage());
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['customer_login'])) {
        $full_name = trim($_POST['full_name']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare('SELECT * FROM customers WHERE full_name = ?');
        $stmt->execute([$full_name]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_role'] = 'customer';
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            header('Location: customer_dashboard.php');
            exit;
        } else {
            $login_error = 'Invalid Full Name or password.';
        }
    } elseif (isset($_POST['admin_login'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['user_role'] = 'admin';
            $_SESSION['user_name'] = 'Josiah Luke (Admin)';
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $login_error = 'Invalid Admin username or password.';
        }
    }
}

if (isset($_GET['role']) && $_GET['role'] !== 'admin') {
    $role = $_GET['role'];
    if (in_array($role, ['laurents', 'jrmp', 'jas5'])) {
        $_SESSION['user_role'] = 'pharmacy';
        $_SESSION['pharma_code'] = $role;
        $names = [
            'laurents' => "Laurent's Pharmacy",
            'jrmp' => "JRMP Doctors Pharmacy",
            'jas5' => "JAS5 Pharmacy"
        ];
        $_SESSION['user_name'] = $names[$role];
        header('Location: pharmacy_dashboard.php?pharma=' . $role);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Selection - PharmaSync</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        .minimal-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F8FAFC;
            padding: 1rem;
        }
        .login-box {
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            max-width: 450px;
            width: 100%;
            text-align: center;
            border: 1px solid #E2E8F0;
        }
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 2rem;
        }
        .pharma-group {
            display: none;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 0.5rem;
            padding: 1rem;
            background: #EFF6FF;
            border-radius: 0.5rem;
            border: 1px solid #BFDBFE;
        }
        .customer-login-box, .admin-login-box {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background: #F8FAFC;
            border-radius: 0.5rem;
            border: 1px solid #E2E8F0;
            text-align: left;
        }
        .alert {
            padding: 0.5rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }
    </style>
</head>
<body>

    <div class="minimal-container animate-fade">
        <div class="login-box">
            <h1 style="font-size: 2rem; font-weight: 800; color: var(--primary);">Pharma<span style="color: black;">Sync</span></h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Select User Login</p>

            <?php if (!empty($login_error)): ?>
                <div class="alert"><?php echo htmlspecialchars($login_error); ?></div>
            <?php endif; ?>

            <div class="btn-group">
                <button onclick="toggleAdminLogin()" class="btn btn-outline" style="width:100%; padding: 1rem; font-size: 1.1rem;">Admin ▼</button>
                
                <div id="adminLoginBox" class="admin-login-box">
                    <form action="index.php" method="POST">
                        <input type="hidden" name="admin_login" value="1">
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" style="font-size: 0.85rem;">Username</label>
                            <input type="text" name="username" class="form-input" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label" style="font-size: 0.85rem;">Password</label>
                            <input type="password" name="password" class="form-input" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width:100%; padding:0.5rem;">Log In as Admin</button>
                    </form>
                </div>

                <button onclick="toggleCustomerLogin()" class="btn btn-outline" style="width:100%; padding: 1rem; font-size: 1.1rem;">Customer ▼</button>
                
                <div id="customerLoginBox" class="customer-login-box" <?php echo !empty($login_error) && isset($_POST['customer_login']) ? 'style="display:block;"' : ''; ?>>
                    <form action="index.php" method="POST">
                        <input type="hidden" name="customer_login" value="1">
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" style="font-size: 0.85rem;">Full Name</label>
                            <input type="text" name="full_name" class="form-input" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label class="form-label" style="font-size: 0.85rem;">Password</label>
                            <input type="password" name="password" class="form-input" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width:100%; padding:0.5rem;">Log In</button>
                    </form>
                    <div style="margin-top: 0.75rem; text-align: center;">
                        <a href="customer_register.php" style="font-size: 0.85rem; color: var(--primary); text-decoration: none; font-weight: 600;">No account? click here</a>
                    </div>
                </div>

                <button onclick="togglePharmaButtons()" class="btn btn-primary" style="width:100%; padding: 1rem; font-size: 1.1rem;">Pharmacy ▼</button>

                <div id="pharmaButtons" class="pharma-group">
                    <a href="index.php?role=laurents" class="btn btn-secondary">Laurent's Pharmacy</a>
                    <a href="index.php?role=jrmp" class="btn btn-secondary">JRMP Doctors Pharmacy</a>
                    <a href="index.php?role=jas5" class="btn btn-secondary">JAS5 Pharmacy</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePharmaButtons() {
            const group = document.getElementById('pharmaButtons');
            const custGroup = document.getElementById('customerLoginBox');
            const adminGroup = document.getElementById('adminLoginBox');
            custGroup.style.display = 'none';
            adminGroup.style.display = 'none';
            if (group.style.display === 'flex') {
                group.style.display = 'none';
            } else {
                group.style.display = 'flex';
            }
        }

        function toggleCustomerLogin() {
            const group = document.getElementById('customerLoginBox');
            const pharmaGroup = document.getElementById('pharmaButtons');
            const adminGroup = document.getElementById('adminLoginBox');
            pharmaGroup.style.display = 'none';
            adminGroup.style.display = 'none';
            if (group.style.display === 'block') {
                group.style.display = 'none';
            } else {
                group.style.display = 'block';
            }
        }

        function toggleAdminLogin() {
            const group = document.getElementById('adminLoginBox');
            const pharmaGroup = document.getElementById('pharmaButtons');
            const custGroup = document.getElementById('customerLoginBox');
            pharmaGroup.style.display = 'none';
            custGroup.style.display = 'none';
            if (group.style.display === 'block') {
                group.style.display = 'none';
            } else {
                group.style.display = 'block';
            }
        }
    </script>
</body>
</html>