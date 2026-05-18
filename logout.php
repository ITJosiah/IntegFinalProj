<?php
// logout.php
// Securely terminates the active user session and redirects to the portal gateway.

session_start();

// Unset all session variables
$_SESSION = [];

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect back to gateway with a logout success message
header("Location: index.php?msg=logged_out");
exit;
?>
