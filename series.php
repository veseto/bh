<?php
	include("includes/header.php");
	include("connection.php");
	if(!isset($_SESSION['uid'])) {
	  	header("Location: index.php");
	}
	if (isset($_GET['inactive']) && $_GET['inactive']=='true') {
		$q = "select distinct playedMatches.seriesId, series.team, series.length, series.active, leagueDetails.country, leagueDetails.displayName 
			from playedMatches left join (matches, series, leagueDetails) 
			on (playedMatches.matchId=matches.matchId and series.seriesId=playedMatches.seriesId and matches.leagueId=leagueDetails.leagueId) 
			where userId=".$_SESSION['uid']." and active=0 order by series.active desc, series.length desc, matches.leagueId asc, series.team asc";

	} else {
		$q = "select distinct playedMatches.seriesId, series.team, series.length, series.active, leagueDetails.country, leagueDetails.displayName 
			from playedMatches left join (matches, series, leagueDetails) 
			on (playedMatches.matchId=matches.matchId and series.seriesId=playedMatches.seriesId and matches.leagueId=leagueDetails.leagueId) 
			where userId=".$_SESSION['uid']." and active=1 order by series.active desc, series.length desc, matches.leagueId asc, series.team asc";
	}
			//echo "$q<br>";
	$res = $mysqli->query($q);
	$pageTitle = "Acitve Series";
?>
	<table id="series" class="table table-hover table-bordered table-condensed span12 text-center">
		<thead>
        <tr>
          <th><input type="hidden"></th>
          <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="league"></th>
          <th><input type="text" name="search_engine" class="search_init" placeholder="team"></th>
          <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="length"></th>
          <th><input type="hidden"></th>
          <th><input type="hidden"></th>
        </tr>
        <tr>
          <th>#</th>
          <th>League</th>
          <th>Team</th>
          <th>Length</th>
          <th>Value</th>
          <th>Details</th>
        </tr>
      </thead>
      <tbody>
<?php
	$i = 1;
	while($row = $res->fetch_array()) {
		$tmp = $mysqli->query("SELECT bet, betSoFar from playedMatches where seriesId=".$row['seriesId']." and userId=".$_SESSION['uid']." order by currentLength desc limit 1")->fetch_array();
		?>
		<tr>
			<td><?php echo $i;?></td>
	        <td><img src="img/<?=$row['country'] ?>.png"/> <?php echo $row['displayName']; ?></td>
			<td><?php echo $row['team'];?></td>
			<td>
			<?php
				if ($row['active'] === '0') {
					echo $row['length'] - 1;
				} else {
					echo $row['length'];
				}
			?>	
			</td>
			<td><?php echo $tmp[0]+$tmp[1];?></td>
			</td>
			<td><?php echo "<a href=seriesdetails.php?series=".$row['seriesId'].">View</strong></a><br>";?></td>
		<?php
		++$i;
	}
?>
	</tbody>
</table>
<script type="text/javascript">
    var asInitVals = new Array();

    $(document).ready(function() {
    /* Init DataTables */
    var oTable = $('#series').dataTable({
    	    "iDisplayLength": 25,
    	    "bJQueryUI": true,
    	    "sPaginationType": "full_numbers",
			"sDom": '<"top" lf><"toolbar">irpti<"bottom"pT><"clear">',
			"oTableTools": {
				"sSwfPath": "dt/media/swf/copy_csv_xls_pdf.swf"
			}
    });
   	$("div.toolbar").html("<h4 style='margin:0;'><?=$pageTitle;?></h4>");
   	$("div.toolbar").addClass("text-center");
    $("thead input").keyup( function () {
      /* Filter on the column (the index) of this element */
        oTable.fnFilter( this.value, $("thead input").index(this));
      } );
    
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