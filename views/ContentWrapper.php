<?php
//PARAMETERS
//content : site content
?>
<script>
	$(document).ready(function(){
	    $("#menu-toggle").click(function(e) {
	        e.preventDefault();
	        $("#wrapper").toggleClass("toggled");
	    });
		
	});
</script>
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
      <a class="navbar-brand" href="#"><img style="margin: -10px;" src="public/images/logo.png" height="40"/></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-left">
       <li><a href="#toggle-menue" style="font-size:20px;" id="menu-toggle"><span class="glyphicon glyphicon-list"> </span></a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li style="margin-right:50px;"></li>
        <form class="navbar-form navbar-left" role="search">
          <div class="form-group">
            <input type="text" class="form-control" placeholder="Suche...">
          </div>
          <div class="btn-group">
            <button type="button" class="btn btn-default">Suche</button>
            <button type="button" class="btn btn-default">Erweitert...</button>
          </div>
        </form>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Mehr<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">About</a></li>
            <li class="divider"></li>
            <li><a href="#">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<div id="wrapper">
	<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
      <li class="sidebar-brand">
        Mein Projectie </li>
      <li>
        <a href="#">Profil</a>
      </li>
      <li>
        <a href="#">Eigene Projekte</a>
      </li>
      <li>
        <a href="#">Favoriten</a>
      </li>
      <li>
        <a href="#">Konversationen</a>
      </li>
    </ul>
  </div>
  <div id="page-content-wrapper"> 
  	<?php echo $_DATA["content"]; ?>   
  </div>
</div>