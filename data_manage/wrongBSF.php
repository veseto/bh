<?php
	include("../connection.php");
	for ($i = 1; $i < 4; $i ++) {
		$q0 = "SELECT DISTINCT seriesId from playedMatches where userId=$i and (betSoFar>0 or bet>0)";
		$res0 = $mysqli->query($q0);
		while($s = $res0->fetch_assoc()) {
			$id=$s['seriesId'];
			$res = $mysqli->query("SELECT * from playedMatches left join matches on matches.matchId=playedMatches.matchId where userId=$i and seriesId=$id order by currentLength");
			$m = $res->fetch_assoc();
			while ($n = $res->fetch_assoc()) {
				$bet = $m['bet'] + $m['betSoFar'];
				$betSoFar = $n['betSoFar'];
				if ($bet != $betSoFar) {
					echo "[$id] user: $i ".$n['homeTeam']." - ".$n['awayTeam']." bet: $bet / bsf: $betSoFar<br>";
				}
				$m=$n;
			}
			// echo "SELECT * from playedMatches where userId=$i and seriesId=$id and bet+betSoFar<>(SELECT betSoFar from playedMatches where seriesId=$id and userId=$i)";
			// $res1 = $mysqli->query("SELECT * from playedMatches left join matches on matches.matchId=playedMatches.matchId where seriesId=$id and userId=$i order by seriesId asc, matchDate asc, matchTime asc");
			// while ($m = $res1->fetch_assoc()) {
			// 	echo "user: $i match: ".$m['homeTeam']." - ".$m['awayTeam']." BSF: ".$m['betSoFar']." bet: ".$m['bet']." <br>";
			// }
			// echo "------------------------------------------<br>";
		}
	}
?>