<?php
include("../connection.php");
$res = $mysqli->query("SELECT * from series where active=1");
	while ($serie = $res->fetch_assoc()) {
		$q1 = "SELECT * FROM `matches` 
				left join series on (series.team=matches.homeTeam or series.team=matches.awayTeam) 
				where matches.resultShort='-' and series.seriesId=".$serie['seriesId']." and state='Sched' order by matchDate asc limit 1;";
		$res2 = $mysqli->query($q1);
		$match = $res2->fetch_assoc();
		if ($match) {
			//echo $match['length']." > ".$settings[$match['leagueId']]."<br>";
					for ($i = 1; $i < 4; $i ++) {
						$q5="SELECT betSoFar, bet FROM playedMatches left join matches on matches.matchId=playedMatches.matchId where userId=$i and seriesId=".$serie['seriesId']." and matchDate<'".$match['matchDate']."' order by matchDate desc limit 1";
						echo "$q5 ";
						$a = $mysqli->query($q5)->fetch_array();
						$betSoFar = $a[0] + $a[1];
						echo "$betSoFar <br>";
						$q7="UPDATE playedMatches set betSoFar=$betSoFar where userId=$i and matchId=".$match['matchId']." and seriesId=".$match['seriesId'];
						$mysqli->query($q7);
						echo $mysqli->error;
					}
				} 
			}
?>