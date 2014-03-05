<?php
  include("includes/header.php");
  include("connection.php");
if(!isset($_SESSION['uid'])) {
   header("Location: index.php");
 }
?>

<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs">
          <li <?php echo $_GET['tab'] == 'comp'? "class=active" : "";?>><a href="stats.php?tab=comp">Completed Series</a></li>
          <li <?php echo $_GET['tab'] == 'dep'? "class=active" : "";?>><a href="stats.php?tab=dep">Deposits (€)</a></li>
          <li <?php echo $_GET['tab'] == 'wit'? "class=active" : "";?>><a href="stats.php?tab=wit">Withdrawals (€)</a></li>
          <li <?php echo $_GET['tab'] == 'sts'? "class=active" : "";?>><a href="stats.php?tab=sts">Statistics</a></li>
        </ul>
      </div>
    </div>       

          <?php
            if ($_GET['tab'] == 'dep' || $_GET['tab'] == 'wit') {
              include('deposits.php');
            } else if ($_GET['tab'] == 'comp') {
              include('completed.php');
            } else if ($_GET['tab'] == 'sts') {
              include('basicstats.php');
            }
          ?>
          
<?php
  include("includes/footer.php");
?>