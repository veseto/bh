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
	$series = trim($_GET['seriesId']);
	$date = $_GET['date'];
	$time = $_GET['time'];

	$league = $mysqli->query("SELECT leagueId from series where seriesId=$series")->fetch_array()[0];

	$q="SELECT * from matches where leagueId=$league and (matchDate>'$date' or (matchDate='$date' and matchTime>'$time')) order by matchDate asc, matchTime asc limit 10";
	$had = $mysqli->query("SELECT resultShort from matches where leagueId=$league and (matchDate<'$date' or (matchDate='$date' and matchTime<'$time')) order by matchDate desc, matchTime desc limit 20");
	$res = $mysqli->query($q);
	$round = $mysqli->query("SELECT round from matches left join playedMatches on playedMatches.matchId=matches.matchId where seriesId=$series and matchTime='$time' and matchDate='$date'")->fetch_array()[0];
	while ($m = $res->fetch_assoc()) {
			$placeH = $mysqli->query("SELECT place from tables where leagueId=".$m['leagueId']." and team='".$m['homeTeam']."'")->fetch_array()[0];
			$placeA = $mysqli->query("SELECT place from tables where leagueId=".$m['leagueId']." and team='".$m['awayTeam']."'")->fetch_array()[0];
			$time = substr($m['matchTime'], 0, -3);

			if ($round != $m['round']) {
				echo "<tr style='color: #ccc'><td>".$m['matchDate']."</td><td>$time</td><td>[$placeH] ".$m['homeTeam']."</td><td>-</td><td>[$placeA] ".$m['awayTeam']."</td></tr>";
			} else {
				$d = date("Y-m-d", time());
				if ($m['matchDate'] == $d) {
					echo "<tr><td><strong>".$m['matchDate']."</strong></td>";
				} else {
					echo "<tr><td>".$m['matchDate']."</td>";
				}
				
				echo "<td>$time</td><td>[$placeH] ".$m['homeTeam']."</td><td>-</td><td>[$placeA] ".$m['awayTeam']."</td></tr>";
			}
			//echo $m['matchDate']."&nbsp;&nbsp;&nbsp;$time&nbsp;&nbsp;&nbsp;[$placeH] ".$m['homeTeam']." - [$placeA] ".$m['awayTeam']."<br />";
	}
?>
					</table></td>
					<td>
						<table>
							<tr>
								<td><strong>total</strong></td>
								<td>
									<?php
										while ($r = $had->fetch_array()) {
											if ($r[0] === 'H') {
										 		echo '<button type="button" class="btn btn-success btn-xs">H</button>&nbsp;';
											} else if ($r[0] === 'A') {
										 		echo '<button type="button" class="btn btn-success btn-xs">A</button>&nbsp;';
											} else if ($r[0] === 'D') {
										 		echo '<button type="button" class="btn btn-warning btn-xs">D</button>&nbsp;';
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
</table