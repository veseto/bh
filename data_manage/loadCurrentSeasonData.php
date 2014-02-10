<?php
include("connection.php");
$userId = $_SESSION['uid'];
$settings = unserialize($mysqli -> query("select leaguesRequirements from userSettings where userId='1'")->fetch_array()[0]);
$seasons = array('2013%2F2014');
$leagues = array('ENGLAND' => array('PREMIER+LEAGUE', 'CHAMPIONSHIP', 'LEAGUE+ONE', 'LEAGUE+TWO'), 'SCOTLAND' => array('PREMIERSHIP', 'CHAMPIONSHIP', 'LEAGUE+ONE', 'LEAGUE+TWO'),
				 'GERMANY' => array('BUNDESLIGA', '2.+BUNDESLIGA', '3.+LIGA'),
				 'FRANCE' => array('LIGUE+1', 'LIGUE+2'),
				 'ITALY' => array('SERIE+A', 'SERIE+B'),
				 'TURKEY' => array('SUPER+LIG'));

//Initial series
$res = $mysqli->query("SELECT DISTINCT homeTeam FROM matches");
		while($team = $res->fetch_array()){
			$mysqli->query("INSERT INTO series (team, length, active) values ('".$team['homeTeam']."', 1, 1)");
		}

$res = $mysqli->query("SELECT * FROM matches where season='2013-2014' order by matchDate ASC");
while ($row = $res->fetch_assoc()) {
	$homeSeries = $mysqli->query("SELECT seriesId FROM series where team='".$row['homeTeam']."' and active=1")->fetch_array()[0];
	$awaySeries = $mysqli->query("SELECT seriesId FROM series where team='".$row['awayTeam']."' and active=1")->fetch_array()[0];
	if ($row['resultShort'] != '-') {
		$q = "update matches set homeTeamSeriesID=$homeSeries, awayTeamSeriesID=$awaySeries where matchId=".$row['matchId'];
		$mysqli->query($q);
		if ($row['resultShort'] === 'D') {
			 $mysqli->query("update series set active=0 where seriesId='$homeSeries' or seriesId='$awaySeries'");
			echo $mysqli->error;
			 $mysqli->query("INSERT INTO series (team, length, active) values ('".$row['homeTeam']."', 1, 1)");
			 echo $mysqli->error;
			 $mysqli->query("INSERT INTO series (team, length, active) values ('".$row['awayTeam']."', 1, 1)");
			 echo $mysqli->error;
		} else {
			 $mysqli->query("update series set length=length+1 where seriesId='$homeSeries' or seriesId='$awaySeries'");
			 echo $mysqli->error;
		}
	}
}

?>