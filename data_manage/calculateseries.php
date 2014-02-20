<?php
	include("../connection.php");

	// $res = $mysqli->query("SELECT * FROM series");
	// while ($row = $res->fetch_assoc()) {
	// 	$leagueId = $mysqli->query("SELECT leagueId from matches where homeTeam='".$row['team']."' limit 1")->fetch_array()[0];
	// 	$mysqli->query("update series set leagueId=$leagueId where seriesId=".$row['seriesId']);
	// }


	$settings = $mysqli->query("SELECT leaguesRequirements from userSettings where userId=1")->fetch_array()[0];
	$settings = unserialize($settings);
	//print_r($settings);
	//foreach ($settings as $key => $value) {
	$value=3;
	// $key=27;
		$res = $mysqli->query("SELECT DISTINCT homeTeam FROM matches where season='2013-2014' and leagueId=$key");
		while($team = $res->fetch_array()) {
			$mysqli->query("INsert into series (team, length, active, leagueId) values ('".$team[0]."', 1, 1, $key)");
			
			$q = "SELECT * from matches where leagueId=$key and season='2013-2014' and state<>'Post' and (homeTeam='".$team[0]."' or awayTeam='".$team[0]."') order by matchDate asc";
			$mRes = $mysqli->query($q);
			$previousHasResult=False;
			while ($match = $mRes->fetch_assoc()) {
				$seriesId = $mysqli->query("select seriesId from series where team='".$team[0]."' and active=1")->fetch_array()[0];
				if ($match['resultShort'] === '-') {
					if ($previousHasResult) {
						
						$length = $mysqli->query("SELECT length from series where seriesId=$seriesId")->fetch_array()[0];
						if ($length > $value) {
							for ($i=1; $i<4; $i++) {
								$mysqli->query("insert into playedMatches (userId, matchId, seriesId, odds, bet, ignored, currentLength, pps) 
										values ($i, ".$match['matchId'].", ".$seriesId.", 3, 0, 0, ".$length.", 1)");
							}
						}
						$previousHasResult = false;
					} else {
						break;
					}
				} else {
					//print_r($match);
					$previousHasResult = true;
					$mysqli->query("update series set length=length+1 where seriesId=$seriesId");
					
					if ($match['resultShort'] === 'D') {
						echo "draw! ";
						$mysqli->query("update series set active=0 where seriesId=$seriesId");
						$mysqli->query("INsert into series (team, length, active, leagueId) values ('".$team[0]."', 1, 1, $key)");
					} 
					$seriesId = $mysqli->query("select seriesId from series where team='".$team[0]."' and active=1")->fetch_array()[0];
					$length = $mysqli->query("SELECT length from series where seriesId=$seriesId")->fetch_array()[0];
					$l = $length-1;
					if ($l > $value) {
						for ($i=1; $i<4; $i++) {
							echo "Match to play!";
							$mysqli->query("insert into playedMatches (userId, matchId, seriesId, odds, bet, ignored, currentLength, pps) 
								values ($i, ".$match['matchId'].", ".$seriesId.", 3, 0, 0, ".$l.", 1)");
						}
					}
				}
			}
		}
	//}

?> 	