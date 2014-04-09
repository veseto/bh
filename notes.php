<?php
	session_start();
	include("connection.php");
	//print_r($_POST);
	if (isset($_POST['note'])) {
		if ($mysqli->query("update userSettings set notes='".$_POST['note']."' where userId=".$_SESSION['uid'])) {
			//echo("<script>alert('Note successfully saved')</script>");
			echo "<div id='msg'>Saved</div>";
		}
	}
	$content = $mysqli->query("SELECT notes from userSettings where userId=".$_SESSION['uid'])->fetch_array()[0];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<!--     <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="description" content="">
    <meta name="author" content="">
    <script src="dt/media/js/jquery-1.10.2.js"></script>
    <link rel="shortcut icon" sizes="16x16 32x32 48x48 64x64" href="favicon/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="favicon/favicon.ico">
    <title>bh</title>
	<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
  	<style type="text/css">
		 #outer {
		    width:100%;

		    /* Firefox */
		    display:-moz-box;
		    -moz-box-pack:center;
		    -moz-box-align:center;

		    /* Safari and Chrome */
		    display:-webkit-box;
		    -webkit-box-pack:center;
		    -webkit-box-align:center;

		    /* W3C */
		    display:box;
		    box-pack:center;
		    box-align:center;
		}
		#inner {
		    width:80%;
		    text-align: center;
		}
		#btnback {
			float: left;
			padding-left: 3px;
		}
 	</style>
  </head>
<body>
	<div id="outer">
		<div id="inner">
			<div id="btnback"><button onclick="location.href='/bh/'">back</button></div>
			<h4>Notes</h4>
		<script>

		        tinymce.init({
				    selector:   "textarea",
				    width:      '100%',
				    height:     500,
				    plugins:    "table insertdatetime searchreplace code",
		//		    statusbar:  true,
		//			menubar:    "tools table format view insert edit",
		//		    toolbar:    "insertdatetime",
					tools: 		"inserttable",
					insertdatetime_formats: ["%Y.%m.%d", "%H:%M"] 
		        });
		$(document).ready(function(){
			setTimeout(function() {
			    $('#msg').fadeOut('fast');
			}, 1000); 
		});
		</script>
		<form method='post' action='notes.php'>
		<textarea name="note"><?=$content ?></textarea>
		    <input type="submit" name="save" value="Submit" />
		</form>
		</div>
	</div>

</body>
</html>