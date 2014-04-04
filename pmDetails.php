<table style="text-align: left; font-size: 90%;" width="100%">
	<tr>
		<td>
			<table border="1" width="100%">
				<tr>
					<th><strong>next 5 matches</strong></th>
					<th><strong>form (last 15)</strong></th>
				</tr>
				<tr>
					<td><table>
<?php
	include("connection.php");
	$team = trim($_GET['team']);
	$date = $_GET['date'];
	$q="SELECT * from matches where (homeTeam='$team' or awayTeam='$team') and matchDate>'$date' order by matchDate limit 5";
	$res = $mysqli->query($q);
	while ($m = $res->fetch_assoc()) {
			$tab = $mysqli->query("SELECT * from tables where leagueId=".$m['leagueId']." and team='$team'")->fetch_assoc();
			echo "<tr>";
			if ($team === $m['homeTeam']) {
				$place = $mysqli->query("SELECT place from tables where leagueId=".$m['leagueId']." and team='".$m['awayTeam']."'")->fetch_array()[0];
				echo "<td>".$m['matchDate']."</td><td><strong>[".$tab['place']."] ".$m['homeTeam']."</strong></td><td>-</td><td>[$place] ".$m['awayTeam']."</td>";
			} else if ($team === $m['awayTeam']) {
				$place = $mysqli->query("SELECT place from tables where leagueId=".$m['leagueId']." and team='".$m['homeTeam']."'")->fetch_array()[0];
				echo "<td>".$m['matchDate']."</td><td>[$place] ".$m['homeTeam']."</td><td>-</td><td><strong>[".$tab['place']."] ".$m['awayTeam']."</strong></td>";
			}
			echo "<tr>";
	}
?>
					</table></td>
					<td>
						<table>
							<tr>
								<td><strong>home</strong></td>
								<td>
									<?php
									 	$i = 0;
										$res0 = $mysqli->query("SELECT resultShort from matches where resultShort<>'-' and homeTeam='$team' order by matchDate desc limit 15");
										while ($home = $res0->fetch_array()) {
											$g = $home[0];
											$i ++;
											if ($g === 'H') {
												echo '<button type="button" class="btn btn-success btn-xs btn-series-wdl">W</button>&nbsp;';
											} else if ($g === 'A') {
												echo '<button type="button" class="btn btn-danger btn-xs btn-series-wdl">L</button>&nbsp;';
											} else if ($g === 'D') {
												echo '<button type="button" class="btn btn-warning btn-xs btn-series-wdl">D</button>&nbsp;';
											}
										}
									?>
								</td>
							</tr>
							<tr>
								<td><strong>away</strong></td>
								<td>
									<?php
									$i = 0;
										$res1 = $mysqli->query("SELECT resultShort from matches where resultShort<>'-' and awayTeam='$team' order by matchDate desc limit 15");
										while ($away = $res1->fetch_array()) {
											$a = $away[0];
											$i ++;
											if ($a === 'A') {
												echo '<button type="button" class="btn btn-success btn-xs btn-series-wdl">W</button>&nbsp;';
											} else if ($a === 'H') {
												echo '<button type="button" class="btn btn-danger btn-xs btn-series-wdl">L</button>&nbsp;';
											} else if ($a === 'D') {
												echo '<button type="button" class="btn btn-warning btn-xs btn-series-wdl">D</button>&nbsp;';
											}
										}
									?>
								</td>
							</tr>
							<tr>
								<td><strong>total</strong></td>
								<td>
									<?php
										$res2 = $mysqli->query("SELECT resultShort, homeTeam from matches where resultShort<>'-' and (homeTeam='$team' or awayTeam='$team') order by matchDate desc limit 15");
										while ($total = $res2->fetch_array()) {
											$t = $total[0];
											$ht = $total[1];
											$i ++;
											if (($t === 'H' && $team == $ht) || ($t == 'A' && $ht != $team)) {
												echo '<button type="button" class="btn btn-success btn-xs btn-series-wdl">W</button>&nbsp;';
											} else if (($t === 'A' && $team == $ht) || ($t == 'H' && $ht != $team)) {
												echo '<button type="button" class="btn btn-danger btn-xs btn-series-wdl">L</button>&nbsp;';
											} else if ($t === 'D') {
												echo '<button type="button" class="btn btn-warning btn-xs btn-series-wdl">D</button>&nbsp;';
											}
										}
									?>
								</td>
							</tr>
						</table>

					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
