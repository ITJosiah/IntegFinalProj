<?php
require_once 'includes/session_guard.php';

/** @var string $greeting Provided by includes/session_guard.php */

$activePage = 'appointments';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - PharmaDirect</title>
    <link rel="stylesheet" href="assets/css/global.css">
</head>
<body>

<?php require_once 'includes/navbar.php'; ?>

<div class="layout">
    <main class="main-content">

        <div class="hero-section">
            <h1 class="hero-title">Appointments</h1>
            <p class="hero-subtitle">
                Schedule and manage your consultations with local Basud pharmacists.
            </p>
        </div>


    </main>
</div>

<!-- Shared footer -->
<?php require_once 'includes/footer.php'; ?>

</body>
</html>