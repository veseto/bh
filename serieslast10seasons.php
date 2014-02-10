<?php
	include("connection.php");
	$league = $_GET['league'];
	$length = $_GET['length'];
	$res = $mysqli->query("SELECT DISTINCT homeTeam from matches where leagueId=$league");
	$teams = array();
	while ($team = $res->fetch_array()) {
		$t = $team[0];		
		$teams[$t] = array();
		$count = 0;
		$q = "SELECT * FROM matches where (homeTeam='$t' or awayTeam='$t') and leagueId=$league order by matchDate";
		//echo "$q<br>";
		$res1 = $mysqli->query($q);
		//echo "string";
		$i = 0;
		while ($match = $res1->fetch_assoc()) {
			if ($match['resultShort'] == 'D') {
				if ($count > $length) {
					$teams[$t][$i."#".$match['matchDate']] = $count;
					$i ++;
				}
				$count = 0;
			} else {
				$count ++;
			}
		}
	}
	foreach ($teams as $key => $value) {
		foreach ($value as $index => $length) {
			echo "$key $index : $length<br>";
		}
		//echo "$key : $value<br>";
	}
?>