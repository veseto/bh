<?php
	session_start();
	//include("header.php");
	include("../connection.php");
  
   	echo date("Y-m-d h:i:s", time());
   	$start=time();
   	$updatedMatches = 0;
   	$updatedMatchesToPlay = 0;

	$data1 = file_get_contents("http://www.xscores.com/soccer/Results.jsp?sport=1&countryName=SPAIN&leagueName=PRIMERA+DIVISION&seasonName=2013%2F2014&sortBy=R&result=3#.Us6_JvQW3zg");

			$dom1 = new domDocument;

			@$dom1->loadHTML($data1);
			$dom1->preserveWhiteSpace = false;

			$select = $dom1->getElementById ('round');
			$op = $select->getElementsByTagName('option');
			$firstRound = $select->firstChild->getAttribute('value');
			$roundCount = $select->lastChild->nodeValue;
			//echo "$country $leagueName $roundCount<br>";

			
			for ($j = 0; $j <= $roundCount; $j ++) {
				$n = $firstRound + $j;
				$round = $j + 1;
			// 	//echo "Round ".$j."<br>";
			 	$data = file_get_contents("http://www.xscores.com/soccer/Results.jsp?sport=1&countryName=SPAIN&leagueName=PRIMERA+DIVISION&seasonName=2013%2F2014&sortBy=R&round=$n&result=3#.Us6_JvQW3zg");

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
						if ($stat === 'Fin' || $stat === 'Sched' || $stat === 'Abd' || $stat === 'Post') {
							$q3 = "SELECT * 
									FROM matches 
									where season='2013-2014' and homeTeam='$home' and awayTeam='$away' and round=$round";
							// echo "$q3<br>";
							$m = $mysqli -> query($q3) -> fetch_assoc();
							
							if (($stat === 'Post' || $stat === 'Abd') && $m['state'] === 'Sched') {
								$mysqli->query("delete FROM playedMatches where matchId=".$m['matchId']);
							}
								$q1 = "UPDATE matches 
										set matchDate='$date', matchTime='$time', state='$stat', result='$ft', resultShort='$ftShort' 
										where matchId=".$m['matchId'];
								// echo $q1."<br>";
								$mysqli->query($q1);

								date_default_timezone_set('Europe/Sofia');
							 	if ($ftShort != '-' && $stat==='Fin' && $m['resultShort'] === '-') {
							 		$updatedMatches ++;
									$homeSeries = $mysqli->query("SELECT seriesId FROM series where team='SPAIN' and active=1")->fetch_array()[0];
									if ($ftShort === 'D') {
										 $mysqli->query("update series set active=0, length=length+1 where seriesId='$homeSeries'");
										echo $mysqli->error;
										 $mysqli->query("INSERT INTO series (team, length, active, leagueId) values ('SPAIN', 1, 1, ".$m['leagueId'].")");
										 echo $mysqli->error;
										 

									} else {
										$mysqli->query("update series set length=length+1 where seriesId='$homeSeries'");
					 					echo $mysqli->error;
									}

								}



								} 
					}
					
				}	
			}
		}
	$res = $mysqli->query("SELECT * from series where active=1 and team='SPAIN'");
	if ($serie = $res->fetch_assoc()) {
		$q1 = "SELECT * FROM `matches` 
				where matches.resultShort='-' and state='Sched' and leagueId=23 order by matchDate asc, matchTime asc limit 1;";
		$res2 = $mysqli->query($q1);
		$match = $res2->fetch_assoc();
		if ($match) {
			//echo $match['length']." > ".$settings[$match['leagueId']]."<br>";
				$mysqli->query("UPDATE matches set homeTeamSeriesID=".$serie['seriesId'].", awayTeamSeriesID=".$serie['seriesId']." where matchId=".$match['matchId']);	
				
				$q2="SELECT count(*) from playedMatches where matchId=".$match['matchId']." and seriesId=".$serie['seriesId'];
				if ($mysqli->query($q2)->fetch_array()[0] === '0') {
					$q4="select * from matches left join (playedMatches, series) on (playedMatches.matchId=matches.matchId and series.seriesId=playedMatches.seriesId) where resultShort='-' and userId=1 and playedMatches.seriesId=".$serie['seriesId'];
					$tmp = $mysqli->query($q4);
					if ($tmp->num_rows) {
						$old = $tmp->fetch_assoc();
						$mysqli->query("UPDATE matches set homeTeamSeriesID=0, awayTeamSeriesID=0 where matchId=".$old['matchId']);
						$mysqli->query("delete FROM playedMatches where matchId=$id");
					}
					
					for ($i = 1; $i < 4; $i ++) {
						$a = $mysqli->query("SELECT betSoFar, bet FROM playedMatches left join matches on matches.matchId=playedMatches.matchId where userId=$i and seriesId=".$serie['seriesId']." and (matchDate<'".$match['matchDate']."' or (matchDate='".$match['matchDate']."' and matchTime<'".$match['matchTime']."')) limit 1")->fetch_array();
						$betSoFar = $a[0] + $a[1];
						echo "$betSoFar <br>";
						$updatedMatchesToPlay ++;
						$q7="INSERT INTO playedMatches (userId, matchId, currentLength, odds, bet, ignored, seriesId, betSoFar, pps) values ($i, ".$match['matchId'].", ".$serie['length'].", 3, 0, 0, ".$serie['seriesId'].", '$betSoFar', 0)";
						$mysqli->query($q7);
						echo $mysqli->error;
					}
				} 
		} else {
	//		echo $serie['seriesId']."<br>";
		}
	}

	$sec = time() - $start;
	file_put_contents("/var/www/bh/data_manage/updateStats.txt", "SPAIN ".date("Y-m-d h:i:s", time())." - seconds: ".$sec."  updated matches: ".$updatedMatches."  added matches to play: ".$updatedMatchesToPlay."\n", FILE_APPEND);
?>