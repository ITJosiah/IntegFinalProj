<?php
require_once 'includes/session_guard.php';

/** @var string $greeting Provided by includes/session_guard.php */

$activePage = 'drugs';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Medications - PharmaDirect</title>
    <link rel="stylesheet" href="assets/css/global.css">
</head>
<body>

<?php require_once 'includes/navbar.php'; ?>

<div class="layout">
    <main class="main-content">

        <div class="hero-section">
            <h1 class="hero-title">My Medications</h1>
            <p class="hero-subtitle">
                A complete view of your active and past prescriptions.
            </p>
        </div>


    </main>
</div>

<?php require_once 'includes/footer.php'; ?>

</body>
</html>