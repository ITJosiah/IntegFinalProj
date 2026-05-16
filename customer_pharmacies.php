<?php
// ── 1. Shared session logic ──
require_once 'includes/session_guard.php';

/** @var string $greeting Provided by includes/session_guard.php */

// ── 2. Set active nav tab for this page ──
$activePage = 'pharmacies';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacies - PharmaDirect</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <!-- Add page-specific CSS later: -->
    <!-- <link rel="stylesheet" href="assets/css/customer_pharmacies.css"> -->
</head>
<body>

<?php require_once 'includes/navbar.php'; ?>

<div class="layout">
    <main class="main-content">

        <div class="hero-section">
            <h1 class="hero-title">Pharmacies</h1>
            <p class="hero-subtitle">
                Browse partner pharmacies across Basud, Camarines Norte.
            </p>
        </div>


    </main>
</div>

<?php require_once 'includes/footer.php'; ?>

</body>
</html>