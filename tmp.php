<?php
  include("includes/header.php");
  include("connection.php");
if(!isset($_SESSION['uid'])) {
   header("Location: index.php");
 }
  $week = array(0=>'SUN', 1=>'MON', 2=>'TUE', 3=>'WED', 4=>'THU', 5=>'FRI', 6=>'SAT');

  $d = date("Y-m-d", time());
  $q = "SELECT * FROM `matches` left join (leagueDetails) on (matches.leagueId=leagueDetails.leagueId) 
  where matchDate='$d' and leagueDetails.leagueId in (17,18,19,20,34,23) order by matches.matchDate ASC, matches.matchTime ASC, matches.leagueId ASC";
  
  $res = $mysqli->query($q);
  $res2 = $mysqli->query("SELECT DISTINCT country FROM leagueDetails");

?>
		<table id="scoreTable" class="table table-fixed table-bordered table-condensed text-center">

	      <tbody>
	        <?php 
	        $i = 1;
	        while ($row = $res->fetch_assoc()) {
	        	$placeH = $mysqli->query("SELECT place FROM tables where leagueId=".$row['leagueId']." and team='".$row['homeTeam']."'")->fetch_array()[0];
	        	$placeA = $mysqli->query("SELECT place FROM tables where leagueId=".$row['leagueId']." and team='".$row['awayTeam']."'")->fetch_array()[0];
	        	$bet = '-';
	        	$q1 = "SELECT bet FROM playedMatches left join series on series.seriesId=playedMatches.seriesId where matchId=".$row['matchId']." and pps=0 and userId=".$_SESSION['uid'];
	        	$t = $mysqli->query($q1);
	        	if ($b=$t->fetch_array()) {
	        		$bet = $b[0];
	        	}
	        ?>

	        <tr id="<?php echo $row['matchId'];?>">
	          <td><?php echo $i; ?></td>
	          <td><?php echo $row['matchDate']; ?></td>
	          <td><?php echo substr($row['matchTime'], 0, -3); ?></td>
	          <td><img src="img/<?=$row['country'] ?>.png" title="<?=$row['country']?>"/> <?php echo $row['displayName']; ?></td>
	          <td><?php 
	              echo "[".$placeH."] ".$row['homeTeam'];
	          ?></td>
	          <td>-</td>
	          <td><?php 
	            echo "[".$placeA."] ".$row['awayTeam'];
	          ?></td>
	          
	          <td><?php echo $row['result']; ?></td>
	          <td><?php echo $row['resultShort']; ?></td>	 
	          <td><?=$bet?></td>        
	          </tr>
	        <?php
	        $i ++;
	        }
	        ?>
	      </tbody>
	      <thead>
	      	<tr>
	      		<th><input type="hidden"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="date"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="time"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="league"></th>
	      		<th><input type="text" name="search_engine" class="search_init" placeholder="home team"></th>
	      		<th><input type="hidden"></th>
	      		<th><input type="text" name="search_engine" class="search_init" placeholder="away team"></th>
	      		<th><input type="hidden"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="result"></th>
	      		<th><input type="hidden"></th>

	      	</tr>
	        <tr>
	          <th>#</th>
	          <th>Date</th>
	          <th>Time</th>
	          <th>League</th>
	          <th>Home Team</th>
	          <th>-</th>
	          <th>Away Team</th>
	          <th>Result</th>
	          <th>Result</th>
	          <th>Bet</th>
	        </tr>
	      </thead>
	    </table>
	    <div class="pointer"> 
			<!-- Bet: <?=$totalBet?><br>
			Income: <?=$totalIncome?><br>
			Profit: <?=$totalProfit?> -->
		</div>
	    <script type="text/javascript">
		var asInitVals = new Array();
	
	    $(document).ready(function() {	
	    /* Init DataTables */
		    var oTable = $('#scoreTable').dataTable({
		    	    "iDisplayLength": 100,
		    	    "bJQueryUI": true,
		    	    "sPaginationType": "full_numbers",
					"sDom": '<"top" lf><"toolbar">irpti<"bottom"pT><"clear">',
					"oTableTools": {
						"sSwfPath": "dt/media/swf/copy_csv_xls_pdf.swf"
					},
					"aoColumns": [
						//{ "sWidth": "2%", "bSortable": false}, // image column width 
						{ "sWidth": "60px" }, // 1st column width 
						{ "sWidth": "100px" }, // 2nd column width 
						{ "sWidth": "40px" }, // 3rd column 
						{ "sWidth": "40px" }, // 4th column 
						{ "sWidth": "" }, // 5th column 
						{ "sWidth": "5px" }, // 6th column 
						{ "sWidth": "" }, 
						{ "sWidth": "30px" }, // 13th column 
						{ "sWidth": "30px" },
						{ "sWidth": "30px" }
						 // 14th column 
					]
			});
	   	$("div.toolbar").addClass("text-center");
		    /* Apply the jEditable handlers to the table */
		    
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
			
			
			// $('#scoreTable thead tr').each( function () {
			// 	this.insertBefore( nCloneTh, this.childNodes[0] );
			// } );
			
			// $('#scoreTable tbody tr').each( function () {
			// 	this.insertBefore(  nCloneTd.cloneNode( true ), this.childNodes[0] );
			// } );
		
			/* Add event listener for opening and closing details
			 * Note that the indicator for showing which row is open is not controlled by DataTables,
			 * rather it is done here
			 */
			
		} );
		new Tether({
          element: '.pointer',
          attachment: 'middle right',
          targetAttachment: 'middle left',
          targetModifier: 'scroll-handle',
          target: document.body
        });
	    </script>

<?php
  include("includes/footer.php");
?>	
