<?php
session_start();

$_SESSION = array();

session_destroy();

// Redirect to the login page (change 'index.php' to your actual login page)
echo '<script type="text/javascript">window.location.href = "/index.php";</script>';

exit();
?>
