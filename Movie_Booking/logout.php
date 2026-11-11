<?php

session_start();

// Destroy all session data
$_SESSION = array();

session_destroy();

// Go back to login page
header("Location: login.php");
exit();

?>