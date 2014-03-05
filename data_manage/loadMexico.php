<?php
include("../connection.php");
 $seasons = array('2013%2F2014');
 // $leagues = array(//'SPAIN' => array('PRIMERA+DIVISION'));
 // 			//'POLAND' => array('DIVISION+1'), 
	// 			 // 'DENMARK' => array('SUPERLIGAEN'),
	// 			 'MEXICO' => array('CLAUSURA'));
	// 			 // 'BELGIUM' => array('PRO+LEAGUE'));
// 				 'SPAIN' => array('PRIMERA+DIVISION'));
	//$seasons = array('2005%2F2006', '2006%2F2007', '2007%2F2008', '2008%2F2009', '2009%2F2010', '2010%2F2011', '2011%2F2012', '2012%2F2013', '2013%2F2014');
	$leagues = array('MEXICO' => array('CLAUSURA'));

	foreach ($seasons as $season) {

		$seasonName = str_replace("%2F", "-", $season);
		$url = "http://www.xscores.com/soccer/Results.jsp?sport=1&countryName=MEXICO&leagueName=CLAUSURA&leagueName1=CLAUSURA&seasonName=2013%2F2014&sortBy=R&result=3#.UwYJ_kKSztk";
		echo "$url";
		$data1 = file_get_contents($url);

		$dom1 = new domDocument;

		@$dom1->loadHTML($data1);
		$dom1->preserveWhiteSpace = false;

		$select = $dom1->getElementById ('round');
		$op = $select->getElementsByTagName('option');
		$country = "MEXICO";
		$leagueName = 'CLAUSURA';

		$q1="select leagueId from leagueDetails where country='$country' and name='$leagueName'";
		$leagueId = $mysqli->query($q1)->fetch_array()[0];

		$firstRound = 115267;//$select->firstChild->getAttribute('value');
		$roundCount = 17;//$select->lastChild->nodeValue;
		//echo "$country $leagueName $roundCount<br>";
		echo "Season: $seasonName First round : $firstRound Round count: $roundCount<br>";
		for ($j = 0; $j < $roundCount; $j ++) {
			$n = $firstRound + $j;
			//echo "$n <br>";
			$round = $j + 1;
			//echo "Round ".$j."<br>";
			//$url="http://www.xscores.com/soccer/Results.jsp?sport=1&countryName=MEXICO&leagueName=CLAUSURA&leagueName1=CLAUSURA&seasonName=2013%2F2014&sortBy=R&round=$n&result=3";
			$url = "http://www.xscores.com/soccer/Results.jsp?sport=1&countryName=MEXICO&leagueName=CLAUSURA&leagueName1=CLAUSURA&seasonName=2013%2F2014&sortBy=R&round=$n&result=3#.UwYJ_kKSztk";
			echo "$url<br>";
			$data = file_get_contents($url);

			$dom = new domDocument;

			@$dom->loadHTML($data);
			$dom->preserveWhiteSpace = false;
			$table = $dom->getElementById ('scoretable');
			$rows = $table->getElementsByTagName('tr');
			// print_r($dom);
			foreach ($rows as $row) {
			    $cols = $row->getElementsByTagName('td');
			    // print_r($cols);
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
						// echo $time." ".$stat." ".$home." ".$away." ".$ftShort;
						// echo "<br>";

						$q2 = "select count(*) from matches where leagueId='$leagueId' and round='$round' and homeTeam='$home' and awayTeam='$away' and matchDate='$date' and matchTime='$time'";
						// echo "$q2<br>";
						$c = $mysqli->query($q2)->fetch_array()[0];
						
						if ($c == 0 || $c == '0') {
							$q = "INSERT INTO `matches`(`leagueId`, `matchDate`, `matchTime`, `homeTeam`, `awayTeam`, `round`, `state`, `result`, `homeTeamSeriesID`, `awayTeamSeriesID`, `resultShort`, `season`) VALUES ('$leagueId','$date','$time','$home','$away','$round','$stat','$ft',NULL,NULL,'$ftShort','$seasonName')";
							// echo "$q<br>";
							$mysqli->query($q);
							echo $mysqli->error;
						} else {
							echo "Exists!";
						}
					} else {
						echo "boo";
					}
				}
			}
		}
	}
?>