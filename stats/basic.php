<?php
	include("../includes/header.php");
	include("../connection.php");
	if (isset($_GET['type'])) {

	} else {
		$profit = $mysqli->query("SELECT sum(profit) 
						from playedMatches 
						left join maatches 
						on playedMatches.matchId=matches.matchId
						where userId=".$_SESSION['uid']." and resultShort='D' and pps=0")->fetch_array()[0];
		echo "ppm profit = $profit";
	}
?>
	
<?php
	include("../includes/footer.php");
?>