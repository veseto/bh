<?php
	include("/var/www/bh/connection.php");
	$str = "";
	$ids = $mysqli->query("SELECT DISTINCT leagueId from series");
	while ($id = $ids->fetch_array()) {
		$i = $id[0];
		$q0 = "SELECT DISTINCT homeTeam
				FROM matches
				WHERE season =  '2013-2014'
				AND leagueId =$i";
		$res0 = $mysqli->query($q0);
		while ($team = $res0->fetch_array()[0]) {
			$q1 = "SELECT * 
					FROM matches
					WHERE season='2013-2014' AND (homeTeam='$team' OR awayTeam='$team') AND resultShort<>'-'
					ORDER BY matchDate DESC";
			$res1 = $mysqli->query($q1);
			$l = 1;
			while ($match = $res1->fetch_assoc()) {
				if ($match['state'] != 'Abd') {
					if ($match['resultShort'] === 'D') {
						break;
					}
				
					$l ++;
				}
			}
			$q2 = "SELECT length, seriesId 
					FROM series
					WHERE active=1 AND team='$team'";
			$length = $mysqli->query($q2)->fetch_array();
			if ($length != "" && $l != $length[0] && $team != "CLUB AMERICA") {
				$str .= $length[1]." $team -> real length: $l series length: ".$length[0]."<br>";
			}
		}
	}
	if ($str != "") {
		$to      = 'wpopowa@gmail.com';
		$subject = 'Wrong series';
		$message = $str;
		mail($to, $subject, $message);
	}
	echo "$str";
?>
