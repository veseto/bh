<?php
  include("includes/header.php");
  include("connection.php");
  if(!isset($_SESSION['uid'])) {
    header("Location: index.php");
  }
  if(isset($_GET["league"])) {
    $league = $_GET['league'];
    // $season = str_replace("/", "-", $_GET['season']);
    $q = "SELECT * FROM matches left join leagueDetails on (matches.leagueId = leagueDetails.leagueId) where matches.leagueId='$league' and season<>'2013-2014' ORDER BY matchDate ASC, matchTime ASC";
   // echo $q;
    $res = $mysqli->query($q);
    echo $mysqli->error;
?>

    <table id="fixtures" class="table table-hover table-bordered table-condensed text-center">
      <thead>
        <tr>
          <th><input type="hidden"></th>
          <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="date"></th>
          <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="time"></th>
          <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="league"></th>
          <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="home"></th>
          <th><input type="hidden"></th>
          <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="away"></th>
          <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="res"></th>
          <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="res"></th>
          <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="season"></th>
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
          <th>Season</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $i=1;
          while ($row = $res->fetch_assoc()) {

        ?>
        <tr>
          <td><?php echo $i;?></td>
          <td><?php echo $row['matchDate']; ?></td>
          <td><?php echo substr($row['matchTime'], 0, -3); ?></td>
          <td><img src="img/<?=$row['country'] ?>.png"/> <?php echo $row['displayName']; ?></td>
          <td><?php echo $row['homeTeam']; ?></td>
          <td>-</td>
          <td><?php echo $row['awayTeam']; ?></td>
          <td><?php echo $row['result']; ?></td>
          <td><?php echo $row['resultShort']; ?></td>
          <td><?php echo $row['season']; ?></td>
        </tr>
        <?php
            ++$i;
          }
        }
        ?>
      </tbody>
    </table>
<script type="text/javascript">
    var asInitVals = new Array();

    $(document).ready(function() {
    /* Init DataTables */
    var oTable = $('#fixtures').dataTable({
          "iDisplayLength": 100,
          "bJQueryUI": true,
          "sPaginationType": "full_numbers",
      "sDom": '<"top"lf>irpti<"bottom"p><"clear">'
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