<?php
	$endings = array('2013-2014');
	$handle = fopen("oddsurls.php", "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {
	    	// echo "$line<br>";
	        if (trim($line) <> "") {
	        	$l = explode("#", $line);
	        	$leagueId = trim($l[0]);
	        	$url = trim($l[1]);
	        	foreach ($endings as $season) {
        			$tmp = $url."-".$season."/results/?stage=z70CDsYn";
        			$data = file_get_contents($tmp);

					$dom = new domDocument;

					@$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
					$dom->preserveWhiteSpace = false;
					$rows = $dom->getElementById ('leagueresults_div')->getElementsByTagName('tr');
					// $rows = $tables->getElementsByTagName('tr');
					if ($rows->length == 1) {
						echo "skip: $tmp";
						continue;
					}
					echo "$tmp<br>";
					foreach ($rows as $row) {
						// echo $row->getAttribute("class")." ";
						$cols = $row->getElementsByTagName('td');
						foreach ($cols as $col) {
							echo $col->nodeValue." ";
							print_r($col);
						}
						echo "<br>";
					}
	        	}
	        }
	    }
	}
?>