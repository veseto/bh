<?php
	include("connection.php");
	$res = $mysqli->query("SELECT * from leagueDetails");
	while ($l = $res->fetch_assoc()) {
		echo $l['leagueId']." ".$l['country']." ".$l['name']."<br>";
	}
?>