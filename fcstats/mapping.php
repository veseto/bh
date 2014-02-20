<?php
	include("../connection.php");
	$leagueId = 16;
	$res = $mysqli->query("SELECT tables.team, fcstatsStandings.team 
					from tables 
					left join fcstatsStandings 
					on tables.leagueId=fcstatsStandings.leagueId and tables.place=fcstatsStandings.place
					where tables.leagueId=$leagueId
					order by tables.place");	
	while($couple = $res->fetch_array()) {
		$count = $mysqli->query("SELECT COUNT(*) from mapping where xscores='".$couple[0]."' and fcstats='".$couple[1]."'")->fetch_array()[0];
		if ($count == 0 || $count == '0') {
			$mysqli->query("INSERT INTO mapping (xscores, fcstats) values ('".$couple[0]."', '".$couple[1]."')");
			echo $mysqli->error;
		}
	}
	echo "imported mapping for leagueId=$leagueId!";	
?>