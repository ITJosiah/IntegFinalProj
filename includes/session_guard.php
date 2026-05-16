<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id']       = 1;
    $_SESSION['user_name']     = 'Alex';
    $_SESSION['user_location'] = 'Basud, Camarines Norte';
}

$hour = (int) date('H');
if ($hour < 12) {
    $greeting = 'Good morning';
} elseif ($hour < 18) {
    $greeting = 'Good afternoon';
} else {
    $greeting = 'Good evening';
}
?>