<?php
	include("includes/header.php");
	include("connection.php");
	if(!isset($_SESSION['uid'])) {
	   header("Location: index.php");
	 }
	$heading = "ENG PR:1 CH:2 L1:3 L2:4 | SCO PR:5 CH:6 L1:7 L2:8 | GER BL:9 2BL:10 3L:11 | FRA L1:12 L2:13 CN:25 | ITA A:14 B:15 | TUR 16 | AUS 17 | POL 18 | DNK 19 | RUS PL:20 D1:21 | ESP 23 | CZE 27";
	$league = $_GET['league'];
	$l = $mysqli->query("SELECT country, name from leagueDetails where leagueId=$league")->fetch_array();
	$pageTitle = $l[0]." ".$l[1].", length $length";
	$res = $mysqli->query("SELECT DISTINCT home from history where leagueId=$league");
	$lengths = array();
	while ($team = $res->fetch_array()) {
		$count = 0;
		$q = "SELECT * FROM history where (home='$t' or away='$t') and leagueId=$league order by mdate";
		//echo "$q<br>";
		$res1 = $mysqli->query($q);
		//echo "string";
		$i = 0;
		while ($match = $res1->fetch_assoc()) {
			if ($match['short'] == 'D') {
				if (array_key_exists($count, $lengths)) {
					$lengths[$count] ++;
				} else {
					$lengths[$count] = 1;
				}
				$count = 0;
			} else {
				$count ++;
			}
		}
	}
?>
		<h6 class="text-center" style="margin-top: 0px;margin-bottom: 15px;"><?=$heading ?></h6>
		<table id="series" class="table table-fixed table-bordered table-condensed text-center">
	      <thead>
	      	<tr>
	      		<th><input type="hidden"></th>
	      		<th><input type="text" name="search_engine" class="search_init" placeholder="team"></th>
	      		<th><input type="text" name="search_engine" class="search_init" placeholder="date"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="count"></th>
	      	</tr>
	        <tr>
	          <th>#</th>
	          <th>Team</th>
	          <th>Last Match Date</th>
	          <th>Count</th>
	        </tr>
	      </thead>
	      <tbody>

<?php
	$i = 1;
	foreach ($teams as $key => $value) {
		foreach ($value as $index => $length) {
			//echo "$key $index : $length<br>";
			$tmp = explode("#", $index);
?>
		<tr>
			<td><?=$i ?></td>
			<td><?=$key ?></td>
			<td><?=$tmp[1] ?></td>
			<td><?=$length ?></td>

		</tr>
<?		$i ++;
		}
	}
	?>
	</tbody>
	</table>
	<script type="text/javascript">
		 $(document).ready(function() {	
	    /* Init DataTables */
		    var oTable = $('#series').dataTable({
		    	    "iDisplayLength": 25,
		    	    "bJQueryUI": true,
		    	    "sPaginationType": "full_numbers",
					"sDom": '<"top" lf><"toolbar">irpti<"bottom"p><"clear">',
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
		} );
	</script>
<?php
	include("includes/footer.php");
?>