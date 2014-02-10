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
										$home = unserialize($tab['home']);
										foreach ($home as $g) {
											if ($i == 15) {
												break;
											}
											$i ++;
											if ($g === 'W') {
												echo '<button type="button" class="btn btn-success btn-xs btn-series-wdl">W</button>&nbsp;';
											} else if ($g === 'L') {
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
										$away = unserialize($tab['away']);
										foreach ($away as $a) {
											if ($i == 15) {
												break;
											}
											$i ++;
											if ($a === 'W') {
												echo '<button type="button" class="btn btn-success btn-xs btn-series-wdl">W</button>&nbsp;';
											} else if ($a === 'L') {
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
										$i = 0;
										$total = unserialize($tab['total']);
										foreach ($total as $t) {
											if ($i == 15) {
												break;
											}
											$i ++;
											if ($t === 'W') {
												echo '<button type="button" class="btn btn-success btn-xs btn-series-wdl">W</button>&nbsp;';
											} else if ($t === 'L') {
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
