<?php
	$a = unserialize("a:16:{i:1;i:3;i:2;i:5;i:3;i:5;i:4;i:6;i:5;i:9;i:6;i:9;i:7;i:9;i:8;i:9;i:9;i:3;i:10;i:3;i:11;i:3;i:12;i:3;i:13;i:3;i:14;i:3;i:15;i:3;i:16;i:3;}");
	array_push($a, 0);
	array_push($a, 3);
	array_push($a, 3);
	array_push($a, 0);
	array_push($a, 0);
	array_push($a, 0);
	array_push($a, 0);
	array_push($a, 0);
	array_push($a, 0);
	echo serialize($a);

?>