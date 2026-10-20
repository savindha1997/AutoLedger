<?php 
session_start();
include('dbconfig.php');

if (isset($_SESSION['user_name'])) {
	system_log($con, 'User logout', 'auth', 'username:' . $_SESSION['user_name'], isset($_SESSION['id']) ? $_SESSION['id'] : '');
}

session_unset();
session_destroy();

header("Location: login.php");

?>