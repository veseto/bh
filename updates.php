<?php
	include("includes/header.php");
	if(!isset($_SESSION['uid'])) {
	   header("Location: index.php");
	 }	
	if ($_SESSION['uid'] === '1' || $_SESSION['uid'] === 1 || $_SESSION['uid'] === '3' || $_SESSION['uid'] === 3) {
?>
	<a href="/bh/data_manage/updateEngland.php">update ENGLAND</a><br>
	<a href="/bh/data_manage/updateFrance.php">update FRANCE</a><br>
	<a href="/bh/data_manage/updateGermany.php">update GERMANY</a><br>
	<a href="/bh/data_manage/updateItaly.php">update ITALY</a><br>
	<a href="/bh/data_manage/updateScotland.php">update SCOTLAND</a><br>
	<a href="/bh/data_manage/updateTurkey.php">update TURKEY</a><br>
	<a href="/bh/data_manage/updateAustralia.php">update AUSTRALIA</a><br>
	<a href="/bh/data_manage/updatePoland.php">update POLAND</a><br>
	<a href="/bh/data_manage/updateSpain.php">update SPAIN</a><br><br>

	<a href="/bh/data_manage/updateStats.txt">stats</a><br>


<?php
		} else {
			header("Location: index.php");
		}
?>

<?php
	include("includes/footer.php");
?>