<?php
	session_start();
	include("connection.php");
	$type = 0;
	if ($_GET['tab'] === 'dep') {
		$type = 1;
	} else if ($_GET['tab'] === 'wit') {
		$type = 2;
	}
	if (isset($_GET['run']) && $_GET['run'] === 'new') {
	    newLine($_SESSION['uid'], $type);
	} else  if(isset($_GET['run']) && $_GET['run'] == 'check'){
		$id=$_POST['id'];
		$checked = $_POST['checked'];
		if ($_GET['tab'] == 'dep' && $checked == 'true') {
			$type = 3;
		} else if ($_GET['tab'] == 'wit' && $checked == 'true') {
			$type = 4;
		}
	  	$mysqli->query("UPDATE money set type=$type where tId=$id");
	  	echo "OK";
	} else {
		$i = $_POST['column'];
		$val = $_POST['value'];
		$id = $_POST['row_id']; 
		switch ($i) {
		    case 1:
		        $mysqli->query("UPDATE money set tDate='$val' where tId=$id");
		        break;
		    case 2:
		        $mysqli->query("UPDATE money set tTime='$val' where tId=$id");
		        break;
		    case 3:
		        $mysqli->query("UPDATE money set source='$val' where tId=$id");
		        break;
		    case 4:
		        $mysqli->query("UPDATE money set amount='$val' where tId=$id");
		        break;
		}
		echo "$val";
	}
	function newLine($userId, $type){
		include("connection.php");
		$d = date("Y-m-d");
		$t = date("H:i:s");
		$q = "INSERT INTO money (type, userId, amount, source, tdate, tTime) values ($type, $userId, 0, ' ', '$d', '$t')";
		echo "$q";
		$mysqli->query($q);
			header("Location: stats.php?tab=".$_GET['tab']);
		}
?>