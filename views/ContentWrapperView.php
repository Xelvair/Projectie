<?php
//PARAMETERS
//content : site content
//user : array of the current user

require_once(abspath_lcl('templates/user_review.html'));

global $locale;

$user_logged_in = (isset($_DATA["user"]) && !empty($_DATA["user"]));
$user_id = isset($_DATA["user"]) ? $_DATA["user"]["user_id"] : 0;

?>
<script>
	$(document).ready(function(){
    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
    });		
		
		$('#logout_btn').on('click', function(){
		  $.post("<?=abspath("/auth/logout")?>").done(function(){
			 window.location.reload();
			});
		});
		
		$('#change_pw').click(function(){
			$(this).slideUp(300);
			setTimeout(function(){ 
				$('#new_pw_div').slideDown();
				$('#set_new_pw').val('true');
			}, 800);
			
			
		});
		
		$('#change_pw_close').click(function(){
			$('#new_pw_div').slideUp(300);
			setTimeout(function(){
				$('#change_pw').slideDown();
				$('#set_new_pw').val('false');
				$('#settings_pw').val('').parent().removeClass('has-error has-success');
				$('#settings_pw_retype').val('').parent().removeClass('has-error has-success');
				$('#settings_old_pw').val('').parent().removeClass('has-error has-success');
			}, 800);
			
			
		});
		
		$('#settings_pw').on('keyup', function(){
			var val = $(this).val();
			if(val.length > 7){
				$(this).parent().removeClass('has-error').addClass('has-success');
			}else{
				$(this).parent().removeClass('has-success').addClass('has-error');
			}
		});
		
		$('#settings_pw_retype').on('keyup', function(){
			var pw_rt = $(this).val();
			var pw = $('#settings_pw').val();
			if(pw_rt == pw){
				$(this).parent().removeClass('has-error').addClass('has-success');
			}else{
				$(this).parent().removeClass('has-success').addClass('has-error');
			}
		});
		
		$('#settings_lang').change(function(){
			$(this).parent().removeClass('has-error');
		})
		
		$(document).find("input").on('keypress',function(){
			var action = $(this).closest('form').attr('enter_action');
			if(action !== undefined && event.keyCode == 13){
				window[action]();	
			}
		});
		
	});
	
function user_update(){
	var set_pw = $('#set_new_pw').val();
	var pw = $('#settings_pw').val();
	var pw_rt = $('#settings_pw_retype').val();
	var lang = $('#settings_lang').val();
	var username = $('#settings_username').val();
	var old_pw = $('#settings_old_pw').val();
	var email = $('#settings_email').val();
	var pw_correct = false;
	var lang_correct = false;
	var username_correct = false;
	var user_id = Projectie.current_user.user_id;

	if(set_pw == "true"){
		if(pw != "" && pw.length > 7){
			if(pw_rt != ""){
				if(pw == pw_rt){
					pw_correct = true;
				}else{
					$('#settings_pw_retype').parent().addClass('has-error');
				}
			}else{
				$('#settings_pw_retype').parent().addClass('has-error');
			}
		}else{
			$('#settings_pw').parent().addClass('has-error');
		}
	}
	
	if(lang == "de-de" || lang == "en-gb" || lang == "en-us"){
		lang_correct = true;
	}else{
		$('#settings_lang').parent().addClass("has-error");
	}
	
	if(lang_correct){
		var form_data = new FormData($("#settings_form")[0]);
		$.ajax({
			type: "POST",
			url: Projectie.server_addr + "/auth/set_user",
			data: form_data,
			contentType: false,
			cache: false,
			processData: false,
			success: function(result){window.location.reload();}
		});
	}	
}

function settings_updated(data){
	var result = JSON.parse(data);
	
	if("ERROR" in result){
		switch(result.ERROR){
			case "ERR_INCORRECT_OLD_PASSWORD":
					alert('ERR_INCORRECT_OLD_PASSWORD');
				break;
			case "ERR_INVALID_ARGUMENTS":
					alert("ERR_INVALID_ARGUMENTS");
				break;
		}
	} else {
		window.location.reload();
	}
}

function remove_news(id){
	if(confirm("relly doe?")){
		$.post("<?=abspath('/project/remove_news');?>", {
			project_news_id : id
		}).done(function(data){
			var result = JSON.parse(data);
	
			if("ERROR" in result){
				switch(result.ERROR){
					case "ERR_NO_RIGHTS":
					alert('You have no permission to do that!')
				}
			}else{
				$('#post_id_'+id).slideUp(250);
			}
		});
	}
}
</script>

<?=Core::view("LoginModal")?>

<div id="settingsModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span></button>
              <h2 class="text-center"><?=$locale["settings"]?></h2>
          </div>
          <div class="modal-body">
				<form id="settings_form" enter_action="user_update">
					<input type="hidden" name="user_id" value="<?=$user_id?>">
					<div class="form-group" style="height:125px;">
						<div class="row">
							<label><?=$locale["profile_pic"]?></label>
						</div>
						<div class="row">
							<div class="col-xs-3">
								<img src="<?=$_DATA["user"] ? abspath($_DATA["user"]["picture"]->file_path) : "#"?>" class="media-object pull-left">
							</div>
							<div class="col-xs-9">
								<input id="input-1" type="file" name="profile_picture"> <!-- add 'class="file" to this item to reenable the fileinput panel' -->
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>E-mail</label>
						<input id="settings_email" name="email" type="text" class="form-control" value="<?=$_DATA["user"]["email"]?>">
					</div>
					<div style="min-height: 40px; width: 100%;">
						<label id="change_pw" style="margin-bottom: 20px; cursor: pointer;"><?=$locale['change_password']?>...</label>
						<input type="hidden" id="set_new_pw" value="false"/>
						<div id="new_pw_div" style="display: none;">
							<div class="form-group">
								<label><?=$locale["old_password"]?></label><span id="change_pw_close" style="cursor: pointer;" class="glyphicon glyphicon-remove pull-right"></span>
								<input id="settings_old_pw" name="old_password" type="password" class="form-control">
							</div>
							<div class="form-group">
								<label><?=$locale["new_password"]?></label>
								<input id="settings_pw" name="new_password" type="password" class="form-control">
							</div>
							<div class="form-group">
								<label><?=$locale["retype_new_password"]?></label>
								<input id="settings_pw_retype" type="password" class="form-control">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label><?=$locale["lang"]?></label>
						<select name="lang" class="form-control" id="settings_lang">
							<option value="0"><?=$locale["select_language"]?>.</option>
							<option value="en-us" <?php if($_DATA["user"]["lang"] == "en-us"){echo "selected";}?>>English(US)</option>
							<option value="en-gb" <?php if($_DATA["user"]["lang"] == "en-gb"){echo "selected";}?>>English(GB)</option>
							<option value="de-de" <?php if($_DATA["user"]["lang"] == "de-de"){echo "selected";}?>>Deutsch</option>
						</select>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<label style="cursor: pointer; color: #CC3114; text-decoration: underline;"><?=$locale["delete_account"]?></label>
							<a class="btn btn-default pull-right" role="button" onclick="user_update();"><?=$locale["save_changes"]?></a>
						</div>
					</div>
				</form>
          </div>
       </div>
   </div>
</div>

<nav class="navbar navbar-default  navbar-fixed-top" role="navigation">
  <div class="container-fluid max_width">
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
        	<a href="<?=abspath("/discover/")?>" class="btn btn-discover-projectie" id="btn_discover_projectie"><span class="glyphicon glyphicon-eye-open" style="margin-right: 5px;"></span><?=$locale["discover_projectie"]?></a>
					<?php if($_DATA["user"]) { ?>
						<a href="<?=abspath("/project/createnew")?>" class="btn btn-create-project" id="btn_create_project"><span class="glyphicon glyphicon-plus" style="margin-right: 5px;"></span><?=$locale["create_a_project"]?></a>
					<?php } else { ?>
						<a href="#loginModal" role="button" data-toggle="modal" class="btn btn-create-project"><span class="glyphicon glyphicon-plus" style="margin-right: 5px;"></span><?=$locale["create_a_project"]?></a>
					<?php } ?>
        </form>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$locale["more"]?><span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="<?=abspath('about')?>"><?=$locale["about"]?></a></li>
            <li class="divider"></li>
			<?php if($user_logged_in){?>
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
	      <!--<li>
	        <a href="<?=abspath("chat")?>"><?=$locale["conversations"]?></a>
	      </li>-->
	    </ul>
	  </div>
	<?php } ?>
  <div id="page-content-wrapper"> 
  	<div class="max_width">
  		<?php echo $_DATA["content"]; ?>
  	</div> 
	<div class="dark">
		<div class="max_width"> 
	 		<?=Core::view("Footer", ["user" => $_DATA["user"]["username"] ?: "Guest"])?>
	 	</div>
 	</div>
  </div>
</div>

