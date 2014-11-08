<?php
global $locale;

?>

<script>

function user_login(){
	var email = document.getElementById("email").value;
	var pw = document.getElementById("pw").value;
		
	$('#email').popover('destroy');
	$('#pw').popover('destroy');	
		
	$("#loginModal").find(".form-group").removeClass("has-error");
		
	if(email != ""){
		$("#email_form_group").removeClass("has-error");		
		if(pw != ""){
			$("#pw_form_group").removeClass("has-error");
			$.post( "<?=abspath("/auth/login")?>", 
							{email: email, password: pw}
			).done(function(data){
				var result = JSON.parse(data);
				if("ERROR" in result){
					switch(result.ERROR){
						case "ERR_USER_NOT_FOUND":
						 $('#email').popover('show');
						 $("#email_form_group").addClass("has-error");
							break;
						case "ERR_INCORRECT_PASSWORD":
						$('#pw').popover('show');
						$("#pw_form_group").addClass("has-error");
							break;
					}
				} else {
					window.location.href = "<?=abspath("")?>";
				}
			});
		} else {
			$("#pw_form_group").addClass("has-error");
		}
	} else {
		$("#email_form_group").addClass("has-error");
	}
}
	
function user_register(){
	var email = document.getElementById("reg_email").value;
	var username = document.getElementById("reg_username").value;
	var password = document.getElementById("reg_password").value;
	var retype_password = document.getElementById("reg_retypepassword").value;
	var lang = document.getElementById("reg_language").value;
	
	$("#registerModal").find(".form-group").removeClass("has-error");	

	if(email != ""){	  
	  if(username != ""){
		  if(password != ""){
			  if(retype_password != ""){
			  	if(password == retype_password){  
					  if(lang != 0){
						  $.post(	"<?=abspath('/auth/register')?>", 
						  				{email: email, username: username, password: password, lang: lang}
						  ).done(function(data){
								var result = JSON.parse(data);
								
								if("ERROR" in result){
									switch(result.ERROR){
										case "ERR_INVALID_EMAIL":
											$('#reg_email').popover('show');
											$("#reg_email_form_group").addClass("has-error");
											break;
										case "ERR_INVALID_PASSWORD":
											$('#reg_password').popover('show');
											$("#reg_password_form_group").addClass("has-error");
											break;
										case "ERR_INVALID_USERNAME":
											$('#reg_username').popover('show');
											$("#reg_username_form_group").addClass("has-error");
											break;									
										case "ERR_USERNAME_IN_USE":
											$('#reg_username').popover('show');
											$("#reg_username_form_group").addClass("has-error");
											break;
										case "ERR_EMAIL_IN_USE":
											$('#reg_email').popover('show');
											$("#reg_email_form_group").addClass("has-error");
											break;
										}
									} else {
										window.location.href = "<?=abspath("")?>";
									}
							});//post
					  } else {
					  	$("#reg_language_form").addClass("has-error");
					  }//if language
					} else {
						$("#reg_retypepassword_form").addClass("has-error");
					}//if retype pw = pw
			  } else {
					$("#reg_retypepassword_form").addClass("has-error");
				}//if retype pw
			}else{
				$("#reg_password_form").addClass("has-error");
			}//if pw
		} else {
			$("#reg_username_form").addClass("has-error");
		}//if username
	} else {
		$("#reg_email_form").addClass("has-error");	
	}//if email
}// ende user_register function
	
function modal_toggle(){
	$('#loginModal').modal('hide');
	setTimeout(function(){
			$('#registerModal').modal('show');			
		}, 500);
}
	
$(document).ready(function(){
	$("#login_btn").on("click", function(){
		user_login();
	});	

	$("#register_btn").on("click", function(){
		user_register();
	});	
});

</script>

<div id="loginModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span></button>
              <h2 class="text-center">Login</h2>
          </div>
          <div class="modal-body">
          	<form name="login_form">
                <div class="form-group" id="email_form_group">
                	<input type="text" class="form-control" placeholder="E-mail" id="email" data-content="<?=$locale["login_email_err"]?>" data-placement="top"/>
                </div>
                <div class="form-group" id="pw_form_group">
                	<input type="password" class="form-control" placeholder="<?=$locale["password"]?>" id="pw" data-content="<?=$locale["login_pw_err"]?>" data-placement="bottom" />
                </div>
                <div class="row">
                	<div class="col-md-6 text-left" style="padding-top:10px;">
                    <a onclick="modal_toggle()"><?=$locale["register"]?></a>
                  </div>      
                  <div class="col-md-6 text-right">
                  	<button id="login_btn" type="button" class="btn btn-default">Login</button>
                  </div>
                </div>
            </form>
          </div>
       </div>
   </div>
</div><!--loginModal-->




<div id="registerModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
  	<div class="modal-content">
    	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        	<span class="glyphicon glyphicon-remove"></span>
        </button>
			  <h2 class="text-center"><?=$locale["register"]?></h2>
     	</div>
      <div class="modal-body">
        <form name="login_form">
          <div class="form-group" id="reg_email_form">
          	<input type="text" class="form-control" placeholder="E-mail" title="E-mail" id="reg_email"  data-placement="bottom"/>
         	</div>
        	<div class="form-group" id="reg_username_form">
          	<input type="text" class="form-control" placeholder="<?=$locale["username"]?>" title="<?=$locale["username"]?>" id="reg_username"  data-placement="bottom"/>
          </div>
          <div class="form-group" id="reg_password_form">
            <input type="password" class="form-control" placeholder="<?=$locale["password"]?>" title="<?=$locale["password"]?>" id="reg_password"  data-placement="bottom"/>
          </div>
          <div class="form-group" id="reg_retypepassword_form">
            <input type="password" class="form-control" placeholder="<?=$locale["retype_password"]?>" title="<?=$locale["retype_password"]?>" data-placement="bottom" id="reg_retypepassword"/>
          </div>
          <div class="form-group" id="reg_language_form">
						<select name="language" class="form-control" id="reg_language">
							<option value="0"><?=$locale["select_language"]?>.</option>
							<option value="en-us">English(US)</option>
							<option value="en-gb">English(GB)</option>
							<option value="de-de">Deutsch</option>
						</select>
          </div>
          <div class="row"> 
            <div class="col-md-6 text-right pull-right">
              <button type="button" id="register_btn" class="btn btn-default"><?=$locale["submit"]?></button>
            </div>
          </div>
        </form>
    	</div>
    </div>
	</div>
</div>