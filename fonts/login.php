<?php
	include("includes/header.php");
  	include("connection.php");
  	// $_SESSION = array();
  	if (isset($_POST['username']) && isset($_POST['password'])) {
  		$username = $_POST['username'];
  		$password = $_POST['password'];
  		$q = "SELECT * FROM users where username='$username' and password='$password'";
  		$res = $mysqli->query($q);
  		if ($res) {
  			$user = $res->fetch_assoc();
  			$_SESSION['uid'] = $user['userId'];
  			$_SESSION['name'] = $user['name'];
  			$_SESSION['username'] = $user['username'];
  			$_SESSION['email'] = $user['email'];
  			header("Location: dayView.php?day=0");
  		} else {
  			$error = "Incorrect login data";
  		}
  	}
?>
    <div class="container" style="padding-top: 25px;">
		<div class="row">
			<div class="col-xs-4 col-xs-offset-4">
				<div class="span6 offset3">
					<form method="post" action="">
					<input name="username" id="username" type="text" class="form-control" placeholder="username" autofocus required>
					<div class="input-group">
					  <input name="password" id="password" type="password" class="form-control" placeholder="password" required>
            <span class="input-group-addon">
              <input type="checkbox" class="btn-warning">
            </span>
            <span class="input-group-btn">
					    <button type="submit" class="btn btn-large btn-danger">Login</button>
					  </span>
					</div><!-- /input-group -->
					</form>
				</div><!-- /.col-xs-6 -->
			</div>
		</div>
    </div> <!-- /container -->
  </body>
</html>