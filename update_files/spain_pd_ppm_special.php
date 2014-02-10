<?php
	include("/var/www/bh/connection.php");
  
	$league = array('SPAIN' => 'PRIMERA+DIVISION');

  	$cntry = '';
  	$lg = '';

   	//echo date("Y-m-d h:i:s", time());
   	$start=time();
   	$updatedMatches = 0;
   	$updatedMatchesToPlay = 0;
   	foreach ($league as $key => $value) {
   		$cntry = $key;
		$leagueName = str_replace("+", " ", $value);
		$leagueId = $mysqli->query("SELECT leagueId from leagueDetails where country='$key' and name='$leagueName'")->fetch_array()[0];
		$lg = $leagueName;

		$data1 = file_get_contents("http://www.xscores.com/soccer/Results.jsp?sport=1&countryName=$key&leagueName=$value&leagueName1=$value&seasonName=2013%2F2014&sortBy=R&result=3#.Us6_JvQW3zg");

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
		 	$data = file_get_contents("http://www.xscores.com/soccer/Results.jsp?sport=1&countryName=$key&leagueName=$value&leagueName1=$value&seasonName=2013%2F2014&sortBy=R&round=$n&result=3#.Us6_JvQW3zg");

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
							$m = $mysqli -> query($q3) -> fetch_assoc();

							$q1 = "UPDATE matches 
										set matchDate='$date', matchTime='$time', state='$stat', result='$ft', resultShort='$ftShort' 
										where matchId=".$m['matchId'];
							$mysqli->query($q1);
							$q5 = "SELECT COUNT(*) 
										FROM matches
										WHERE round =".$m['round']."
										AND season =  '2013-2014'
										AND leagueId = 23
										AND resultShort = 'D'";
										echo "$q5<br>";
							if ($tmp = $mysqli->query($q5)) {
								if ($tmp->fetch_array()[0] > 0) {
									// echo "$round has D<br>";
									$r = $m['round'] + 1;
									$q6 = "SELECT COUNT(*) 
											FROM playedMatches
											LEFT JOIN matches ON playedMatches.matchId = matches.matchId
											WHERE round = $r
											AND season =  '2013-2014'
											AND leagueId = 23";
									if ($mysqli->query($q6)->fetch_array()[0] > 0) {
										// echo "added to play next round<br>";
										continue;
									} else {
										echo "add to play for $r round";
										$r = $m['round'] + 1;
										$q7 = "SELECT * 
											FROM matches
											WHERE round = $r
											AND season =  '2013-2014'
											AND leagueId = 23
											ORDER BY matchDate asc, matchTime asc
											limit 1";
										$next = $mysqli->query($q7)->fetch_assoc();
										$mysqli->query("update series set active=0 where leagueId=23");
										$mysqli->query("insert into series (team, length, leagueId, active) values ('SPAIN', 1, 23, 1)");
										$seriesId = $mysqli->insert_id;
										echo "$seriesId<br>";
										for ($i = 1; $i < 4; $i ++) {
											$q2 = "insert into playedMatches (userId, matchId, seriesId, odds, bet, betSoFar, ignored, currentLength, income, profit, pps) 
															values ($i, ".$next['matchId'].", $seriesId, 3, 0, 0, 0, 1, 0, 0, 0)";
											echo "$q2<br>";
											$mysqli->query($q2);
										}
									}
								} else {
								print_r($m);
								echo " $ftShort $stat ".$m['resultShort']." <br>";
								if ($ftShort != '-' && $stat==='Fin' && $m['resultShort'] === '-') {
									echo "next match";
									$serie = $mysqli->query("SELECT * FROM series where team='SPAIN' and active=1")->fetch_array();
									$s=$serie['seriesId'];
									if ($ftShort === 'D') {
										echo "D match!";
										$mysqli->query("update series set active=0, length=length+1 where seriesId='$s'");
										echo $mysqli->error;
										$mysqli->query("INSERT INTO series (team, length, active, leagueId) values ('SPAIN', 1, 1, 23)");
										$newSeriesId = $mysqli->insert_id;
										echo $mysqli->error;
										$r = $m['round'] + 1;
										$q1 = "SELECT * 
										 		FROM `matches` 
												where matches.resultShort='-' and state='Sched' and leagueId=23 and round=$r 
												order by matchDate asc, matchTime asc 
												limit 1";
										$res2 = $mysqli->query($q1);
										$match = $res2->fetch_assoc();
										if ($matchId) {
											echo "add match to play for $r round";
											for ($i = 1; $i < 4; $i ++) {
												$q7="INSERT INTO playedMatches (userId, matchId, currentLength, odds, bet, ignored, seriesId, betSoFar, pps) values ($i, ".$match['matchId'].", 1, 3, 0, 0, $newSeriesId, 0, 0)";
												$mysqli->query($q7);
												echo $mysqli->error;
											}
										}
									} else {
										echo "not D - add next match to play";
										$q1 = "SELECT * 
										 		FROM `matches` 
												where matches.resultShort='-' and state='Sched' and leagueId=23
												ORDER BY matchDate asc, matchTime asc 
												limit 1";
										$res2 = $mysqli->query($q1);
										$match = $res2->fetch_assoc();
										
										$mysqli->query("UPDATE series set length=length+1 where seriesId=$s");
										for ($i = 1; $i < 4; $i ++) {
											$a = $mysqli->query("SELECT betSoFar, bet 
																	FROM playedMatches 
																	left join matches 
																	on matches.matchId=playedMatches.matchId 
																	where userId=$i and seriesId=$s and (matchDate<'".$match['matchDate']."' or (matchDate='".$match['matchDate']."' and matchTime<'".$match['matchTime']."')) limit 1")->fetch_array();
											$betSoFar = $a[0] + $a[1];
											echo "$betSoFar <br>";
											$q7="INSERT INTO playedMatches (userId, matchId, currentLength, odds, bet, ignored, seriesId, betSoFar, pps) 
													values ($i, ".$match['matchId'].", ".$serie['length'].", 3, 0, 0, ".$serie['seriesId'].", '$betSoFar', 0)";
											$mysqli->query($q7);
											echo $mysqli->error;
										}

									}
								}
								}
							}				
						}
					}
				}	
			}
		}
	}

	

	$sec = time() - $start;
	$mysqli->query("INSERT INTO stats (leagueId, lastSec, updated, addedToPlay) values ($leagueId, $sec, $updatedMatches, $updatedMatchesToPlay)");
	// file_put_contents("/var/www/bh/data_manage/updateStats.txt", "$cntry->$lg ".date("Y-m-d h:i:s", time())." - seconds: ".$sec."  updated matches: ".$updatedMatches."  added matches to play: ".$updatedMatchesToPlay."\n", FILE_APPEND);
?>