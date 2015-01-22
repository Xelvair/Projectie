<?php
//PARAMETERS
//content : site content
//user : array of the current user

require_once(abspath_lcl('templates/user_review.html'));

global $locale;

$user_logged_in = (isset($_DATA["user"]) && !empty($_DATA["user"]));

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
		
		$('#btn_advanced_search').on('click', function(){
			
			var search_val = $('#advanced_search_input').val();
			var search_for = $("input[name='radio_search_for']:checked").val();
			var sorted_by = $("input[name='radio_sorted_by']:checked").val();
			
			if(search_val != ""){
			
			alert('search_val: '+search_val+'search_for: '+search_for+'sorted_by: '+sorted_by)
			}else{
				$('#advanced_search_group').addClass('has-error');
			}
			
		});
		
		$('#advanced_search_input').keypress(function(){
			$(this).parent().removeClass('has-error');
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
						<div class="form-group" id="advanced_search_group">
							<input type="text" id="advanced_search_input" class="form-control" placeholder="<?=$locale['placeholder_search']?>"/>
						</div> 
						<div class="row text-center">
							<div class="col-xs-12">
								<h3><?=$locale['search_for']?>...</h3>
								<div class="form-group">
									<div class="btn-group" data-toggle="buttons">
									  <label class="btn btn-default active">
										<input type="radio" name="radio_search_for" value="projects" autocomplete="off" checked><?=$locale['projects']?>
									  </label>
									  <label class="btn btn-default">
										<input type="radio" name="radio_search_for" value="users" autocomplete="off"><?=$locale['users']?>
									  </label>
									  <label class="btn btn-default">
										<input type="radio" name="radio_search_for" value="tags" autocomplete="off"><?=$locale['tags']?>
									  </label>
									  <label class="btn btn-default">
										<input type="radio" name="radio_search_for" value="skills" autocomplete="off"><?=$locale['skills']?>
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
											<input type="radio" name="radio_sorted_by" value="relevance" autocomplete="off" checked><?=$locale['relevance']?>
										</label>
										<label class="btn btn-default">
											<input type="radio" name="radio_sorted_by" value="alphabet" autocomplete="off"><?=$locale['alphabet']?>
										</label>
										<label class="btn btn-default">
											<input type="radio" name="radio_sorted_by" value="date" autocomplete="off"><?=$locale['date']?>
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
					<button type="button" id="btn_advanced_search" class="btn btn-default"><?=$locale["search"]?></button>
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
      <?php if($user_logged_in){ ?>
	      <ul class="nav navbar-nav navbar-left">
	      	<li><a href="#toggle-menue" style="font-size:20px; height:50px;" id="menu-toggle"><span class="glyphicon glyphicon-list"></span></a></li>
	      </ul>
      <?php } ?>
      <ul class="nav navbar-nav navbar-right">
        <form class="navbar-form navbar-left" role="search">
			<a href="<?=abspath("/project/createnew")?>" class="btn btn-create-project" id="btn_create_project"><span class="glyphicon glyphicon-plus" style="margin-right: 5px;"></span><?=$locale["create_a_project"]?></a>
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
	<?php if($user_logged_in){ ?>
		<div id="sidebar-wrapper">
	    <ul class="sidebar-nav">
	      <li class="sidebar-brand"><?=$_DATA["user"]["username"]?></li>
	      <?php if(isset($_DATA["user"]) && !empty($_DATA["user"])){ ?>
	      <li>
	        <a href="<?=abspath("profile/show/".$_DATA["user"]["user_id"])?>"><?=$locale["profile"]?></a>
	      </li>
	      <?php } ?>
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
	<?php } ?>
  <div id="page-content-wrapper"> 
  	<div id="page-content-div">
  		<?php echo $_DATA["content"]; ?>
  	</div>
  	<div id="page-footer-div">
  		<?php echo $_DATA["footer"]; ?>
  	</div>   
  </div>
</div>

