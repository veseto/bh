<?php
	include("includes/header.php");
	include("connection.php");
	if(!isset($_SESSION['uid'])) {
	   header("Location: index.php");
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
	$profitQ = "SELECT profit 
			FROM `playedMatches`
			left join matches
			on matches.matchId=playedMatches.matchId
			where resultShort='D' and userId=".$_SESSION['uid']." and bet<>0";

	$oddsQ = "SELECT odds 
			FROM `playedMatches`
			left join matches
			on matches.matchId=playedMatches.matchId
			where userId=".$_SESSION['uid']." and bet<>0";
	if (isset($_GET['type'])) {
		$type=$_GET['type'];
		if (isset($_GET['country']) && $type == 1) {
			$country = $_GET['country'];
			if (isset($_GET['leagueId']) && $type == 1) {
				$leagueId = $_GET['leagueId'];
				$q = $mysqli->query("SELECT DISTINCT homeTeam
					FROM matches 
					LEFT JOIN (playedMatches, leagueDetails)
					ON (matches.leagueId=leagueDetails.leagueId and playedMatches.matchId=matches.matchId)
					where matches.matchId 
					in (SELECT distinct matchId FROM playedMatches where pps=$type and (bet<>0 or betSoFar<>0) 
						and userId=".$_SESSION['uid']." and leagueDetails.leagueId='$leagueId')");
				echo $mysqli->error;
				$i = 1;
				while ($c = $q->fetch_array()) {
					$team = $c[0];

					$oddsQ2 = "SELECT odds 
								FROM `playedMatches`
								left join (matches, leagueDetails)
								on (matches.matchId=playedMatches.matchId and matches.leagueId=leagueDetails.leagueId)
								where userId=".$_SESSION['uid']." and bet<>0 and pps=$type and (homeTeam='$team' or awayTeam='$team')";

					$odds = 0;
					$j = 0;
					$r = $mysqli->query($oddsQ2);
					// echo "$oddsQ2<br>";
					while($tmp = $r->fetch_array()) {
						$odds += $tmp[0];
						$j ++;
					}

					$profitQ2 = "SELECT profit 
						FROM `playedMatches`
						left join (matches, leagueDetails)
						on (matches.matchId=playedMatches.matchId and leagueDetails.leagueId=matches.leagueId)
						where resultShort='D' and userId=".$_SESSION['uid']." and bet<>0 and pps=$type and (homeTeam='$team' or awayTeam='$team')";
					$res = $mysqli->query($profitQ2);
					$profit = 0;
					while ($p = $res->fetch_array()) {
						$profit += $p[0];
					}

					$odds = round( $odds/$j, 2, PHP_ROUND_HALF_UP);
					
					echo "<tr><td>$i</td><td>$team</td><td>$profit</td><td></td><td>$odds</td></tr>";
					
					$i ++;
				}
			} else {
				$q = $mysqli->query("SELECT DISTINCT leagueDetails.leagueId, name
					FROM leagueDetails 
					LEFT JOIN (playedMatches, matches)
					ON (matches.leagueId=leagueDetails.leagueId and playedMatches.matchId=matches.matchId)
					where matches.matchId 
					in (SELECT distinct matchId FROM playedMatches where pps=$type and (bet<>0 or betSoFar<>0) and userId=".$_SESSION['uid'].") and country='$country'");
				echo $mysqli->error;
				$i = 1;
				while ($c = $q->fetch_array()) {
					$leagueId = $c[0];
					$leagueName = $c[1];

					$oddsQ2 = "SELECT odds 
								FROM `playedMatches`
								left join (matches, leagueDetails)
								on (matches.matchId=playedMatches.matchId and matches.leagueId=leagueDetails.leagueId)
								where userId=".$_SESSION['uid']." and bet<>0 and pps=$type and matches.leagueId=$leagueId";

					$odds = 0;
					$j = 0;
					$r = $mysqli->query($oddsQ2);
					// echo "$oddsQ2<br>";
					while($tmp = $r->fetch_array()) {
						$odds += $tmp[0];
						$j ++;
					}

					$profitQ2 = "SELECT profit 
						FROM `playedMatches`
						left join (matches, leagueDetails)
						on (matches.matchId=playedMatches.matchId and leagueDetails.leagueId=matches.leagueId)
						where resultShort='D' and userId=".$_SESSION['uid']." and bet<>0 and pps=$type and matches.leagueId='$leagueId'";
					$res = $mysqli->query($profitQ2);
					$profit = 0;
					while ($p = $res->fetch_array()) {
						$profit += $p[0];
					}

					$odds = round( $odds/$j, 2, PHP_ROUND_HALF_UP);
					
					echo "<tr><td>$i</td><td><a href='basicstats.php?type=$type&country=$country&leagueId=$leagueId'>$country->$leagueName</td><td>$profit</td><td></td><td>$odds</td></tr>";
					
					$i ++;
				}
			}
		} else {
		
			$q = $mysqli->query("SELECT DISTINCT country 
					FROM leagueDetails 
					LEFT JOIN (playedMatches, matches)
					ON matches.leagueId=leagueDetails.leagueId and playedMatches.matchId=matches.matchId
					where matches.matchId in (SELECT distinct matchId FROM playedMatches where pps=$type and (bet<>0 or betSoFar<>0) and userId=".$_SESSION['uid'].")");
			
			$i = 1;
			while ($c = $q->fetch_array()) {
				$country = $c[0];
				
				$oddsQ2 = "SELECT odds 
							FROM `playedMatches`
							left join (matches, leagueDetails)
							on (matches.matchId=playedMatches.matchId and matches.leagueId=leagueDetails.leagueId)
							where userId=".$_SESSION['uid']." and bet<>0 and pps=$type and country='$country'";

				$odds = 0;
				$j = 0;
				$r = $mysqli->query($oddsQ2);
				// echo "$oddsQ2<br>";
				while($tmp = $r->fetch_array()) {
					$odds += $tmp[0];
					$j ++;
				}

				$profitQ2 = "SELECT profit 
					FROM `playedMatches`
					left join (matches, leagueDetails)
					on (matches.matchId=playedMatches.matchId and leagueDetails.leagueId=matches.leagueId)
					where resultShort='D' and userId=".$_SESSION['uid']." and bet<>0 and pps=$type and country='$country'";
				$res = $mysqli->query($profitQ2);
				$profit = 0;
				while ($p = $res->fetch_array()) {
					$profit += $p[0];
				}

				$odds = round( $odds/$j, 2, PHP_ROUND_HALF_UP);
				if ($type == 0) {
					echo "<tr><td>$i</td><td>$country</td><td>$profit</td><td></td><td>$odds</td></tr>";
				} else {
					echo "<tr><td>$i</td><td><a href='basicstats.php?type=$type&country=$country'>$country</td><td>$profit</td><td></td><td>$odds</td></tr>";
				}
				$i ++;
			}
		}	
	} else {
		for ($type = 0; $type < 2; $type ++) {
			$profitQ1 = $profitQ." and pps=$type";
			$oddsQ1 = $oddsQ." and pps=$type";
						
			$res = $mysqli->query($profitQ1);
			$profit = 0;
			while ($p = $res->fetch_array()) {
				$profit += $p[0];
			}
			$odds = 0;
			$j = 0;
			$r = $mysqli->query($oddsQ1);
			// echo "$oddsQ1<br>";
			while($tmp = $r->fetch_array()) {
				$odds += $tmp[0];
				$j ++;
			}


			$odds = round( $odds/$j, 2, PHP_ROUND_HALF_UP);
			$t = $type == 0? "PPM": "PPS";
			echo "<tr><td>".($type + 1)."</td><td><a href='basicstats.php?type=$type'>$t</td><td>$profit</td><td></td><td>$odds</td></tr>";
		}
	}
?>
	</tbody>
</table>
<?php
	include("includes/footer.php");
?>