<!-- table evenOdd sortable tips -->
<?php
	include("../connection.php");
	$url = "http://fcstats.com/table,super-lig-turkey,37,1,727.php";
		// echo "$url";
	$leagueId = 16;
	$data = file_get_contents($url);

	$dom = new domDocument;

	@$dom->loadHTML($data);
	$dom->preserveWhiteSpace = false;

	$finder = new DomXPath($dom);
	$classname="table evenOdd sortable tips";
	$nodes = $finder->query("//*[contains(@class, '$classname')]");
	$rows = $nodes->item(0)->getElementsByTagName("tr");
	foreach ($rows as $row) {
		$cols = $row->getElementsByTagName("td");
		if ($cols->length > 0){
			$place = $cols->item(0)->nodeValue;
			$team = $cols->item(1)->nodeValue;
			$count = $mysqli->query("SELECT COUNT(*) FROM fcstatsStandings where team='$team' and leagueId=$leagueId")->fetch_array()[0];
			if ($count == 0 || $count == '0') {
				$mysqli->query("INSERT INTO fcstatsStandings (leagueId, team, place) values ($leagueId, '$team', $place)");
			}
			echo $mysqli->error;
		}
	}
	$league = $mysqli->query("SELECT * FROM leagueDetails where leagueId=$leagueId")->fetch_assoc();
	echo $league['country']." ".$league['name']." standings imported!";
?>