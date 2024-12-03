<?php
session_start();
session_unset();   // Remove all session variables
session_destroy(); // Destroy the session
setcookie(session_name(), '', time() - 3600, '/'); // Delete session cookie (optional)

// Redirect to login page
header("Location: login.php");
exit();
?>
