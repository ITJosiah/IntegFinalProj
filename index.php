<?php
session_start();
if (isset($_GET['role'])) {
    $role = $_GET['role'];
    if ($role === 'admin') {
        $_SESSION['user_role'] = 'admin';
        $_SESSION['user_name'] = 'Josiah Luke (Admin)';
        header('Location: admin_dashboard.php');
        exit;
    } elseif ($role === 'customer') {
        $_SESSION['user_role'] = 'customer';
        $_SESSION['user_name'] = 'Basud Resident';
        header('Location: customer_dashboard.php');
        exit;
    } elseif (in_array($role, ['laurents', 'jrmp', 'jas5'])) {
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
    </style>
</head>
<body>

    <div class="minimal-container animate-fade">
        <div class="login-box">
            <h1 style="font-size: 2rem; font-weight: 800; color: var(--primary);">Pharma<span style="color: black;">Sync</span></h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Select User Login</p>

            <div class="btn-group">
                <!-- User 1: Admin Button -->
                <a href="index.php?role=admin" class="btn btn-outline" style="width:100%; padding: 1rem; font-size: 1.1rem;">Admin</a>

                <!-- User 2: Customer Button -->
                <a href="index.php?role=customer" class="btn btn-outline" style="width:100%; padding: 1rem; font-size: 1.1rem;">Customer</a>

                <!-- User 3: Pharmacy Button Toggle -->
                <button onclick="togglePharmaButtons()" class="btn btn-primary" style="width:100%; padding: 1rem; font-size: 1.1rem;">Pharmacy ▼</button>

                <!-- 3 Pharmacy Sub-buttons -->
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
            if (group.style.display === 'flex') {
                group.style.display = 'none';
            } else {
                group.style.display = 'flex';
            }
        }
    </script>
</body>
</html>
