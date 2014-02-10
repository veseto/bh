<?php
  include("includes/header.php");
  include("connection.php");
if(!isset($_SESSION['uid'])) {
   header("Location: index.php");
 }
  $res = $mysqli->query("SELECT DISTINCT country
                  FROM leagueDetails");
  while ($c0 = $res->fetch_array()[0]) {
    $c1 = $res->fetch_array()[0];
?>
<div class="row">
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12">
          <div class="list-group">
            <a href="drawslast10seasons.php?country=<?=$c0?>" class="list-group-item"><img src="img/<?=$c0?>.png" class="pullup" /> <?=$c0?> - Number of draws for all teams (last 10 seasons)</a>
          </div>
        </div>
      </div>
    </div>
    <?php if ($c1) {?>
    <div class="col-md-6 pull-right">
      <div class="row">
        <div class="col-md-12">
          <div class="list-group">
            <a href="drawslast10seasons.php?country=<?=$c1?>" class="list-group-item"><img src="img/<?=$c1?>.png" class="pullup" /> <?=$c1?> - Number of draws for all teams (last 10 seasons)</a>
          </div>
        </div>
      </div>
    </div>
    <?php }?>
</div>
<?php
  }
  include("includes/footer.php");
?>