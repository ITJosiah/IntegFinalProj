<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set defaults for guests (no session / not logged in)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id']       = null;
    $_SESSION['user_name']     = 'Guest';
    $_SESSION['user_location'] = 'Basud, Camarines Norte';
}

// Determine if user is a logged-in customer or guest
$isLoggedInCustomer = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer');
$isGuest = !isset($_SESSION['user_role']);

$hour = (int) date('H');
if ($hour < 12) {
    $greeting = 'Good morning';
} elseif ($hour < 18) {
    $greeting = 'Good afternoon';
} else {
    $greeting = 'Good evening';
}
?>