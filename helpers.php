<?php
  include("includes/header.php");
  include("connection.php");
if(!isset($_SESSION['uid'])) {
   header("Location: index.php");
 }
?>
<div class="row">
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12">
          <div class="list-group">
            <a href="drawhelper.php" class="list-group-item">draw games last 5 seasons</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 pull-right">
      <div class="row">
        <div class="col-md-12">
          <div class="list-group">
            <a href="serieslast10seasons.php?length=13&league=1" class="list-group-item">series last 5 seasons</a>
          </div>
        </div>
      </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12">
          <div class="list-group">
            <a href="lengthhelper.php?league=1" class="list-group-item">series length last 5 seasons</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 pull-right">
      <div class="row">
        <div class="col-md-12">
          <div class="list-group">
            
          </div>
        </div>
      </div>
    </div>
</div>
<?php
  include("includes/footer.php");
?>