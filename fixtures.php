<?php
	include("includes/header.php");
	include("connection.php");
	if(!isset($_SESSION['uid'])) {
   header("Location: index.php");
 }
	$settings = unserialize($mysqli->query("SELECT leaguesRequirements from userSettings where userId=".$_SESSION['uid'])->fetch_array()[0]);
	$res = $mysqli->query("SELECT * FROM leagueDetails order by country");
?>
	<!-- <div class="row"> -->
<?php
	$i = 0;
	while ($row = $res->fetch_assoc()) {
		$badge = 0;
		if (array_key_exists($row['leagueId'], $settings)) {
			$badge = $settings[$row['leagueId']] + 1;
		}
?>
	<div class="col-md-4">
    <ul class="list-group">
      <li class="list-group-item">
        <img src="img/<?=$row['country']?>.png" class="pullup" /> <span class="badge"><?=$badge ?></span><a href="<?php echo "fixturesdetails.php?league=".$row['leagueId']; ?>"><?php echo $row['country']." ".$row['name']?></a>
      </li>
    </ul>
  </div>
<?php	
	// $i ++;
	// }
	// if ($i % 4 == 0) {
	// 	echo "</div>";
	}
?>
<?php
	include("includes/footer.php");
?>