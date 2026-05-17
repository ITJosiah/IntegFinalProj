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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM customers WHERE full_name = ?');
        $stmt->execute([$full_name]);
        if ($stmt->fetch()) {
            $error = 'This Full Name is already registered.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO customers (full_name, email, password) VALUES (?, ?, ?)');
            if ($stmt->execute([$full_name, $email, $hashed_password])) {
                $success = 'Account created successfully! You can now log in.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration - PharmaSync</title>
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
            border: 1px solid #E2E8F0;
        }
        .alert {
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }
        .alert-success {
            background: #D1FAE5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }
    </style>
</head>
<body>
    <div class="minimal-container">
        <div class="login-box">
            <h1 style="font-size: 2rem; font-weight: 800; color: var(--primary); text-align: center;">Pharma<span style="color: black;">Sync</span></h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem; margin-bottom: 2rem; text-align: center;">Create Customer Account</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="customer_register.php" method="POST">
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-input" placeholder="e.g. Juan Dela Cruz" required>
                </div>
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" placeholder="name@example.com" required>
                </div>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1.05rem;">Register Account</button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem;">
                <a href="index.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">← Back to Login Selection</a>
            </div>
        </div>
    </div>
</body>
</html>