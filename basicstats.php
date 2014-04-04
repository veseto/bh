<?php
	include("connection.php");
	if(!isset($_SESSION['uid'])) {
	   header("Location: index.php");
	}
	
	$ppm = array('profit' => 0, 'insys' => 0, 'odds' => 0, 'countries' => array());
	$pps = array('profit' => 0, 'insys' => 0, 'odds' => 0, 'countries' => array());

	$seriesRes = $mysqli->query("SELECT DISTINCT seriesId
									FROM playedMatches
									WHERE userId=".$_SESSION['uid']." AND (bet<>0 or betSoFar<>0)");
	while ($series = $seriesRes->fetch_array()) {
		$seriesId = $series[0];
		$matchesRes = $mysqli->query("SELECT * 
									FROM playedMatches 
									LEFT JOIN (matches, leagueDetails, series) 
									ON (matches.matchId=playedMatches.matchId AND matches.leagueId=leagueDetails.leagueId and series.seriesId=playedMatches.seriesId)
									WHERE userId=".$_SESSION['uid']." AND series.seriesId=$seriesId 
									ORDER BY currentLength desc
									LIMIT 1");
		$res = $mysqli->query("SELECT odds, pps
									FROM playedMatches 
									LEFT JOIN (matches, leagueDetails) 
									ON (matches.matchId=playedMatches.matchId AND matches.leagueId=leagueDetails.leagueId)
									WHERE userId=".$_SESSION['uid']." AND seriesId=$seriesId AND (bet<>0 or betSoFar<>0)");

		while ($match = $matchesRes->fetch_assoc()) {
			$profit = $match['bet'] * $match['odds'] - $match['betSoFar'] - $match['bet'];
			$insys = $match['bet'] + $match['betSoFar'];
				
			if ($match['pps'] == 0) {
				if ($match['resultShort'] == 'D') {
					$ppm['profit'] += $match['profit'];
					if (array_key_exists($match['country'], $ppm['countries'])) {
						$ppm['countries'][$match['country']]['profit'] += $profit;
					} else {
						$ppm['countries'][$match['country']] = array();
						$ppm['countries'][$match['country']]['profit'] = $profit;
						$ppm['countries'][$match['country']]['insys'] = 0;
						$ppm['countries'][$match['country']]['odds'] = 0;

					}		
				} else {
					$insys = $match['bet'] + $match['betSoFar'];
					$ppm['insys'] += $insys;
					if (array_key_exists($match['country'], $ppm['countries'])) {
						$ppm['countries'][$match['country']]['insys'] += $insys;
					} else {
						$ppm['countries'][$match['country']] = array();
						$ppm['countries'][$match['country']]['profit'] = 0;
						$ppm['countries'][$match['country']]['insys'] = $insys;
						$ppm['countries'][$match['country']]['odds'] = 0;
					}
				}
			} else {
				if ($match['resultShort'] == 'D') {
					//echo $match['homeTeam']." - ".$match['awayTeam']." ".$profit."<br>";
					$pps['profit'] += $match['profit'];
					if (array_key_exists($match['country'], $pps['countries'])) {
						$pps['countries'][$match['country']]['profit'] += $profit;
						if (array_key_exists($match['name'], $pps['countries'][$match['country']]['leagues'])) {
							$pps['countries'][$match['country']]['leagues'][$match['name']]['profit'] += $profit;
							if (array_key_exists($match['team'], $pps['countries'][$match['country']]['leagues'][$match['name']]['teams'])) {
								$pps['countries'][$match['country']]['leagues'][$match['name']]['teams'][$match['team']]['profit'] += $profit;
							} else {
								$pps['countries'][$match['country']]['leagues'][$match['name']]['teams'][$match['team']] = array();
								$pps['countries'][$match['country']]['leagues'][$match['name']]['teams'][$match['team']]['profit'] = $profit;
								$pps['countries'][$match['country']]['leagues'][$match['name']]['teams'][$match['team']]['insys'] = 0;
								$pps['countries'][$match['country']]['leagues'][$match['name']]['teams'][$match['team']]['odds'] = 0;
							}
						} else {
							$pps['countries'][$match['country']]['leagues'][$match['name']] = array();
							$pps['countries'][$match['country']]['leagues'][$match['name']]['profit'] = $profit;
							$pps['countries'][$match['country']]['leagues'][$match['name']]['insys'] = 0;
							$pps['countries'][$match['country']]['leagues'][$match['name']]['odds'] = 0;
							$pps['countries'][$match['country']]['leagues'][$match['name']]['teams'] = array();
						}
					} else {
						$pps['countries'][$match['country']] = array();
						$pps['countries'][$match['country']]['profit'] = $profit;
						$pps['countries'][$match['country']]['insys'] = 0;
						$pps['countries'][$match['country']]['odds'] = 0;
						$pps['countries'][$match['country']]['leagues'] = array();
					}
							
				} else {
					$insys = $match['bet'] + $match['betSoFar'];
					$pps['insys'] += $insys;
					if (array_key_exists($match['country'], $pps['countries'])) {
						$pps['countries'][$match['country']]['insys'] += $insys;
						if (array_key_exists($match['name'], $pps['countries'][$match['country']]['leagues'])) {
							$pps['countries'][$match['country']]['leagues'][$match['name']]['insys'] += $insys;
							if (array_key_exists($match['team'], $pps['countries'][$match['country']]['leagues'][$match['name']]['teams'])) {
								$pps['countries'][$match['country']]['leagues'][$match['name']]['teams'][$match['team']]['insys'] += $insys;
							} else {
								$pps['countries'][$match['country']]['leagues'][$match['name']]['teams'][$match['team']] = array();
								$pps['countries'][$match['country']]['leagues'][$match['name']]['teams'][$match['team']]['profit'] = 0;
								$pps['countries'][$match['country']]['leagues'][$match['name']]['teams'][$match['team']]['insys'] = $insys;
								$pps['countries'][$match['country']]['leagues'][$match['name']]['teams'][$match['team']]['odds'] = 0;
							}
						} else {
							$pps['countries'][$match['country']]['leagues'][$match['name']] = array();
							$pps['countries'][$match['country']]['leagues'][$match['name']]['profit'] = 0;
							$pps['countries'][$match['country']]['leagues'][$match['name']]['insys'] = $insys;
							$pps['countries'][$match['country']]['leagues'][$match['name']]['odds'] = 0;
							$pps['countries'][$match['country']]['leagues'][$match['name']]['teams'] = array();
						}
					} else {
						$pps['countries'][$match['country']] = array();
						$pps['countries'][$match['country']]['profit'] = 0;
						$pps['countries'][$match['country']]['insys'] = $insys;
						$pps['countries'][$match['country']]['odds'] = 0;
						$pps['countries'][$match['country']]['leagues'] = array();
					}		
				}
			}
		}

	}

?>

<table id="scoreTable" class="table table-fixed table-bordered table-condensed text-center">
	<thead>
		<tr>
			<th>#</th>
			<th>Source</th>
			<th>Profit</th>
			<th>In system</th>
			<th>Avg odds</th>
		</tr>
	</thead>
	<tbody>	
<?php
	if (isset($_GET['team'])) {

	} else {
		if (isset($_GET['name'])) {
	
			$ops = $pps['countries'][$_GET['country']]['leagues'][$_GET['name']]['teams'];
			$k = 1;
			foreach ($ops as $key => $value) {
				$oddsarr = array();
					$oddRes0 = $mysqli->query("SELECT pps, odds, country, name, team
												FROM playedMatches
												LEFT JOIN (
												matches, leagueDetails, series
												) ON ( matches.matchId = playedMatches.matchId
												AND matches.leagueId = leagueDetails.leagueId
												AND series.seriesId = playedMatches.seriesId ) 
												WHERE userId =".$_SESSION['uid']."
												AND (bet<>0 or betSoFar<>0)
												AND pps=".$_GET['type']." AND country='".$_GET['country']."' AND name='".$_GET['name']."' AND team='$key'");
					echo $mysqli->error;
					$i = 0;
					$avgOdds0 = 0;
					while ($odds = $oddRes0->fetch_assoc()) {
						$avgOdds0 += $odds['odds'];
						$i++;
					}
					$oddsarr[$key] = round($avgOdds0/$i, 3, PHP_ROUND_HALF_UP);
				echo "<tr><td>".($k ++)."</td><td>$key</td><td>".$ops[$key]['profit']."</td><td>".$ops[$key]['insys']."</td><td>".$oddsarr[$key]."</td></tr>";
			}
		} else {
			if (isset($_GET['country'])) {
				$ops = $pps['countries'][$_GET['country']]['leagues'];
				$k = 1;
				foreach ($ops as $key => $value) {
					$oddsarr = array();
					$oddRes0 = $mysqli->query("SELECT pps, odds, country, name, team
												FROM playedMatches
												LEFT JOIN (
												matches, leagueDetails, series
												) ON ( matches.matchId = playedMatches.matchId
												AND matches.leagueId = leagueDetails.leagueId
												AND series.seriesId = playedMatches.seriesId ) 
												WHERE userId =".$_SESSION['uid']."
												AND (bet<>0 or betSoFar<>0)
												AND pps=".$_GET['type']." AND country='".$_GET['country']."' AND name='$key'");
					echo $mysqli->error;
					$i = 0;
					$avgOdds0 = 0;
					while ($odds = $oddRes0->fetch_assoc()) {
						$avgOdds0 += $odds['odds'];
						$i++;
					}
					$oddsarr[$key] = round($avgOdds0/$i, 3, PHP_ROUND_HALF_UP);
					echo "<tr><td>".($k ++)."</td><td><a href='stats.php?tab=sts&type=1&country=".$_GET['country']."&name=$key'>$key</a></td><td>".$ops[$key]['profit']."</td><td>".$ops[$key]['insys']."</td><td>".$oddsarr[$key]."</td></tr>";
				}
			} else {
				if (isset($_GET['type'])) {
					if ($_GET['type'] == 0) {
						$ops = $ppm['countries'];
						$k = 1;
						foreach ($ops as $key => $value) {
							$oddsarr = array();
							$oddRes0 = $mysqli->query("SELECT pps, odds, country, name, team
														FROM playedMatches
														LEFT JOIN (
														matches, leagueDetails, series
														) ON ( matches.matchId = playedMatches.matchId
														AND matches.leagueId = leagueDetails.leagueId
														AND series.seriesId = playedMatches.seriesId ) 
														WHERE userId =".$_SESSION['uid']."
														AND (bet<>0 or betSoFar<>0)
														AND pps=".$_GET['type']." AND country='$key'");
							echo $mysqli->error;
							$i = 0;
							$avgOdds0 = 0;
							while ($odds = $oddRes0->fetch_assoc()) {
								$avgOdds0 += $odds['odds'];
								$i++;
							}
							$oddsarr[$key] = round($avgOdds0/$i, 3, PHP_ROUND_HALF_UP);
							
							echo "<tr><td>".($k ++)."</td><td>$key</td><td>".$ops[$key]['profit']."</td><td>".$ops[$key]['insys']."</td><td>".$oddsarr[$key]."</td></tr>";
						
						}
						// $r = $mysqli->query("select count(*), length-1 from series where team in (select country from leagueDetails) and active=0 group by length");
						// while ($l = $r->fetch_array()) {
						// 	echo "length: ".$l[1]."-> count: ".$l[0]."<br>";
						// }
					} else {
						$ops = $pps['countries'];
						$k = 1;
						foreach ($ops as $key => $value) {
							$oddsarr = array();
							$oddRes0 = $mysqli->query("SELECT pps, odds, country, name, team
														FROM playedMatches
														LEFT JOIN (
														matches, leagueDetails, series
														) ON ( matches.matchId = playedMatches.matchId
														AND matches.leagueId = leagueDetails.leagueId
														AND series.seriesId = playedMatches.seriesId ) 
														WHERE userId =".$_SESSION['uid']."
														AND (bet<>0 or betSoFar<>0)
														AND pps=".$_GET['type']." AND country='$key'");
							echo $mysqli->error;
							$i = 0;
							$avgOdds0 = 0;
							while ($odds = $oddRes0->fetch_assoc()) {
								$avgOdds0 += $odds['odds'];
								$i++;
							}
							$oddsarr[$key] = round($avgOdds0/$i, 3, PHP_ROUND_HALF_UP);
							
							echo "<tr><td>".($k ++)."</td><td><a href='stats.php?tab=sts&type=1&country=$key'>$key</a></td><td>".$ops[$key]['profit']."</td><td>".$ops[$key]['insys']."</td><td>".$oddsarr[$key]."</td></tr>";
						}
					
					}
					
				} else {
					$oddsarr = array();
					for ($j = 0; $j < 2; $j ++){
						$oddRes0 = $mysqli->query("SELECT pps, odds, country, name, team
													FROM playedMatches
													LEFT JOIN (
													matches, leagueDetails, series
													) ON ( matches.matchId = playedMatches.matchId
													AND matches.leagueId = leagueDetails.leagueId
													AND series.seriesId = playedMatches.seriesId ) 
													WHERE userId =".$_SESSION['uid']."
													AND (bet<>0 or betSoFar<>0)
													AND pps=$j");
						echo $mysqli->error;
						$i = 0;
						$avgOdds0 = 0;
						while ($odds = $oddRes0->fetch_assoc()) {
							$avgOdds0 += $odds['odds'];
							$i++;
						}
						$oddsarr[$j] = round($avgOdds0/$i, 3, PHP_ROUND_HALF_UP);
					}
					echo "<tr><td>1</td><td><a href='stats.php?tab=sts&type=0'>PPM</a></td><td>".$ppm['profit']."</td><td>".$ppm['insys']."</td><td>".$oddsarr[0]."</td></tr>";
					echo "<tr><td>2</td><td><a href='stats.php?tab=sts&type=1'>PPS</a></td><td>".$pps['profit']."</td><td>".$pps['insys']."</td><td>".$oddsarr[1]."</td></tr>";

				}
			}
		}
	}
?>
	</tbody>
	</table>
<?php
	include("includes/footer.php");
?>