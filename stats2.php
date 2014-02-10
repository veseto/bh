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
              <li <?php echo $_GET['tab'] == 'dep'? "class=active" : "";?>><a href="stats2.php?tab=dep">Deposits</a></li>
              <li <?php echo $_GET['tab'] == 'wit'? "class=active" : "";?>><a href="stats2.php?tab=wit">Withdrawals</a></li>
              <li <?php echo $_GET['tab'] == 'comp'? "class=active" : "";?>><a href="stats2.php?tab=comp">Completed Series</a></li>
            </ul>

        <div class="row">
          <div class="col-md-12">
          <?php
            if ($_GET['tab'] == 'dep' || $_GET['tab'] == 'wit') {
              include('deposits.php');
            } else if ($_GET['tab'] == 'comp') {
              include('completed.php');
            }
          ?>
          </div> <!-- // cashouts -->
        </div>
        
<?php
  include("includes/footer.php");
?>