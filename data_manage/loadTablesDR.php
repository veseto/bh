<?php
	//include("header.php");
	include("../connection.php");
  	$start = time();
	$leagues = array('USA' => array('MAJOR+LEAGUE+SOCCER'),
	 				 'JAPAN' => array('J.LEAGUE+DIV.1'));
						//'POLAND' => array('DIVISION+1'), 
	 				 //'DENMARK' => array('SUPERLIGAEN'),
	 				 // 'CZECH+REPUBLIC' => array('DIVISION+1'));
	 				//  'BELGIUM' => array('PRO+LEAGUE'),
	 				//  'SPAIN' => array('PRIMERA+DIVISION'), 
	 				//  'AUSTRALIA' => array('A-LEAGUE'),
	 				//  'ENGLAND' => array('PREMIER+LEAGUE', 'CHAMPIONSHIP', 'LEAGUE+ONE', 'LEAGUE+TWO'), 
					 // 'SCOTLAND' => array('PREMIERSHIP', 'CHAMPIONSHIP', 'LEAGUE+ONE', 'LEAGUE+TWO'),
				 	//  'GERMANY' => array('BUNDESLIGA', '2.+BUNDESLIGA', '3.+LIGA'),
				 	//  'FRANCE' => array('LIGUE+1', 'LIGUE+2'),
				 	//  'ITALY' => array('SERIE+A', 'SERIE+B'),
				 	//  'TURKEY' => array('SUPER+LIG'));
				 	 //'MEXICO' => array('CLAUSURA'));

	// $leagues2 = array('1' => 'http://int.soccerway.com/national/england/premier-league/20132014/regular-season/r21322/', 
	// 					'2' => 'http://int.soccerway.com/national/england/championship/20132014/regular-season/r21389/', 
	// 					'3' => 'http://int.soccerway.com/national/england/league-one/20132014/regular-season/r21395/',
	// 					'4' => 'http://int.soccerway.com/national/england/league-two/20132014/regular-season/r21398/',
	// 					'5' => 'http://int.soccerway.com/national/scotland/premier-league/20132014/1st-phase/r21449/',
	// 					'6' => 'http://int.soccerway.com/national/scotland/first-division/20132014/regular-season/r21563/',
	// 					'7' => 'http://int.soccerway.com/national/scotland/second-division/20132014/regular-season/r21564/',
	// 					'8' => 'http://int.soccerway.com/national/scotland/third-division/20132014/regular-season/r21575/',
	// 					'9' => 'http://int.soccerway.com/national/germany/bundesliga/20132014/regular-season/r21344/',
	// 					'10' => 'http://int.soccerway.com/national/germany/2-bundesliga/20132014/regular-season/r21345/',
	// 					'11' => '',
	// 					'12' => '');

	foreach ($leagues as $key => $value) {
		foreach ($value as $league) {
			$url = "http://www.xscores.com/soccer/Results.jsp?sport=1&countryName=$key&leagueCup=L&leagueName=$league&leagueName1=$league&seasonName=2013%2F2014&leagueName1=&groupBy=4&viewLast=0&result=5#.UudoTRD8JaT";
		 	//echo "$url <br>";
		 	$data = file_get_contents($url);

			$dom = new domDocument;

			@$dom->loadHTML($data);
			$dom->preserveWhiteSpace = false;
			//echo "$key->$league";
			$tables = $dom->getElementsByTagName ('table');

			foreach ($tables as $table) {

				$rows = $table->getElementsByTagName('tr');
				$tmp = $rows->item(0)->getElementsByTagName('td')->item(1)->nodeValue;
				echo "$tmp <br>";
				if ($tmp === 'HOME'){
					foreach ($rows as $row) {
						$cols = $row->getElementsByTagName('td');
						if ($cols->length > 5 && $cols->item(0)->nodeValue != 'P.'){
							$home = array();
							$away = array();
							$total = array();
						    $place = $cols->item(0)->nodeValue;
						    $team = $cols->item(1)->nodeValue;
						    for ($i = 2; $i < $cols->length; $i ++) {
						    	$td = $cols->item($i);
						    	$name = $td->getAttribute("name");
						    	if ($name === 'wdl0') {
						    		array_push($home, $td->nodeValue);
						    	} else if ($name === 'wdl1') {
						    		array_push($away, $td->nodeValue);
						    	} else if ($name === 'wdl2') {
						    		array_push($total, $td->nodeValue);
						    	}
						    }
						    $leagueName = str_replace("+", " ", $league);
						     $countryName = str_replace("+", " ", $key);
						    $q = "SELECT leagueId from leagueDetails where country='$countryName' and name='$leagueName'";
						    $leagueId = $mysqli->query($q)->fetch_array()[0];
						    $home = serialize($home);
						    $away = serialize($away);
						    $total = serialize($total);
						    $q0 = "INSERT INTO tables (place, home, away, total, team, leagueId) values ($place, '$home', '$away', '$total', '$team', $leagueId)";
						    //$q0 = "UPDATE tables set place=$place, home='$home', away='$away', total='$total' where leagueId=$leagueId and team='$team'";
						    //echo "$q0<br>";
						    $mysqli->query($q0);
						    echo $mysqli->error;
						    //echo "$key->$league $leagueId <br>";
						}
					}
				}
			}
		}
	}

	echo time()-$start;

?>