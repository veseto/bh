<?php
  include("includes/header.php");
  include("connection.php");
if(!isset($_SESSION['uid'])) {
   header("Location: index.php");
 }
  date_default_timezone_set('Europe/Sofia');
  $str = "";
  $getStr = "";
  if (isset($_GET['day'])) {
    $getStr = "day=".$_GET['day']."&";
    if ($_GET['day'] === '7' || $_GET['day'] === 7) {
      $d = date("Y-m-d", time()+(86400*7));
      $d1= date("Y-m-d", time());
      $str = " matchDate<'$d' and matchDate>='$d1' and ";
    } else {
      $d = date("Y-m-d", time()+(86400*$_GET['day']));
      $str = " matchDate='$d' and ";
    }
  }
  $q = "SELECT * FROM `playedMatches` left join (matches, series, leagueDetails) on (playedMatches.matchId=matches.matchId and playedMatches.seriesId=series.seriesId and series.leagueId=leagueDetails.leagueId) where ".$str." userId=".$_SESSION['uid']." and playedMatches.ignored=0 order by matches.leagueId ASC, playedMatches.seriesId ASC, matches.matchDate ASC";
  if (isset($_GET['country'])) {
    $q = "SELECT * FROM `playedMatches` left join (matches, series, leagueDetails) on (playedMatches.matchId=matches.matchId and playedMatches.seriesId=series.seriesId and series.leagueId=leagueDetails.leagueId) where ".$str." userId=".$_SESSION['uid']." and playedMatches.ignored=0 and country='".$_GET['country']."' order by matches.leagueId ASC, playedMatches.seriesId ASC, matches.matchDate ASC";
    if (isset($_GET['league'])) {
      $q = "SELECT * FROM `playedMatches` left join (matches, series, leagueDetails) on (playedMatches.matchId=matches.matchId and playedMatches.seriesId=series.seriesId and series.leagueId=leagueDetails.leagueId) where ".$str." userId=".$_SESSION['uid']." and playedMatches.ignored=0 and country='".$_GET['country']."' and name='".$_GET['league']."' order by matches.leagueId ASC, playedMatches.seriesId ASC, matches.matchDate ASC";  
      if (isset($_GET['team'])) {
        $q = "SELECT * FROM `playedMatches` left join (matches, series, leagueDetails) on (playedMatches.matchId=matches.matchId and playedMatches.seriesId=series.seriesId and series.leagueId=leagueDetails.leagueId) where ".$str." userId=".$_SESSION['uid']." and playedMatches.ignored=0 and country='".$_GET['country']."' and name='".$_GET['league']."' and series.team='".$_GET['team']."' order by matches.leagueId ASC, playedMatches.seriesId ASC, matches.matchDate ASC";  
      }
    }
  }
  $res = $mysqli->query($q);
  $res2 = $mysqli->query("SELECT DISTINCT country FROM leagueDetails");
?>

	<div class="container" style="padding: 0px; margin: 0px 0px 5px 0px;">

	  <!-- filtering -->
	  <span> Filter: </span>
	  <!-- country dropdown -->
	  <div class="btn-group">
	    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" id="country">
	      <?php 
	        if (isset($_GET["country"])) {
	          $res3 = $mysqli->query("SELECT name from leagueDetails where country='".$_GET['country']."'");
	          echo $mysqli->error;
	          echo $_GET["country"]; 
	        } else {
	          echo "Country";
	        }
	      ?> <span class="caret"></span>
	    </button>
	    <ul class="dropdown-menu filters" role="menu">
	      <?php 
	        while ($c = $res2->fetch_array()) {
	          echo "<li><a href='dayview.php?".$getStr."country=".$c[0]."'>".$c[0]."</a></li>";
	        }
	      ?>
	    </ul>
	  </div> <!-- // country dropdown -->

	  <!-- league dropdown -->
	  <div class="btn-group" id="countryFilter">
	    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" id="league" <?php if (!isset($_GET['country'])) echo "disabled";?>>
	        <?php
	        if (isset($_GET['league']) && isset($_GET['country'])){
	          $res4 = $mysqli->query("SELECT DISTINCT homeTeam FROM matches left join leagueDetails on leagueDetails.leagueId = matches.leagueId where name='".$_GET['league']."' and country='".$_GET['country']."' order by homeTeam asc");
	          echo $_GET['league'];
	        } else {
	          echo "League";
	        }
	        ?>
	       <span class="caret"></span>
	    </button>
	    <ul class="dropdown-menu filters" role="menu">
	      <?php
	        if (isset($_GET['country'])) {
	          while ($l = $res3->fetch_array()) {
	            echo "<li><a href='dayview.php?".$getStr."country=".$_GET['country']."&league=".$l[0]."'>".$l[0]."</a></li>";
	          }
	        }
	      ?>
	    </ul>
	  </div> <!-- // league dropdown -->

	  <!-- team dropdown -->
	  <div class="btn-group">
	    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" id="team" <?php if (!isset($_GET['league']) || !isset($_GET['country'])) echo "disabled";?>>
	      <?php
	        if (isset($_GET['team'])){
	          echo $_GET['team'];
	        } else {
	          echo "Team";
	        }
	        ?>
	       <span class="caret"></span>
	    </button>
	    <ul class="dropdown-menu filters" role="menu">
	      <?php
	        if (isset($_GET['country']) && isset($_GET['league'])) {
	          while ($t = $res4->fetch_array()) {
	            echo "<li><a href='dayview.php?".$getStr."country=".$_GET['country']."&league=".$_GET['league']."&team=".$t[0]."'>".$t[0]."</a></li>";
	          }
	        }
	      ?>
	    </ul>
	  </div> <!-- // team dropdown -->


	  <!-- sorting -->
	  <span> Sort: </span>
	  <!-- first sort dropdown -->
	  <div class="btn-group">
	    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" id="country">
	      First by <span class="caret"></span>
	    </button>
	    <ul class="dropdown-menu filters" role="menu">
	      <li><a href='#'>DATE AND TIME</a></li>
	      <li><a href='#'>SERIES LENGTH</a></li>
	      <li><a href='#'>LEAGUE</a></li>
	      <li><a href='#'>HOME TEAM NAME</a></li>
	  	</ul>
	  </div> <!-- // first sort dropdown -->

	  <!-- second sort dropdown -->
	  <div class="btn-group">
	    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" id="country">
	      then <span class="caret"></span>
	    </button>
	    <ul class="dropdown-menu filters" role="menu">
	      <li class="disabled"><a href='#'>DATE AND TIME</a></li>
	      <li><a href='#'>SERIES LENGTH</a></li>
	      <li><a href='#'>LEAGUE</a></li>
	      <li><a href='#'>HOME TEAM NAME</a></li>
	  	</ul>
	  </div> <!-- // second sort dropdown -->

	  <!-- third sort dropdown -->
	  <div class="btn-group">
	    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" id="country">
	      then <span class="caret"></span>
	    </button>
	    <ul class="dropdown-menu filters" role="menu">
	      <li class="disabled"><a href='#'>DATE AND TIME</a></li>
	      <li><a href='#'>SERIES LENGTH</a></li>
	      <li class="disabled"><a href='#'>LEAGUE</a></li>
	      <li><a href='#'>HOME TEAM NAME</a></li>
	  	</ul>
	  </div> <!-- // third sort dropdown -->

	  <!-- fixtures -->
	  <span> Show: </span>
	  <!-- period filter dropdown -->
	  <div class="btn-group">
	    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" id="country">
	      --- <span class="caret"></span>
	    </button>
	    <ul class="dropdown-menu filters" role="menu">
	      <li><a href='#'>1</a></li>
	      <li><a href='#'>2</a></li>
	      <li><a href='#'>3</a></li>
	      <li><a href='#'>4</a></li>
	      <li><a href='#'>5</a></li>
	      <li><a href='#'>6</a></li>
	      <li><a href='#'>7</a></li>
	      <li><a href='#'>ALL</a></li>
	  	</ul>
	  </div> <!-- // period filter dropdown -->
	  <span> days. </span>
	  <a href="#" class="btn btn-default btn-xs active" role="button">RESET</a>
	</div>
	    <table class="table table-fixed table-bordered table-condensed text-center" style="margin-top: 5px;">
	      <thead>
	        <tr>
	          <th style="width: 4%">#</th>
	          <th style="width: 8%">Date</th>
	          <th style="width: 4%">Time</th>
	          <th style="width: 5%">League</th>
	          <th style="width: 15%">Home Team</th>
	          <th style="width: 2%">-</th>
	          <th style="width: 15%">Away Team</th>
	          <th style="width: 6%">Bet so far</th>
	          <th style="width: 6%">Bet</th>
	          <th style="width: 5%">Odds</th>
	          <th style="width: 6%">Income</th>
	          <th style="width: 5%">Profit</th>
	          <th style="width: 5%">Result</th>
	          <th style="width: 2%">Result</th>
	          <th style="width: 6%"></th>
	          <th style="width: 5%"></th>
	        </tr>
	      </thead>
	      <tbody>
	        <?php 
	        $i = 1;
	        while ($row = $res->fetch_assoc()) {

	        ?>
	        <tr>
	          <form method="post" id="rowForm" action="save.php">
	          <td><?php echo $i; ?></td>
	          <td><?php echo $row['matchDate']; ?></td>
	          <td><?php echo substr($row['matchTime'], 0, -3); ?></td>
	          <td><img src="img/<?=$row['country'] ?>.png"/> <?php echo $row['displayName']; ?></td>
	          <td><?php 
	              if ($row['team'] === $row['homeTeam']) {
	                echo "<strong>".$row['homeTeam']." (".$row['currentLength'].")</strong>";
	              } else {
	                echo $row['homeTeam'];
	              } 
	          ?></td>
	          <td>-</td>
	          <td><?php 
	            if ($row['team'] === $row['awayTeam']) {
	                echo "<strong>".$row['awayTeam']." (".$row['currentLength'].")</strong>";
	              } else {
	                echo $row['awayTeam'];
	              }
	          ?></td>
	          <td><?php
	            $betSoFar = $mysqli->query("SELECT SUM(bet) FROM playedMatches left join matches on matches.matchId=playedMatches.matchId where userId=".$_SESSION['uid']." and seriesId=".$row['seriesId']." and matchDate<'".$row['matchDate']."'")->fetch_array()[0];
	            if($betSoFar) {
	              echo $betSoFar;
	            } else {
	              echo "0";
	            }
	          ?></td>
	          <td><input class="editable <?php echo $i; ?>" type="number" step="any" readonly id="bet" name="bet" value="<?php echo $row['bet']; ?>"></td>
	          <td><input class="editable <?php echo $i; ?>" type="number" step="0.05" readonly id="odds" name="odds" value="<?php echo $row['odds']; ?>"></td>
	          <td><span class="income<?php echo $i;?>"><?php echo $row['bet'] * $row['odds'] - $row['bet']; ?></span></td>
	          <td><?php echo $row['bet'] * $row['odds'] - $row['bet'] - $betSoFar; ?></td>
	          <td><?php echo $row['result']; ?></td>
	          <td><?php echo $row['resultShort']; ?></td>
	          <td><input type="submit"  id="save<?php echo $i?>" name="<?php echo $i?>" style="width: 35px;" value="save"></td>
	          <td><input type="button" name="ignore" value="ignore" class="ignore" style="width: 40px;"></td>
	          <input type="hidden" name="seriesId" value="<?php echo $row['seriesId'];?>">
	          <input type="hidden" name="matchId" value="<?php echo $row['matchId'];?>">
	          <input type="hidden" name="ignored" id="ignored" disabled value="1">
	          <input type="hidden" name="url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
	          </form>
	        </tr>
	        <?php
	        $i ++;
	        }
	        ?>
	      </tbody>
	    </table>
	    <script type="text/javascript">
	 //    $('form').submit(function(event) {

		//     /* Stop form from submitting normally */
		//     event.preventDefault();

		//     /* Clear result div*/

		//     /* Get some values from elements on the page: */
		//     var values = $(this).serialize();

		//     /* Send the data using post and put the results in a div */
		//     $.ajax({
		//         url: "save.php",
		//         type: "post",
		//         data: values,
		//         success: function(response){
		//         	var arr = response.split('#');
		//         	var class1 = ".income" + arr[0];
		//         	$(class1).text(response);
		//         },
		//         error:function(){
		//             alert("failure");
		//             $("#income").html('There is error while submit');
		//         }
		//     });
		// });

// });
	      $(".editable").on("click", function(){
	      	$(this).attr("readonly", false);
	      	$(this).parent().addClass("danger");
	        $(this).parent().parent().addClass("success");
	      });

	      $('.editable').bind('keypress', function(e) {
	      	var code = e.keyCode || e.which;
			 if(code == 13) { //Enter keycode
			   // /alert("Enter!");
			   	$(this).parent().removeClass("danger");
	        	$(this).parent().parent().removeClass("success");
	        	$('.editable').attr("readonly", true);
			 }
	      });

	      $('.editable').focusout(function(){
	      	$('form').submit();
	      });
// 	      $(".editRow").on("click", function(){
// 	        name = $(this).attr("name");
// 	        var claz = "." + name;
// 	        $(claz).attr("readonly", false);
// 	      	$(claz).parent().addClass("danger");
// 	        $(claz).parent().parent().addClass("success");
// 	        $(this).css("display", "none");
	        
// 	        var save = "#save" + name;
// 	        $(save).attr("style", "");
// 	        var cancel = "#cancel" + name;
// 	        $(cancel).attr("style", "");
// 	    	});
	       
// 	       $(".submit1").on("click", function(){
// 	        // $("#rowForm").submit();
// 	        name = $(this).attr("name");
// 	        var claz = "." + name;
// 	        $(claz).attr("readonly", true);
// 	       	$(claz).parent().parent().removeClass("success");
// 	        $(claz).parent().removeClass("danger");
// 	        $(this).attr("style", "");
	        
// 	        var save = "#save" + name;
// 	        $(save).css("display", "none");
// 	        var cancel = "#cancel" + name;
// 	        $(cancel).css("display", "none");
// 	      }); 
	      
// 	      $(".submit2").on("click", function(){
// 	        // $("#rowForm").attr("action", "");
// 	        // $("#rowForm").submit();
// 	        name = $(this).attr("name");
// 	        var claz = "." + name;
// 	        $(claz).attr("readonly", true);
// 	        $(claz).parent().parent().removeClass("success");
// 	        $(claz).parent().removeClass("danger");
// 	        $(this).attr("style", "");
	        
// 	        var save = "#save" + name;
// 	        $(save).css("display", "none");
// 	        var cancel = "#cancel" + name;
// 	        $(cancel).css("display", "none");
// 	      }); 

// 	      $(".ignore").on("click", function(){
// 	        alert("boo");
// 	        $("#rowForm").submit();
// 	      });

	    </script>

<?php
  include("includes/footer.php");
?>