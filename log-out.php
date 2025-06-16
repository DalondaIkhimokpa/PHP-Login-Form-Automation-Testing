<?php
// ==============================================
// STEP 4: LOGOUT (log-out.php)
// ==============================================
session_start();

// Set appropriate logout message
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    $_SESSION['logout_message'] = "Admin logged out successfully";
} else {
    $_SESSION['logout_message'] = "Logged out successfully";
}

// Clear all session data
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: log-in.php");
exit();
?>
/*     
LEARNING NOTES:
1. Clears all session data
2. Redirects to welcome page
3. Simple but essential for security
*/
?>