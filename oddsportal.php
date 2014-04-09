<?php
	include("includes/header.php");
  	include("connection.php");
  	// $_SESSION = array();
?>
    <div class="container" style="padding-top: 25px;">
    <?php 
      if(isset($_SESSION['error'])){
        echo $_SESSION['error'];
        unset($_SESSION['error']);
      } 
    ?>

    <div class="container">
      <script type="text/javascript" src="http://www.oddsportal.com/ajax-widget/ca21409c1d09f86/"></script>
    </div>
 </body>
</html>