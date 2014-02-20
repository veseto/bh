<?php
  include("includes/header.php");
  include("connection.php");
if(!isset($_SESSION['uid'])) {
   header("Location: index.php");
 }
 if (isset($_GET['country'])) {
	$seasons=array('2004-2005', '2005-2006', '2006-2007', '2007-2008', '2008-2009', '2009-2010', '2010-2011', '2011-2012', '2012-2013', '2013-2014');
	$res = $mysqli->query("SELECT * from leagueDetails where country='".$_GET['country']."'");
?>
<h4 style="margin: 0px 0px 10px 0px;">Total amount of draw games per team for the last ten seasons</h4>
<table id="draws" class="table table-fixed table-bordered table-condensed text-center">
	      <thead>
	      	<tr>
	      		<th><input type="hidden"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="league"></th>
	      		<th><input type="text" name="search_engine" class="search_init" placeholder="season"></th>
	      		<th><input type="text" name="search_engine" class="search_init" placeholder="team"></th>
	      		<th><input type="text" name="search_engine" class="search_init shortInput" placeholder="draws"></th>
	      	</tr>
	        <tr>
	          <th>#</th>
	          <th>League</th>
	          <th>Season</th>
	          <th>Team</th>
	          <th>Draws</th>
	        </tr>
	      </thead>
	      <tbody>
<?php
	$i=0;
	while ($league = $res->fetch_assoc()) {
		$leagueId = $league['leagueId'];
		foreach ($seasons as $season) {
			$q0="select distinct home from history where leagueId=$leagueId and season='$season'";
			//echo "$q0<br>";
			$res1 = $mysqli->query($q0);
			while ($teamres = $res1->fetch_array()) {
				$i ++;
				$team = $teamres[0];
				$draw = $mysqli->query("SELECT count(*) from history where season='$season' and leagueId=$leagueId and (home='$team' or away='$team') and short='D'")->fetch_array()[0];
				echo "<tr><td>$i</td><td><img src='img/".$league['country'].".png' /> ".$league['displayName']."</td> <td>$season</td> <td>$team</td> <td>$draw</td></tr>";
			}
		}
	}
	?>
	</tbody>
</table>
<script type="text/javascript">
	    $(document).ready(function() {
	    /* Init DataTables */
	    var oTable = $('#draws').dataTable({
	    	    "iDisplayLength": 100,
	    	    "bJQueryUI": true,
	    	    "sPaginationType": "full_numbers",
				"sDom": '<"top" lf>irpti<"bottom"pT><"clear">',
				"oTableTools": {
					"sSwfPath": "dt/media/swf/copy_csv_xls_pdf.swf"
				}
		});

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
	}
  include("includes/footer.php");
?>