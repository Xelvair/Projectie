<?php
//PARAMETERS
//content : site content
global $locale;
?>
<script>
	$(document).ready(function(){
	    $("#menu-toggle").click(function(e) {
	        e.preventDefault();
	        $("#wrapper").toggleClass("toggled");
	    });
		
	});
</script>


<?=$_DATA['login_modal']?>


<nav class="navbar navbar-default  navbar-fixed-top" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"><img style="margin: -10px;" src="<?=abspath("/public/images/logo.png")?>" height="40"/></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-left">
       <li><a href="#toggle-menue" style="font-size:20px; height:50px;" id="menu-toggle"><span class="glyphicon glyphicon-list"></span></a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li style="margin-right:50px;"></li>
        <form class="navbar-form navbar-left" role="search">
          <div class="form-group">
            <input type="text" class="form-control" placeholder="Suche...">
          </div>
          <div class="btn-group">
            <button type="button" class="btn btn-default"><?=$locale["search"];?></button>
            <button type="button" class="btn btn-default"><?=$locale["advanced"] ?></button>
          </div>
        </form>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$locale["more"]?><span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#"><?=$locale["about"]?></a></li>
            <li class="divider"></li>
            <li><a href="#"><?=$locale["logout"]?></a></li>
            <li><a href="#loginModal" role="button" class="btn" data-toggle="modal">Login</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<div id="wrapper">
	<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
      <li class="sidebar-brand"><?php echo($_DATA["user"] != null ? $_DATA["user"] : $locale["welcome"]); ?></li>
      <li>
        <a href="#"><?=$locale["profile"]?></a>
      </li>
      <li>
        <a href="#"><?=$locale["my_projects"]?></a>
      </li>
      <li>
        <a href="#"><?=$locale["favorites"]?></a>
      </li>
      <li>
        <a href="#"><?=$locale["conversations"]?></a>
      </li>
    </ul>
  </div>
  <div id="page-content-wrapper"> 
  	<?php echo $_DATA["content"]; ?>   
  </div>
</div>