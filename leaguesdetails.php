<?php
	include("connection.php");
	include("includes/header.php");
	$res = $mysqli->query("select * from leagueDetails order by leagueId");
	while ($l = $res->fetch_assoc()) {
		echo $l['leagueId']." ".$l['country']." ".$l['name']."<br>";
	}
	include("includes/footer.php");
?>