<?php
	include("includes/header.php");
	include("connection.php");
	if(!isset($_SESSION['uid'])) {
	   header("Location: index.php");
	 }
	if (isset($_GET['league'])) {
		$league = $_GET['league'];
		$res = $mysqli->query("SELECT * FROM tables where leagueId=$league");
		while ($row = $res->fetch_assoc()) {
			echo $row['place'].". ".$row['team']."<br>";
		}
?>
	

<?php
	}
	include("includes/footer.php");
?>