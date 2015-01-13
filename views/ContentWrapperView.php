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
		
		$('#logout_btn').on('click', function(){
		  $.post("<?=abspath("/auth/logout")?>").done(function(){
			 window.location.href="<?=abspath("")?>";
			});
		});
		
		
	});
</script>


<?=$_DATA['login_modal']?>

<div id="AdvancedSearchModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span></button>
              <h2 class="text-center"><?=$locale["advanced_search"]?></h2>
          </div>
          <div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<form>
						<div class="form-group">
							<input type="text" class="form-control" placeholder="<?=$locale['placeholder_search']?>"/>
						</div>
						<div class="row text-center">
							<div class="col-xs-12">
								<h3><?=$locale['search_for']?>...</h3>
								<div class="form-group">
									<div class="btn-group" data-toggle="buttons">
									  <label class="btn btn-default active">
										<input type="radio" name="options" id="opt_projects" autocomplete="off" checked><?=$locale['projects']?>
									  </label>
									  <label class="btn btn-default">
										<input type="radio" name="options" id="opt_users" autocomplete="off"><?=$locale['users']?>
									  </label>
									  <label class="btn btn-default">
										<input type="radio" name="options" id="opt_tags" autocomplete="off"><?=$locale['tags']?>
									  </label>
									  <label class="btn btn-default">
										<input type="radio" name="options" id="opt_skills" autocomplete="off"><?=$locale['skills']?>
									  </label>
									</div>
								</div>
							</div>
						</div>
						<div class="row  text-center">
							<div class="col-xs-12">
								<h3><?=$locale['sorted_by']?>...</h3>
								<div class="form-group">
									<div class="btn-group" data-toggle="buttons">
										<label class="btn btn-default active">
											<input type="radio" name="options" id="opt_relevance" autocomplete="off" checked><?=$locale['relevance']?>
										</label>
										<label class="btn btn-default">
											<input type="radio" name="options" id="opt_alphabetical" autocomplete="off"><?=$locale['alphabet']?>
										</label>
										<label class="btn btn-default">
											<input type="radio" name="options" id="opt_chronological" autocomplete="off"><?=$locale['date']?>
										</label>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="row">     
				  <div class="col-md-12 text-right">
					<button type="button" class="btn btn-default"><?=$locale["search"]?></button>
				  </div>
			</div>
          </div>
       </div>
   </div>
</div>

<div id="settingsModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span></button>
              <h2 class="text-center"><?=$locale["settings"]?></h2>
          </div>
          <div class="modal-body">
			
          </div>
       </div>
   </div>
</div>


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
      <a class="navbar-brand" href="<?=abspath("")?>"><img style="margin: -10px;" src="<?=abspath("/public/images/logo.png")?>" height="40"/></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-left">
       <li><a href="#toggle-menue" style="font-size:20px; height:50px;" id="menu-toggle"><span class="glyphicon glyphicon-list"></span></a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <form class="navbar-form navbar-left" role="search">
			<a href="<?=abspath("CreateProject")?>" class="btn btn-create-project" id="btn_create_project"><span class="glyphicon glyphicon-plus" style="margin-right: 5px;"></span><?=$locale["create_a_project"]?></a>
			<div class="form-group">
				<input type="text" class="form-control" placeholder="<?=$locale['placeholder_search']?>">
			</div>
			<div class="btn-group">
				<button type="button" class="btn btn-default"><?=$locale["search"];?></button>
				<button type="button" class="btn btn-default" data-toggle="modal" data-target="#AdvancedSearchModal"><?=$locale["advanced"] ?></button>
			</div>
        </form>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$locale["more"]?><span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#"><?=$locale["about"]?></a></li>
            <li class="divider"></li>
			<?php if($_DATA["user"] != null){?>
			<li><a href="#settingsModal" role="button" data-toggle="modal"><?=$locale["settings"]?></a></li>
            <li><a href="#" id="logout_btn"><?=$locale["logout"]?></a></li>
			<?php }else{ ?>
            <li><a href="#loginModal" role="button" data-toggle="modal">Login</a></li>
			<?php } ?>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<div id="wrapper" class="toggled">
	<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
      <li class="sidebar-brand"><?php echo($_DATA["user"] != null ? $_DATA["user"] : $locale["welcome"]); ?></li>
      <li>
        <a href="<?=abspath("profile")?>"><?=$locale["profile"]?></a>
      </li>
      <li>
        <a href="<?=abspath("MyProjects")?>"><?=$locale["my_projects"]?></a>
      </li>
      <li>
        <a href="<?=abspath("favorites")?>"><?=$locale["favorites"]?></a>
      </li>
      <li>
        <a href="<?=abspath("chat")?>"><?=$locale["conversations"]?></a>
      </li>
    </ul>
  </div>
  <div id="page-content-wrapper"> 
  	<div id="page-content-div">
  		<?php echo $_DATA["content"]; ?>
  	</div>
  	<div id="page-footer-div">
  		<?php echo $_DATA["footer"]; ?>
  	</div>   
  </div>
</div>

<div class="user-review">
<img src="../public/images/default-profile-pic.png" class="img-rounded pull-left" height="50" width="50"/>
<h3 class="user-review-title" id="user-review-username">Username</h3>
<p>Projects involved: 30</p>
</div>

<script>
var mouse_x, mouse_y;
$(document).mousemove(function(event) {
        mouse_x = event.pageX;
        mouse_y = event.pageY;
		
});

$(document).ready(function(){
	var tOut;
		$('.user-review').hide();
		$('.user').hover(function(){
			var id = $(this).attr( "user-id" );
			tOut = setTimeout(function(){
					$('.user-review').animate({left: mouse_x+"px", top: mouse_y+"px"});
					
					$.ajax({url: "<?=abspath("/auth/get_user/")?>"+id}).done(function(data){
					var result = JSON.parse(data);
					
					$('#user-review-username').text(result.username);
					$('.user-review').fadeIn();
					
					});					
				},1000);
		},function(){
			clearTimeout(tOut);
			$('.user-review').fadeOut();		
		});

});
</script>