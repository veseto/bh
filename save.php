<?php
	 session_start();
	 include("connection.php");
	// // print_r($_POST);
	// $mysqli->query("update playedMatches set bet='".$_POST['bet']."', odds='".$_POST['odds']."' where matchId='".$_POST['matchId']."' and seriesId='".$_POST['seriesId']."' and userId=".$_SESSION['uid']);
	
	// //echo $_POST['row']."#".$_POST["bet"]*$_POST['odds'];
	// header("Location: dayview.php?".$_POST['url']);
//print_r($_POST);
	if ($_POST['column'] === 7 || $_POST['column'] ==='8') {
		$q0 = "update playedMatches set betSoFar='".$_POST['value']."' where matchId='".$_POST['row_id']."' and seriesId=".$_POST['id']." and userId=".$_SESSION['uid'];
		//echo $q1;
		$mysqli->query($q0);
	}
	if ($_POST['column'] === 8 || $_POST['column'] ==='9') {
		$q1 = "update playedMatches set bet='".$_POST['value']."', income=playedMatches.odds*".$_POST['value'].", profit=playedMatches.odds*".$_POST['value']."-(playedMatches.betSoFar+".$_POST['value'].") where matchId='".$_POST['row_id']."' and seriesId=".$_POST['id']." and userId=".$_SESSION['uid'];
		//echo $q1;
		$mysqli->query($q1);
	}
	if ($_POST['column'] === 9 || $_POST['column'] ==='10') {
		$mysqli->query("update playedMatches set odds='".$_POST['value']."', income=playedMatches.bet*".$_POST['value'].", profit=playedMatches.bet*".$_POST['value']."-(playedMatches.betSoFar+playedMatches.bet) where matchId='".$_POST['row_id']."' and seriesId=".$_POST['id']." and userId=".$_SESSION['uid']);
	}

	$q = "SELECT * FROM playedMatches where matchId='".$_POST['row_id']."' and seriesId=".$_POST['id']." and userId=".$_SESSION['uid'];
	//echo "$q";
	$played = $mysqli->query($q)->fetch_assoc();

	// print_r($_POST);
	echo $played['bet']."#".$played['odds']."#".$played["income"]."#".$played['profit']."#".$played['betSoFar'];

?>