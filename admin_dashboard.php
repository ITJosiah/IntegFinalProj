<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PharmaSync</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="brand" style="display:flex; align-items:center; gap:0.35rem;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                <span><span style="color: var(--primary);">Pharma</span><span style="color: black;">Sync</span></span>
            </a>
            <div class="nav-links">
                <a href="index.php" class="btn btn-outline">Back to Login Selection</a>
            </div>
        </div>
    </nav>

    <main class="container animate-fade" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 60vh; text-align: center;">
        <div class="card" style="max-width: 500px; width: 100%;">
            <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--primary); margin-bottom: 1rem;">System Admin Logged In</h1>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Welcome, Josiah Luke! Login button workflow successfully verified.</p>
            <a href="index.php" class="btn btn-primary">Return to Role Selection</a>
        </div>
    </main>

</body>
</html>
