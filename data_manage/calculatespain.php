 <?php
	include("../connection.php");

	// $res = $mysqli->query("SELECT * FROM series");
	// while ($row = $res->fetch_assoc()) {
	// 	$leagueId = $mysqli->query("SELECT leagueId from matches where homeTeam='".$row['team']."' limit 1")->fetch_array()[0];
	// 	$mysqli->query("update series set leagueId=$leagueId where seriesId=".$row['seriesId']);
	// }

	$mysqli -> query("INSERT INTO series (team, length, active, leagueId) values ('SPAIN', 1, 1, 23)");

	$q = "SELECT * from matches where leagueId=23 and season='2013-2014' and state<>'Post' order by matchDate asc, matchTime asc";
	$mRes = $mysqli->query($q);
	$previousHasResult=False;
	while ($match = $mRes->fetch_assoc()) {
		$seriesId = $mysqli->query("select seriesId from series where team='SPAIN' and active=1")->fetch_array()[0];

		if ($match['resultShort'] === '-') {
			if ($previousHasResult) {
				$mysqli->query("update matches set homeTeamSeriesID=".$seriesId.", awayTeamSeriesID=".$seriesId." where matchId=".$match['matchId']);
				$length = $mysqli->query("SELECT length from series where seriesId=$seriesId")->fetch_array()[0];
				for ($i=1; $i<4; $i++) {
					$mysqli->query("insert into playedMatches (userId, matchId, seriesId, odds, bet, ignored, currentLength, pps) values ($i, ".$match['matchId'].", ".$seriesId.", 3, 0, 0, ".$length.", 0)");
				}
			} else {
				break;
			}
			$previousHasResult = false;
		} else {
			$previousHasResult = true;
			$mysqli->query("update series set length=length+1 where seriesId=$seriesId");
			$mysqli->query("update matches set homeTeamSeriesID=".$seriesId.", awayTeamSeriesID=".$seriesId." where matchId=".$match['matchId']);
			if ($match['resultShort'] === 'D') {
				$mysqli->query("update series set active=0 where seriesId=$seriesId");
				$mysqli->query("INsert into series (team, length, active, leagueId) values ('SPAIN', 1, 1, 23)");
			}
			$length = $mysqli->query("SELECT length from series where seriesId=$seriesId")->fetch_array()[0];
			$l = $length-1;
			for ($i=1; $i<4; $i++) {
				echo "Match to play!";
				$mysqli->query("insert into playedMatches (userId, matchId, seriesId, odds, bet, ignored, currentLength, pps) values ($i, ".$match['matchId'].", ".$seriesId.", 3, 0, 0, ".$l.", 0)");
			}
		}
	}
	?> 	