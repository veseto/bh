<?php
	include("connection.php");

	// $res = $mysqli->query("SELECT * FROM series");
	// while ($row = $res->fetch_assoc()) {
	// 	$leagueId = $mysqli->query("SELECT leagueId from matches where homeTeam='".$row['team']."' limit 1")->fetch_array()[0];
	// 	$mysqli->query("update series set leagueId=$leagueId where seriesId=".$row['seriesId']);
	// }


	$settings = $mysqli->query("SELECT leaguesRequirements from userSettings where userId=1")->fetch_array()[0];
	$settings = unserialize($settings);
	//print_r($settings);
	foreach ($settings as $key => $value) {
		$q="SELECT * FROM series where leagueId=".$key." and length>".$value;
		// echo "$q";
		$res=$mysqli->query($q);
		while($row = $res->fetch_assoc()) {
			$limit = $row['length'] - $value - 1;
			$q1 = "SELECT * from matches where (homeTeamSeriesID=".$row['seriesId']." AND homeTeam='".$row['team']."') OR (awayTeamSeriesID=".$row['seriesId']." and awayTeam='".$row['team']."') order by matchDate DESC limit $limit";
			echo $q1."<br>";
			$res2 = $mysqli->query($q1);
			$currLength = $row['length'] - 1;
			while ($m = $res2->fetch_assoc()) {
				for ($t = 1; $t < 4; $t ++) {
					if ($mysqli->query("SELECT count(*) from playedMatches where userId=".$t." and seriesId=".$row['seriesId']." and matchId=".$m['matchId']) -> fetch_array()[0] === '0') {
						$q2 = "INSERT INTO playedMatches (userId, matchId, currentLength, odds, bet, ignored, seriesId) values ($t, ".$m['matchId'].", $currLength, 3, 0, 0, ".$row['seriesId'].")";
						$mysqli->query($q2);
					} else {
						echo "exists!<br>";
					}
				}
				-- $currLength; 

			}
		}
	}

?> 	