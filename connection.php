<?php
$mysql_hostname = "localhost";
$mysql_user = "bh";
$mysql_password = "abcd1234";
$mysql_database = "fefe_bethelper";
$prefix = "";
$mysqli = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);
if (mysqli_connect_errno($mysqli)) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
date_default_timezone_set('Europe/Sofia');

?>