<?php
	// $a = unserialize("a:16:{i:1;i:3;i:2;i:5;i:3;i:5;i:4;i:6;i:5;i:9;i:6;i:9;i:7;i:9;i:8;i:9;i:9;i:3;i:10;i:3;i:11;i:3;i:12;i:3;i:13;i:3;i:14;i:3;i:15;i:3;i:16;i:3;}");

	// print_r (unserialize("a:32:{i:27;i:3;i:26;i:3;i:65;i:2;i:60;i:3;i:30;i:3;i:31;i:3;i:28;i:3;i:57;i:3;i:67;i:3;i:1;i:3;i:2;i:5;i:3;i:5;i:4;i:6;i:5;i:9;i:6;i:9;i:7;i:9;i:8;i:9;i:9;i:3;i:10;i:3;i:11;i:3;i:12;i:3;i:13;i:3;i:14;i:3;i:15;i:3;i:16;i:3;i:17;i:0;i:18;i:3;i:19;i:3;i:20;i:5;i:34;i:3;i:55;i:3;i:35;i:3;}"));
	include("../connection.php");


	$res = $mysqli->multi_query("SELECT * FROM userSettings where userId=1; SELECT * FROM users where userId=1");
	do {
        /* store first result set */
        if ($result = $mysqli->store_result()) {
            while ($row = $result->fetch_row()) {
                print_r($row);
            }
            $result->free();
        }
        /* print divider */
        if ($mysqli->more_results()) {
            printf("-----------------\n");
        }
    } while ($mysqli->next_result());
?>