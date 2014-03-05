<?php
	include("includes/header.php");
	include("connection.php");
	if(!isset($_SESSION['uid'])) {
	   header("Location: index.php");
	 }
	
	//$league = $_GET['league'];
	
	$res2 = $mysqli->query("SELECT country, name, leagueId from leagueDetails ");
	$leagues = array();
	while ($l = $res2->fetch_array()) {

		//$pageTitle = $l[0]." ".$l[1];
		$res = $mysqli->query("SELECT DISTINCT home from history where leagueId=".$l[2]);

		$lengths = array();
		$lengths = array_fill(1, 20, 0);
		while ($team = $res->fetch_array()) {
			$count = 1;
			$t = $team[0];
			$q = "SELECT * FROM history where (home='$t' or away='$t') and leagueId=".$l[2]." order by mdate";
			$res1 = $mysqli->query($q);
			// echo "$t: ";
			$i = 0;
			while ($match = $res1->fetch_assoc()) {
				//echo $match['short'];
				if ($match['short'] == 'D') {
					//echo $count;
					if ($count > 20) {
						$count = 20;
					}
					$lengths[$count] ++;
					
					$count = 1;
				} else {
					$count ++;
				}
			}
		}
		$leagues[$l[2]." ".$l[0]."/".$l[1]] = array();
		$leagues[$l[2]." ".$l[0]."/".$l[1]] = $lengths;
	}
?>
		<h6 class="text-center" style="margin-top: 0px;margin-bottom: 15px;"></h6>
		<table id="series" class="table table-fixed table-bordered table-condensed text-center">
	      <thead>
	      	
	        <tr>
	          <th>league</th>
	          <?php
	          	for ($i=1; $i < 20; $i++) { 
	          		echo "<th>$i</th>";
	          	}
	          			echo "<td>20 +</td>";

	          ?>
	        </tr>
	      </thead>
	      <tbody>

<?php
	foreach ($leagues as $league => $ls) {
		echo "<tr><td>$league</td>";
		$all = 0;
		for ($i=1; $i < 21; $i++) { 
			echo "<td>".$ls[$i].'</td>';
			$all += $ls[$i];
		}
		echo "</tr>";
		echo "<tr><td>$all</td>";
		for ($i=1; $i < 21; $i++) { 
			if ($all != 0)
			echo "<td>".round((($ls[$i]*100) / $all), 2, PHP_ROUND_HALF_UP).' %</td>';
		}
		echo "</tr>";
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