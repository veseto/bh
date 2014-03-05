<?php
  include("includes/header.php");
  include("connection.php");
if(!isset($_SESSION['uid'])) {
   header("Location: index.php");
 }
  $week = array(0=>'SUN', 1=>'MON', 2=>'TUE', 3=>'WED', 4=>'THU', 5=>'FRI', 6=>'SAT');

  date_default_timezone_set('Europe/Sofia');
  $heading = "";
  $str = "";
  $getStr = "";
  if (isset($_GET['day'])) {
    $getStr = "day=".$_GET['day']."&";
    if ($_GET['day'] === 'all') {
   	  	$d = date("Y-m-d", time());
   	  	$heading = "greater than $d";
      	$str = " matchDate>='$d' and ";
      	$pageTitle = "PPM: All Available";

    } else if ($_GET['day'] === 'today') {
      	$d = date("Y-m-d", time());
   	  	$heading = "$d";
      	$str = " matchDate='$d' and ";
      	$date = date("d.m.Y", time());
      	$wd = $week[date('w', time())];
      	$pageTitle = "PPM: $date ($wd)";
    } else if ($_GET['day'] > 0){
      	$d0 = date("Y-m-d", time());
      	$d = date("Y-m-d", time()+(86400*($_GET['day'] - 1)));
      	$str = " matchDate>='$d0' and matchDate<='$d' and ";
      	$date1 = date("d.m.Y", time());
      	$wd1 = $week[date('w', time())];
		$date2 = date("d.m.Y", time()+(86400*($_GET['day'] - 1)));
      	$wd2 = $week[date('w', time()+(86400*($_GET['day'] - 1)))];

      	$pageTitle = "PPM: $date1 ($wd1) - $date2 ($wd2)";
    } else {
    	$d0 = date("Y-m-d", time());
    	$d = date("Y-m-d", time()+(86400*($_GET['day'])));
      	$str = " matchDate<'$d0' and matchDate>='$d' and ";
      	$date1 = date("d.m.Y", time()-86400);
      	$wd1 = $week[date('w', time()-86400)];
		$date2 = date("d.m.Y", time()+(86400*($_GET['day'])));
      	$wd2 = $week[date('w', time()+(86400*($_GET['day'])))];

      	$pageTitle = "PPM: $date2 ($wd2) - $date1 ($wd1)";
    }
  } else {
  	$d = date("Y-m-d", time());
  	$str = " matchDate < '$d' and ";
  	$date1 = date("d.m.Y", time());
  	$wd1 = $week[date('w', time())];

  	$pageTitle = "PPM: Before $date2 ($wd2)";
  }

  
  $q = "SELECT * FROM `playedMatches` left join (matches, series, leagueDetails) on (playedMatches.matchId=matches.matchId and playedMatches.seriesId=series.seriesId and series.leagueId=leagueDetails.leagueId) where ".$str." userId=".$_SESSION['uid']." and playedMatches.pps=0 and playedMatches.ignored=0 order by matches.matchDate ASC, matches.matchTime ASC, matches.leagueId ASC, playedMatches.currentLength DESC";

  // if (isset($_GET['country'])) {
  //   $q = "SELECT * FROM `playedMatches` left join (matches, series, leagueDetails) on (playedMatches.matchId=matches.matchId and playedMatches.seriesId=series.seriesId and series.leagueId=leagueDetails.leagueId) where ".$str." userId=".$_SESSION['uid']." and playedMatches.ignored=0 and country='".$_GET['country']."' order by matches.leagueId ASC, playedMatches.seriesId ASC, matches.matchDate ASC";
  //   if (isset($_GET['league'])) {
  //     $q = "SELECT * FROM `playedMatches` left join (matches, series, leagueDetails) on (playedMatches.matchId=matches.matchId and playedMatches.seriesId=series.seriesId and series.leagueId=leagueDetails.leagueId) where ".$str." userId=".$_SESSION['uid']." and playedMatches.ignored=0 and country='".$_GET['country']."' and name='".$_GET['league']."' order by matches.leagueId ASC, playedMatches.seriesId ASC, matches.matchDate ASC";  
  //     if (isset($_GET['team'])) {
  //       $q = "SELECT * FROM `playedMatches` left join (matches, series, leagueDetails) on (playedMatches.matchId=matches.matchId and playedMatches.seriesId=series.seriesId and series.leagueId=leagueDetails.leagueId) where ".$str." userId=".$_SESSION['uid']." and playedMatches.ignored=0 and country='".$_GET['country']."' and name='".$_GET['league']."' and series.team='".$_GET['team']."' order by matches.leagueId ASC, playedMatches.seriesId ASC, matches.matchDate ASC";  
  //     }
  //   }
  // }
  $res = $mysqli->query($q);
  $res2 = $mysqli->query("SELECT DISTINCT country FROM leagueDetails");

?>
		<table id="scoreTable" class="table table-fixed table-bordered table-condensed text-center">

	      <tbody>
	        <?php 
	        $i = 1;
	        $totalBet = 0;
	        $totalIncome = 0;
	        $totalProfit = 0;
	        $totalBSF = 0;
	        while ($row = $res->fetch_assoc()) {
	        	$totalBet += $row['bet'];
	        	$totalProfit += $row['profit'];
	        	$totalIncome += $row['income'];
	        	$totalBSF += $row['betSoFar'];
	        	$placeH = $mysqli->query("SELECT place FROM tables where leagueId=".$row['leagueId']." and team='".$row['homeTeam']."'")->fetch_array()[0];
	        	$placeA = $mysqli->query("SELECT place FROM tables where leagueId=".$row['leagueId']." and team='".$row['awayTeam']."'")->fetch_array()[0];
	        ?>

	        <tr id="<?php echo $row['matchId'];?>">
	          <td class="center" id="<?php echo $row['seriesId'];?>"><img class="clickable" src="dt/examples/examples_support/details_open.png"></td>	          
	          <td><?php echo $i; ?></td>
	          <td><?php echo $row['matchDate']; ?></td>
	          <td><?php echo substr($row['matchTime'], 0, -3); ?></td>
	          <td><img src="img/<?=$row['country'] ?>.png" title="<?=$row['country']?>"/> <?php echo $row['displayName']; ?></td>
	          <td><?php 
	              echo "<a href='seriesdetails.php?series=".$row['seriesId']."'>[".$placeH."] ".$row['homeTeam'] ." </a>";
	          ?></td>
	          <td>-</td>
	          <td><?php 
	            echo "<a href='seriesdetails.php?series=".$row['seriesId']."'> [".$placeA."] ".$row['awayTeam']." <span style='color: red; font-weight: bold;'>(".$row['currentLength'].")</span> </a>";
	          ?></td>
	          <td id="<?php echo $row['seriesId'];?>"><?php echo $row["betSoFar"]; ?></td>
	          <td <?php if ($row['resultShort'] == '-') echo 'class="editable warning"';?> id="<?php echo $row['seriesId'];?>"><?php echo $row['bet']; ?></td>
	          <td <?php if ($row['resultShort'] == '-') echo 'class="editable warning"';?> id="<?php echo $row['seriesId'];?>"><?php echo $row['odds']; ?></td>
	          <td><?php echo $row['income']; ?></span></td>
	          <td><?php echo $row['profit']; ?></td>
	          <td><?php echo $row['result']; ?></td>
	          <td><?php echo $row['resultShort']; ?></td>	         
	          </tr>
	        <?php
	        $i ++;
	        }
	        ?>
	      </tbody>
	      <thead>
	      	<tr>
	      		<th><input type="hidden"></th>
	      		<th><input type="hidden"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="date"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="time"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="league"></th>
	      		<th><input type="text" name="search_engine" class="search_init" placeholder="home team"></th>
	      		<th><input type="hidden"></th>
	      		<th><input type="text" name="search_engine" class="search_init" placeholder="away team"></th>
	      		<th><input type="hidden"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="bet"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="odds"></th>
	      		<th><input type="hidden"></th>
	      		<th><input type="hidden"></th>
	      		<th><input type="hidden"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="result"></th>
	      	</tr>
	      	<tr>
	      		<th></th>	
	      		<th></th>
	      		<th></th>
	      		<th></th>
	      		<th></th>
	      		<th></th>
	      		<th></th>
	      		<th></th>
	      		<th><?=$totalBSF?></th>
	      		<th><?=$totalBet?></th>
	      		<th></th>
	      		<th><?=$totalIncome?></th>
	      		<th><?=$totalProfit?></th>
      			<th></th>
      			<th></th>
	      	</tr>
	        <tr>
	          <th></th>
	          <th>#</th>
	          <th>Date</th>
	          <th>Time</th>
	          <th>League</th>
	          <th>Home Team</th>
	          <th>-</th>
	          <th>Away Team</th>
	          <th>BSF</th>
	          <th>Bet</th>
	          <th>Odds</th>
	          <th>Income</th>
	          <th>Profit</th>
	          <th>Result</th>
	          <th>Result</th>
	        </tr>
	      </thead>
	      <tfoot>
	      	<tr>
	      		<th></th>	
	      		<th></th>
	      		<th></th>
	      		<th></th>
	      		<th></th>
	      		<th></th>
	      		<th></th>
	      		<th></th>
	      		<th><?=$totalBSF?></th>
	      		<th><?=$totalBet?></th>
	      		<th></th>
	      		<th><?=$totalIncome?></th>
	      		<th><?=$totalProfit?></th>
      			<th></th>
      			<th></th>
	      	</tr>
	      </tfoot>
	    </table>
	    <div class="pointer"> 
			<!-- Bet: <?=$totalBet?><br>
			Income: <?=$totalIncome?><br>
			Profit: <?=$totalProfit?> -->
		</div>
	    <script type="text/javascript">
		var asInitVals = new Array();

		/* Formating function for row details */
		function fnFormatDetails ( oTable, nTr, id )
		{
			var text = '';
			var aData = oTable.fnGetData( nTr );
			var team = aData[4];
			var promise = testAjax(id, aData[2], aData[3]);
			promise.success(function (data) {
			  text = data;
			});
			return text;
		}

		function testAjax(seriesId, mDate, mTime) {
		  var url = "ppmDetails.php?seriesId=" + seriesId + "&date=" + mDate + "&time=" + mTime; 
		  return $.ajax({
		  	async: false,
		    url:url
		  	});
		}

		
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
						{ "sWidth": "10px" , "bSortable": false}, // image column width 
						{ "sWidth": "60px" }, // 1st column width 
						{ "sWidth": "100px" }, // 2nd column width 
						{ "sWidth": "40px" }, // 3rd column 
						{ "sWidth": "40px" }, // 4th column 
						{ "sWidth": "" }, // 5th column 
						{ "sWidth": "5px" }, // 6th column 
						{ "sWidth": "" }, // 7th column 
						{ "sWidth": "50px" }, // 8th column BET SO FAR
						{ "sWidth": "50px" }, // 9th column 
						{ "sWidth": "50px" }, // 10th column 
						{ "sWidth": "50px" }, // 11th column 
						{ "sWidth": "50px" }, // 12th column 
						{ "sWidth": "30px" }, // 13th column 
						{ "sWidth": "30px" } // 14th column 
					]
			});
	   	$("div.toolbar").html("<h5 style='margin:0;'><?=$pageTitle;?></h5>");
	   	$("div.toolbar").addClass("text-center");
		    /* Apply the jEditable handlers to the table */
		    oTable.$('td.editable').editable( 'save.php', {
		        "callback": function( sValue, y ) {
		           //	alert(y[0]);
		            var aPos = oTable.fnGetPosition( this );
		            var arr = sValue.split("#");

		            oTable.fnUpdate( arr[0], aPos[0], 9 );
		           	oTable.fnUpdate( arr[1], aPos[0], 10 );
		            oTable.fnUpdate( arr[2], aPos[0], 11 );
		            oTable.fnUpdate( arr[3], aPos[0], 12 );
		            oTable.fnUpdate( arr[4], aPos[0], 8 );
		    
		            //oTable.fnClearTable();
	            	//oTable.fnReloadAjax() ;
		        },
		        "submitdata": function ( value, settings ) {
		            return {
		                "row_id": this.parentNode.getAttribute('id'),
		                "column": oTable.fnGetPosition( this )[2]
		            };
		        },
		        "height": "18px",
		        "width": "28px"
		    } );
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
			/*
			 * Insert a 'details' column to the table
			 */
			var nCloneTh = document.createElement( 'th' );
			var input = document.createElement("input");
			input.setAttribute("type", "hidden");
			nCloneTh.appendChild(input);

			var nCloneTd = document.createElement( 'td' );
			nCloneTd.innerHTML = '<img src="dt/examples/examples_support/details_open.png">';
			nCloneTd.className = "center";
			
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
			$('#scoreTable tbody').on('click', '.clickable' ,function () {
				var nTr = this.parentNode.parentNode;
				var id = this.parentNode.getAttribute('id');
				if ( this.src.match('details_close') )
				{
					/* This row is already open - close it */
					this.src = "dt/examples/examples_support/details_open.png";
					oTable.fnClose( nTr );
				}
				else
				{
					/* Open this row */
					this.src = "dt/examples/examples_support/details_close.png";
					oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr, id), 'details' );
				}
			} );
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