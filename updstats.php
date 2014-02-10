<?php
	include("includes/header.php");
	include("connection.php");

	$res = $mysqli->query("SELECT * from stats left join leagueDetails on leagueDetails.leagueId=stats.leagueId order by stats.updateTime desc limit 500");
?>
<table id="statsTable" class="table table-fixed table-bordered table-condensed text-center">
	      <thead>
	      	<tr>
	          <th><input type="hidden"></th>
			  <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="country"></th>
			  <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="league"></th>
			  <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="time/date"></th>
			  <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="lasted"></th>
			  <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="updated"></th>
			  <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="added"></th>
	        </tr>
	      	<tr>
	          <th>#</th>
	          <th>Country</th>
	          <th>League</th>
	          <th>Update time</th>
	          <th>Lasted</th>
	          <th>Updated matches</th>
	          <th>Added for bet</th>
	        </tr>
	      </thead>
	      <tbody>
	        <?php 
	        $i = 1;
	        while ($row = $res->fetch_assoc()) {
	        ?>
	        <tr>
			  <td><?php echo $i; ?></td>
	          <td><?=$row['country'] ?></td>
	          <td><img src="img/<?=$row['country'] ?>.png"/> <?=$row['displayName'] ?></td>
	          <td><?=$row['updateTime'] ?></td>
	          <td><?=$row['lastSec'] ?></td>
	          <td><?=$row['updated'] ?></td>
	          <td><?=$row['addedToPlay'] ?></td>
	        </tr>
	        <?php
	        $i ++;
	        }
	        ?>
	      </tbody>
	    </table>
	    <script type="text/javascript">
    var asInitVals = new Array();

    $(document).ready(function() {
    /* Init DataTables */
    var oTable = $('#statsTable').dataTable({
    	    "iDisplayLength": 100,
    	    "bJQueryUI": true,
    	    "sPaginationType": "full_numbers",
			"sDom": '<"top" lf>Tirpti<"bottom"p><"clear">',
			"oTableTools": {
				"sSwfPath": "dt/media/swf/copy_csv_xls_pdf.swf"
			}
    });
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