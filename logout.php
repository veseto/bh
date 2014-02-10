<?php
	session_start();
	$_SESSION = array();
	setcookie("bh", $user['userId'], time()-3600*2);
	header("Location: index.php");
?>