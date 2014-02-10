<?php
	session_start();
	//include("header.php");
	include("connection.php");

	$teams = array();

	$settings = $mysqli->query("SELECT leaguesRequirements from userSettings where userId=1")->fetch_array()[0];
	$settings = unserialize($settings);
	$seasons = array('2013%2F2014');
	$leagues = array('ENGLAND' => array('PREMIER+LEAGUE', 'CHAMPIONSHIP', 'LEAGUE+ONE', 'LEAGUE+TWO'), 
					'SCOTLAND' => array('PREMIERSHIP', 'CHAMPIONSHIP', 'LEAGUE+ONE', 'LEAGUE+TWO'),
				 	'GERMANY' => array('BUNDESLIGA', '2.+BUNDESLIGA', '3.+LIGA'),
				 	'FRANCE' => array('LIGUE+1', 'LIGUE+2'),
				 	'ITALY' => array('SERIE+A', 'SERIE+B'),
				 	'TURKEY' => array('SUPER+LIG'));

	foreach ($leagues as $country => $leaguesArray) {
	foreach ($leaguesArray as $leagueKey) {
		foreach ($seasons as $season) {
		$seasonName = str_replace("%2F", "-", $season);
		$data1 = file_get_contents("http://www.xscores.com/soccer/Results.jsp?sport=1&countryName=$country&leagueName=$leagueKey&seasonName=$season&sortBy=R&result=3#.Us6_JvQW3zg");

		$dom1 = new domDocument;

		@$dom1->loadHTML($data1);
		$dom1->preserveWhiteSpace = false;

		$select = $dom1->getElementById ('round');
		$op = $select->getElementsByTagName('option');
		$firstRound = $select->firstChild->getAttribute('value');
		$roundCount = $select->lastChild->nodeValue;
		//echo "$country $leagueName $roundCount<br>";
		$leagueName = str_replace("+", " ", $leagueKey);

		for ($j = 0; $j < $roundCount; $j ++) {
			$n = $firstRound + $j;
			$round = $j + 1;
			//echo "Round ".$j."<br>";
			$data = file_get_contents("http://www.xscores.com/soccer/Results.jsp?sport=1&countryName=$country&leagueName=$leagueKey&seasonName=$season&sortBy=R&round=$n&result=3#.Us6_JvQW3zg");

			$dom = new domDocument;

			@$dom->loadHTML($data);
			$dom->preserveWhiteSpace = false;
			$tables = $dom->getElementById ('scoretable');

			$rows = $tables->getElementsByTagName('tr');
			foreach ($rows as $row) {
			    $cols = $row->getElementsByTagName('td');
			    if ($cols->length == 1) {
			    	$date = $cols->item(0)->nodeValue;
			    } else {
			    	
			    	if ($cols->item(0)->nodeValue != "K/O") {
			    		//echo "$country $leagueName $date ";
					    $time = $cols->item(0)->nodeValue;
					    $stat = $cols->item(1)->nodeValue;
					    $home = $cols->item(4)->nodeValue;
					    $away = $cols->item(7)->nodeValue;
					    $ft = $cols->item(11)->nodeValue;
					    $tmp = explode("-", $ft);
					    if ($tmp[0] != "" && $tmp[1] != ""){
						    if ($tmp[0] === $tmp[1]){
						    	$ftShort = "D";
						    } else if ($tmp[0] > $tmp[1]) {
						    	$ftShort = "H";
						    } else {
						    	$ftShort = "A";
						    }
						} else {
							$ftShort = "-";
						}
						//echo $time." ".$stat." ".$home." ".$away." ".$ftShort;
						//echo "<br>";
						$leagueId = $mysqli->query("SELECT leagueId from leagueDetails where country='$country' and name='$leagueName'")->fetch_array()[0];
						$q3="select * from matches where leagueId=$leagueId and round='$round' and homeTeam='$home' and awayTeam='$away' and season='2013-2014' and resultShort='-'";
						//echo "$q3";
						$c = $mysqli->query($q3)->fetch_array();
						if ($c  && $ftShort != '-' && $stat==='Fin') {
							// print_r($c);
							// echo " ---> $home - $away $ft / $ftShort<br>";
							$homeSeries = $mysqli->query("SELECT seriesId FROM series where team='$home' and active=1")->fetch_array()[0];
							$awaySeries = $mysqli->query("SELECT seriesId FROM series where team='$away' and active=1")->fetch_array()[0];
							$q4="update matches set result='$ft', resultShort='$ftShort' where matchId=".$c['matchId'];
							// echo "$q4";
							$mysqli->query($q4);
							array_push($teams, $home);
							array_push($teams, $away);
							if ($ftShort === 'D') {
								 $mysqli->query("update series set active=0, length=length+1 where seriesId='$homeSeries' or seriesId='$awaySeries'");
								echo $mysqli->error;
								 $mysqli->query("INSERT INTO series (team, length, active, leagueId) values ('".$c['homeTeam']."', 1, 1, ".$c['leagueId'].")");
								 echo $mysqli->error;
								 $mysqli->query("INSERT INTO series (team, length, active, leagueId) values ('".$c['awayTeam']."', 1, 1, ".$c['leagueId'].")");
								 echo $mysqli->error;

							} else {
								$mysqli->query("update series set length=length+1 where seriesId='$homeSeries' or seriesId='$awaySeries'");
			 					echo $mysqli->error;
							}
						} else {
							//echo "$q3<br>";
						}
						$q5="select * from matches where leagueId=$leagueId and round='$round' and homeTeam='$home' and awayTeam='$away' and season='2013-2014' and state<>'Fin' and state<>'Sched'";
						$m = $mysqli->query($q5)->fetch_assoc();
						//print_r($m);
						if ($m && $stat === 'Fin') {
							array_push($teams, $m['homeTeam']);
							array_push($teams, $m['awayTeam']);
							$q6 = "update matches set matchDate='$date', matchTime='$time', state='$stat', result='$ft', resultShort='$ftShort' where matchId=".$m['matchId'];
							$mysqli->query($q6);
						}
						$q5="select * from matches where leagueId=$leagueId and round='$round' and homeTeam='$home' and awayTeam='$away' and season='2013-2014' and state<>'Fin' and state<>'Sched'";
						$m = $mysqli->query($q5)->fetch_assoc();
						//print_r($m);
						if ($m && $stat === 'Sched') {
							array_push($teams, $m['homeTeam']);
							array_push($teams, $m['awayTeam']);

							$q6 = "update matches set matchDate='$date', matchTime='$time', state='$stat', result='$ft', resultShort='$ftShort' where matchId=".$m['matchId'];
							$mysqli->query($q6);
						}
				}	
				}
			}
		}
	}

	}
}

foreach ($teams as $team) {
	$res = $mysqli->query("SELECT * from series where team=$team and active=1");
	while ($serie = $res->fetch_assoc()) {
		$q1 = "SELECT * FROM `matches` 
				left join series on (series.team=matches.homeTeam or series.team=matches.awayTeam) 
				where matches.resultShort='-' and series.seriesId=".$serie['seriesId']." and state='Sched' order by matchDate asc limit 1;";
		$res2 = $mysqli->query($q1);
		$match = $res2->fetch_assoc();
		if ($match) {
			//echo $match['length']." > ".$settings[$match['leagueId']]."<br>";
			if ($match['length'] > $settings[$match['leagueId']]) {
				if ($match['homeTeam'] = $match['team']) {
					$mysqli->query("UPDATE matches set homeTeamSeriesID=".$serie['seriesId']);	
				} else if ($match['awayTeam'] = $match['team']) {
					$mysqli->query("UPDATE matches set awayTeamSeriesID=".$serie['seriesId']);	
				}
				$q2="SELECT count(*) from playedMatches where matchId=".$match['matchId']." and seriesId=".$match['seriesId'];
				if ($mysqli->query($q2)->fetch_array()[0] === '0') {
					for ($i = 1; $i < 4; $i ++) {
						$betSoFar = $mysqli->query("SELECT SUM(bet) FROM playedMatches left join matches on matches.matchId=playedMatches.matchId where userId=$i and seriesId=".$serie['seriesId']." and matchDate<'".$row['matchDate']."'")->fetch_array()[0];
						echo "$betSoFar <br>";
						$q7="INSERT INTO playedMatches (userId, matchId, currentLength, odds, bet, ignored, seriesId, betSoFar) values ($i, ".$match['matchId'].", ".$match['length'].", 3, 0, 0, ".$match['seriesId'].", '$betSoFar')";
						echo "$q7<br>";
						$mysqli->query($q7);
						echo $mysqli->error;
					}
				} 
			}
		} else {
	//		echo $serie['seriesId']."<br>";
		}
	}
}
?>