<?php
// 1. Start or resume the session
session_start();

// 2. Unset all of the session variables
$_SESSION = array();

// 3. Destroy the actual session file on the server
session_destroy();

// 4. Redirect the user back to the homepage
header("Location: index.php");
exit();
?>
