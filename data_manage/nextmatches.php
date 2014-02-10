<?php
	include("connection.php");
	$settings = $mysqli->query("SELECT leaguesRequirements from userSettings where userId=1")->fetch_array()[0];
	$settings = unserialize($settings);
	$res = $mysqli->query("SELECT * from series where active=1");
	while ($serie = $res->fetch_assoc()) {
		$q1 = "SELECT * FROM `matches` 
				left join series on (series.team=matches.homeTeam or series.team=matches.awayTeam) 
				where matches.resultShort='-' and series.seriesId=".$serie['seriesId']." order by matchDate asc limit 1;";
		$res2 = $mysqli->query($q1);
		$match = $res2->fetch_assoc();
		if ($match) {
			echo $match['length']." > ".$settings[$match['leagueId']]."<br>";
			if ($match['length'] > $settings[$match['leagueId']]) {
				if ($match['homeTeam'] = $match['team']) {
					$mysqli->query("UPDATE matches set homeTeamSeriesID=".$serie['seriesId']);	
				} else if ($match['awayTeam'] = $match['team']) {
					$mysqli->query("UPDATE matches set awayTeamSeriesID=".$serie['seriesId']);	
				}
				$q2="SELECT count(*) from playedMatches where matchId=".$match['matchId'];
				if ($mysqli->query($q2)->fetch_array()[0] === '0') {
					for ($i = 1; $i < 4; $i ++) {
						$mysqli->query("INSERT INTO playedMatches (userId, matchId, currentLength, odds, bet, ignored, seriesId) values ($i, ".$match['matchId'].", ".$match['length'].", 3, 0, 0, ".$match['seriesId'].")");
					}
				}
			}
		} else {
			echo $serie['seriesId']."<br>";
		}
	}
?>