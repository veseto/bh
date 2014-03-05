<?php
    session_start();
    ob_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<!--     <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" sizes="16x16 32x32 48x48 64x64" href="favicon/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="favicon/favicon.ico">
    
    <?php
      if ($_SERVER['PHP_SELF'] == '/bh/livescore.php') {
        echo '<meta http-equiv="refresh" content="30" />';
      }
    ?>

    <title>bh</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/custom.css" rel="stylesheet">
    <link href="css/TableTools.css" rel="stylesheet">

    <link href="dt/media/css/jquery.dataTables_themeroller.css" rel="stylesheet">

    <!-- Bootstrap core JS -->
    <script src="dt/media/js/jquery-1.10.2.js"></script>
    <script src="dt/media/js/jquery-ui-1.10.4.custom.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/tether.js"></script>

    <!-- dataTables js -->
    <script type="text/javascript" language="javascript" src="dt/examples/examples_support/jquery.jeditable.js"></script>
    
    <script type="text/javascript" language="javascript" src="dt/media/js/jquery.dataTables.js"></script>
    <script type="text/javascript" language="javascript" src="js/ColVis.js"></script>
    <script type="text/javascript" language="javascript" src="js/TableTools.js"></script>
    <script type="text/javascript" language="javascript" src="js/ZeroClipboard.js"></script>
    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <?php
     $week = array(0=>'SUN', 1=>'MON', 2=>'TUE', 3=>'WED', 4=>'THU', 5=>'FRI', 6=>'SAT');

      include("connection.php");
      $ppm = "ppm";
      $pps = "today";
      $notPlayedPPS = 0;
      $notPlayedPPM = 0;
      $allPPS = 0;
      $allPPM = 0;
      $livescoreAll = 0;
      $livescoreCurr = 0;
        
      if (isset($_SESSION['uid'])) {
        $d = date("Y-m-d", time());
        $t = date("H:i", time());

        $d1 = date("Y-m-d", time() + 86400);
        $t1 = date("H:i", time() + 86400);

        $q0 = "SELECT count(*) 
              from playedMatches 
              left join matches 
              on matches.matchId=playedMatches.matchId 
              where matchDate='$d' and bet>0 and pps=1 and userId=".$_SESSION['uid'];
    
        $notPlayedPPS = $mysqli->query($q0)->fetch_array()[0];
        $notPlayedPPM = $mysqli->query("SELECT count(*) 
              from playedMatches 
              left join matches 
              on matches.matchId=playedMatches.matchId 
              where (matchDate>'$d' or (matchDate='$d' and matchTime>='$t')) and (matchDate<'$d1' or (matchDate='$d1' and matchTime<='$t1')) and bet>0 and pps=0 and userId=".$_SESSION['uid'])->fetch_array()[0];
        $allPPS = $mysqli->query("SELECT count(*) 
              from playedMatches 
              left join matches 
              on matches.matchId=playedMatches.matchId 
              where matchDate='$d' and pps=1 and userId=".$_SESSION['uid'])->fetch_array()[0];
        $allPPM = $mysqli->query("SELECT count(*) 
              from playedMatches 
              left join matches 
              on matches.matchId=playedMatches.matchId 
              where (matchDate>'$d' or (matchDate='$d' and matchTime>='$t')) and (matchDate<'$d1' or (matchDate='$d1' and matchTime<='$t1')) and pps=0 and userId=".$_SESSION['uid'])->fetch_array()[0];
        $ppm = "ppm $notPlayedPPM/$allPPM";
        $pps = "today $notPlayedPPS/$allPPS";

        $startTime = date("H:i:s", time()-60*110);
        $endTime = date("H:i:s", time());


        $livescoreCurr = $mysqli->query("SELECT count(distinct playedMatches.matchId) as s 
              from playedMatches 
              left join matches 
              on matches.matchId = playedMatches.matchId
              where matchDate='$d' and matchTime>='$startTime' and matchTime<='$endTime' and (userId=".$_SESSION['uid']." and (bet>0 or betSoFar>0))")->fetch_array()[0];
        $livescoreAll = $mysqli->query("SELECT count(distinct playedMatches.matchId) as s
              from playedMatches 
              left join matches 
              on matches.matchId = playedMatches.matchId
              where matchDate='$d' and (userId=".$_SESSION['uid']." and (bet>0 or betSoFar>0))")->fetch_array()[0];
        echo $mysqli->error;
      }
    ?>
    <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
         <ul class="nav navbar-nav">
          <li class="padded"><div class="btn-group">
            <button onclick="location.href='dayview.php?day=today'" type="button" class="btn btn-primary btn-sm" id="navBtns">pps <span <?php if ($notPlayedPPS != $allPPS) echo 'class="unplayedGamesNumber"'; ?>><?=$notPlayedPPS ?></span>/<?=$allPPS ?></button>
            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu">
               <li class="dropdown-header">next</li>
               <li><a href="dayview.php?day=2">2 days (<?php echo date("d.m", time()+86400)." ".$week[date("w", time()+86400)];?>)</a></li>
               <li><a href="dayview.php?day=3">3 days (<?php echo date("d.m", time()+2*86400)." ".$week[date("w", time()+2*86400)];?>)</a></li>
               <li><a href="dayview.php?day=4">4 days (<?php echo date("d.m", time()+3*86400)." ".$week[date("w", time()+3*86400)];?>)</a></li>
               <li><a href="dayview.php?day=5">5 days (<?php echo date("d.m", time()+4*86400)." ".$week[date("w", time()+4*86400)];?>)</a></li>
               <li><a href="dayview.php?day=6">6 days (<?php echo date("d.m", time()+5*86400)." ".$week[date("w", time()+5*86400)];?>)</a></li>
               <li><a href="dayview.php?day=7">7 days (<?php echo date("d.m", time()+6*86400)." ".$week[date("w", time()+6*86400)];?>)</a></li>
               <li><a href="dayview.php?day=all">all available</a></li>
               <li class="divider"></li>
               <li class="dropdown-header">previous</li>
               <li><a href="dayview.php?day=-1">-1 days (<?php echo date("d.m", time()-86400)." ".$week[date("w", time()-86400)];?>)</a></li>
               <li><a href="dayview.php?day=-2">-2 days (<?php echo date("d.m", time()-2*86400)." ".$week[date("w", time()-2*86400)];?>)</a></li>
               <li><a href="dayview.php?day=-3">-3 days (<?php echo date("d.m", time()-3*86400)." ".$week[date("w", time()-3*86400)];?>)</a></li>
               <li><a href="dayview.php?day=-5">-5 days (<?php echo date("d.m", time()-4*86400)." ".$week[date("w", time()-4*86400)];?>)</a></li>
               <li><a href="dayview.php?day=-10">-10 days (<?php echo date("d.m", time()-9*86400)." ".$week[date("w", time()-9*86400)];?>)</a></li>
               <li><a href="dayview.php?day=-15">-15 days (<?php echo date("d.m", time()-14*86400)." ".$week[date("w", time()-14*86400)];?>)</a></li>
               <li><a href="dayview.php?day=-30">-30 days (<?php echo date("d.m", time()-29*86400)." ".$week[date("w", time()-29*86400)];?>)</a></li>
            </ul>
          </div></li>
          <li class="padded"><div class="btn-group">
            <button onclick="location.href='ppm.php?day=today'" type="button" class="btn btn-primary btn-sm" id="navBtns">ppm <span <?php if ($notPlayedPPM != $allPPM) echo 'class="unplayedGamesNumber"'; ?>><?=$notPlayedPPM ?></span>/<?=$allPPM ?></button>
            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu">
               <li class="dropdown-header">next</li>
               <li><a href="ppm.php?day=2">2 days (<?php echo date("d.m", time()+86400)." ".$week[date("w", time()+86400)];?>)</a></li>
               <li><a href="ppm.php?day=3">3 days (<?php echo date("d.m", time()+2*86400)." ".$week[date("w", time()+2*86400)];?>)</a></li>
               <li><a href="ppm.php?day=4">4 days (<?php echo date("d.m", time()+3*86400)." ".$week[date("w", time()+3*86400)];?>)</a></li>
               <li><a href="ppm.php?day=5">5 days (<?php echo date("d.m", time()+4*86400)." ".$week[date("w", time()+4*86400)];?>)</a></li>
               <li><a href="ppm.php?day=6">6 days (<?php echo date("d.m", time()+5*86400)." ".$week[date("w", time()+5*86400)];?>)</a></li>
               <li><a href="ppm.php?day=7">7 days (<?php echo date("d.m", time()+6*86400)." ".$week[date("w", time()+6*86400)];?>)</a></li>
               <li><a href="ppm.php?day=all">all available</a></li>
               <li class="divider"></li>
               <li class="dropdown-header">previous</li>
               <li><a href="ppm.php?day=-1">-1 days (<?php echo date("d.m", time()-86400)." ".$week[date("w", time()-86400)];?>)</a></li>
               <li><a href="ppm.php?day=-2">-2 days (<?php echo date("d.m", time()-2*86400)." ".$week[date("w", time()-2*86400)];?>)</a></li>
               <li><a href="ppm.php?day=-3">-3 days (<?php echo date("d.m", time()-3*86400)." ".$week[date("w", time()-3*86400)];?>)</a></li>
               <li><a href="ppm.php?day=-5">-5 days (<?php echo date("d.m", time()-4*86400)." ".$week[date("w", time()-4*86400)];?>)</a></li>
               <li><a href="ppm.php?day=-10">-10 days (<?php echo date("d.m", time()-9*86400)." ".$week[date("w", time()-9*86400)];?>)</a></li>
               <li><a href="ppm.php?day=-15">-15 days (<?php echo date("d.m", time()-14*86400)." ".$week[date("w", time()-14*86400)];?>)</a></li>
               <li><a href="ppm.php?day=-30">-30 days (<?php echo date("d.m", time()-29*86400)." ".$week[date("w", time()-29*86400)];?>)</a></li>
            </ul>
          </div></li>
          <li class="padded"><div class="btn-group">
            <button onclick="location.href='livescore.php?played=true'" type="button" class="btn btn-primary btn-sm" id="navBtns"><span  style="font-size: 90%;">livescore <?=$livescoreCurr ?>/<?=$livescoreAll?></span></button>
            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu">
               <li><a href="livescore.php">leagues</a></li>
               <li><a href="livescore.php?all=true">all</a></li>
            </ul>
          </div></li>
          <li class="padded"><div class="btn-group">
            <button onclick="location.href='standings.php'" type="button" class="btn btn-primary btn-sm" id="navBtns">standings</button>
            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu">
               <li><a href="fixtures.php">fixtures</a></li>
               <li><a href="history.php">history</a></li>
               <li class="divider"></li>
               <li class="dropdown-header">series</li>
               <li><a href="series.php">active</a></li>
               <li><a href="series.php?inactive=true">inactive</a></li>
            </ul>
          </div></li>
        <?php
          if (isset($_SESSION['uid'])) {
        ?>
          <li class="padded"><div class="btn-group">
            <button onclick="location.href='stats.php?tab=comp'" type="button" class="btn btn-primary btn-sm" id="navBtns">money</button>
            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu">
        <li class="dropdown-header"><strong><?=$_SESSION['name']?></strong></li>
        <li class="divider"></li>
        <li><a href="notes.php">notes</a></li>
        <li><a href="helpers.php">helpers</a></li>
        <li><a href="dayview.php">queue</a></li>
        <?php 
          if (isset($_SESSION['uid']) && $_SESSION['uid'] === '1' || $_SESSION['uid'] === 1 || $_SESSION['uid'] === '3' || $_SESSION['uid'] === 3) {
            echo '<li class="divider"></li>';
            echo '<li><a href="updstats.php">infrastructure</a></li>';
            // echo '<li><a href="update_files">updates</a></li>';
          }
        ?>
        <li class="divider"></li>
        <li><a href='logout.php'>logout</a></li>
            </ul>
          </div></li>
          <?php
          } else {
            echo '<li><a href="index.php">login</a></li>';

          }
          ?>
        </ul>
        <li><p class="navbar-text pull-right" style="margin: 0px; padding-top: 4px;">test</p></li>
      </div>
    </div>
    
    <div id="wrap">
    <div class="container">