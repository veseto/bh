<?php 
	include("includes/header.php");
	include("connection.php");
	if(!isset($_SESSION['uid'])) {
	   header("Location: index.php");
	 }
 	$pageTitle = "Automatically refreshes every 30 seconds";
?>

<table id="scoreTable" class="table table-fixed table-bordered table-condensed text-center">
	      <thead>
	      	<tr>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="time"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="state"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="min"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="league"></th>
	      		<th><input type="text" name="search_engine" class="search_init" placeholder="home"></th>
	      		<th><input type="hidden"></th>
	      		<th><input type="text" name="search_engine" class="search_init " placeholder="away"></th>
	      		<th><input type="hidden"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="res"></th>
	      		<th><input type="hidden"></th>
	      	</tr>
	        <tr>
				<th>Time</th>
				<th>State</th>
				<th>Min</th>
				<th>League</th>
				<th>Home Team</th>
				<th>-</th>
				<th>Away Team</th>
				<th>income home</th>
				<th>income away</th>
				<th>result</th>
				<th>result</th>
	        </tr>
	      </thead>
	      <tbody>
	      
<?php
	
	$arr = array('england.gif'=>array('PR'=>'PR', 'ECH'=>'CH', 'EL1'=>'L1', 'EL2'=>'L2'), 
				'germany.gif'=>array('BL'=>'BL', '2BL'=>'2BL', '3LG'=>'3L'), 
				'scotland.gif'=>array('PR'=>'PR', 'SCH'=>'CH', 'SL1'=>'L1', 'SL2'=>'L2'), 
				'turkey.gif'=>array('SL'=>'SL'), 
				'italy.gif'=>array('A'=>'A', 'B'=>'B'), 
				'australia.gif'=>array('D1'=>'AL'), 
				'spain.gif'=>array('PD'=>'PD'), 
				'poland.gif'=>array('D1'=>'D1'), 
				'france.gif'=>array('L1'=>'L1', 'L2'=>'L2'),
				'romania.gif'=>array('L1' => 'L1'),
				'denmark.gif'=>array('SL' => 'SL'),
				'croatia.gif'=>array('D1' => 'D1'),
				'mexico.gif'=>array('CL' => 'CL'),
				'austria.gif'=>array('BL' => 'BL'),
				'czech republic.gif'=>array('D1' => 'D1'),
				'cyprus.gif'=>array('D1' => 'D1'), 
				'slovakia.gif'=>array('D1' => 'D1'));

 	$data = file_get_contents("http://www.xscores.com/soccer/soccer.jsp?sports=soccer&flag=sportData#.Ut5V72T8K2w");

	$dom = new domDocument;

	@$dom->loadHTML($data);
	$dom->preserveWhiteSpace = false;
	$tables = $dom->getElementById ('scoretable');

	$rows = $tables->getElementsByTagName('tr');
	foreach ($rows as $row) {
	    $cols = $row->getElementsByTagName('td');
	    if ($cols->length == 1) {
	    	$date = $cols->item(0)->nodeValue;
	    } else {	    	
	    	if ($cols->item(0)->nodeValue != "K/O") {
	    		//echo "$country $leagueName $date ";
			    $time = $cols->item(0)->nodeValue;
			    $stat = $cols->item(1)->nodeValue;
			    $currTime = $cols->item(2)->nodeValue;
			    $home = $cols->item(5)->nodeValue;
			    $league = $cols->item(4)->nodeValue;
			    $attr = $cols->item(3)->getElementsByTagName('img')->item(0)->attributes;
			    foreach ($attr as $a) {
			    	$href = $a->value;
			    }
			    $gif = explode("/", $href)[4];
			    $away = $cols->item(9)->nodeValue;
			    $ft = $cols->item(14)->nodeValue;
			    
			
		$inPlayed = false;
	  	$d = date("Y-m-d", time());
	  	$q = "select * from playedMatches
						left join (matches, series)
						on (matches.matchId = playedMatches.matchId and series.seriesId=playedMatches.seriesId) 
						where (bet<>0 or betSoFar<>0) and matches.matchDate='$d' and homeTeam='$home' and awayTeam='$away' and userId=".$_SESSION['uid'];
		// echo "$q<br>";
		$bet = "";
		$bet1 = "";
		if ($res = $mysqli->query($q)) {
			if ($match = $res->fetch_assoc()){
				$inPlayed = true;
				$bet = $match['income'];
				if ($match2 = $res->fetch_assoc()) {
					$bet1 =  $match2['income'];
				}
				if ($match['homeTeam'] == $match['team']) {
						$betH = $bet;
						$betA = $bet1;
				} else if ($match['awayTeam'] === $match['team']) {
					$betH = $bet1;
					$betA = $bet;				
				} else {
					$betH = $bet;
					$betA = '-';
				}
			}
		}

		if (isset($_GET['all']) || (isset($_GET['played']) && $inPlayed) || (!isset($_GET['played']) && !isset($_GET['all']) && array_key_exists($gif, $arr) && array_key_exists($league, $arr[$gif]))) {
		?>
		<tr>
		<td><?php echo $time; ?></td>
		<td
			<?php 
			if (trim($stat) === "1 HF") {
				echo " class='livescore1HF'";
			} else if (trim($stat) === "2 HF") {
				echo " class='livescore2HF'";
			}?>
		><?=$stat ?></td>
		<td><?php echo $currTime; ?></td>
		<td><img src="<?=$href ?>"/> <?php echo $arr[$gif][$league]; ?></td>
		<td><?php echo $home; ?></td>
		<td>-</td>
		<td><?php echo $away; ?></td>
		<td><?=$betH ?></td>
		<td><?=$betA ?></td>
		<td <?php 
			$short = "-";
			$tmp = explode("-", $ft);
		    if ($tmp[0] != "" && $tmp[1] != ""){
			    if ($tmp[0] === $tmp[1]){
			    	$short="D";
			    	echo " class='livescoreScore'";
			    } else if ($tmp[0]>$tmp[1]) {
			    	$short='H';
			    } else if ($tmp[0]<$tmp[1]) {
			    	$short='A';
			    }
			}?>><?=$ft?></td>
		<td><?=$short ?></td>
		</tr>
		<?
		}
		}
		}
		// echo $time." ".$stat." ".$currTime." ".$league." ".$home." ".$away." ".$ft."<br>";
	}

?>
	</tbody>
</table>
<script type="text/javascript">
	    $(document).ready(function() {
	    /* Init DataTables */
	    var oTable = $('#scoreTable').dataTable({
	    	    "iDisplayLength": 100,
	    	    "bJQueryUI": true,
	    	    "sPaginationType": "full_numbers",
				"sDom": '<"top"lf><"toolbar">irpti<"bottom"p><"clear">'
	    });
		$("div.toolbar").html("<h5 style='margin:0;'><?=$pageTitle;?></h5>");
	   	$("div.toolbar").addClass("text-center");

	   	 $("thead input").keyup( function () {
			/* Filter on the column (the index) of this element */
				oTable.fnFilter( this.value, $("thead input").index(this));
			} );
			
			/*
			 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
			 * the footer
			 */
			$("thead input").each( function (i) {
				asInitVals[i] = this.value;
			} );
			
			$("thead input").focus( function () {
				if ( this.className == "search_init" )
				{
					this.className = "";
					this.value = "";
				}
			} );
			
			$("thead input").blur( function (i) {
				if ( this.value == "" )
				{
					this.className = "search_init";
					this.value = asInitVals[$("thead input").index(this)];
				}
			} );
	});
	   
</script>

<?php
	include("includes/footer.php");
?>