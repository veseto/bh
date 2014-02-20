<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
	include("../connection.php");
	$months = array('January' => '01', 'February' => '02', 'March' => '03', 
					'April' => '04', 'May' => '05', 'June' => '06', 'July' => '07', 
					'August' => '08', 'September' => '09', 'October' => '10', 'November' => '11', 'December' => '12');
	
	$handle = fopen("fcstatsurls-new.php", "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {
	        if (trim($line) <> "") {
	        	$l = explode("#", $line);
	        	$leagueId = trim($l[0]);
	        	$season = trim($l[1]);
	        	$url = trim($l[2]);
				$data = file_get_contents($url);

				$dom = new domDocument;

				@$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
				$dom->preserveWhiteSpace = false;
				

				$finder = new DomXPath($dom);
				$classname="matchesListMain";
				$nodes = $finder->query("//*[contains(@class, '$classname')]");
				$rows = $nodes->item(0)->getElementsByTagName("tr");
				$date = "";
				foreach ($rows as $row) {
					$tds = $row->getElementsByTagName("td");
					if ($tds->length == 2) {
						$date = $tds->item(0)->nodeValue;
					} else {
						$home = $tds->item(0)->nodeValue;
						$home = str_replace("'", " ", $home);
						//$h = $mysqli->query("SELECT xscores from mapping where fcstats='$home'")->fetch_array()[0];
						$res = $tds->item(1)->nodeValue;
						$away = $tds->item(2)->nodeValue;
						$away = str_replace("'", " ", $away);
						// $a = $mysqli->query("SELECT xscores from mapping where fcstats='$away'")->fetch_array()[0];
						$res = trim(str_replace(":", "-", $res));
						$resShort = '-';
						$tmp = explode("-", $res);
						
						if ($tmp[0] == $tmp[1]) {
							$resShort = 'D';
						} else if ($tmp[0] > $tmp[1]) {
							$resShort = 'H';
						} else {
							$resShort = 'A';
						}
						
						$tmpDate = explode(" ", $date); 
						$day = trim($tmpDate[0]);
						$month = $months[$tmpDate[1]];
						$year = trim($tmpDate[2]);
						if (strlen($day) == 1) {
							$day = "0".$day;
						}
						$strDate = $year."-".$month."-".$day;
						$q0 = "SELECT count(*) from history where home='$home' and away='$away' and mdate='$strDate' and season='$season' and leagueId=$leagueId";
						//echo "$q0<br>";
						$count = $mysqli->query($q0)->fetch_array()[0];
						if ($count == 0 || $count == '0') {
							$mysqli->query("INSERT INTO history
											(leagueId, mdate, season, home, away, result, short)
											values ($leagueId, '$strDate', '$season', '$home', '$away', '$res', '$resShort')");
							echo $mysqli->error;
						}
					}
				}
	        }
	    }
	} else {
	    // error opening the file.
	}
?>
</body>