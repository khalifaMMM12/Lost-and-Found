<?php
// Destroy session and log out user
session_start();
$_SESSION = array();
session_destroy();
header('Location: login.php');
exit; 