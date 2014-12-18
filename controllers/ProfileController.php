<?php
require_once("../core/Controller.php");

class ProfileController extends Controller{
	function show($data){
		global $locale;
		global $CONFIG;

		if(!isset($data[0]) || !filter_var($data[0], FILTER_VALIDATE_INT)){
			header("Location: /home");
			return;
		}

		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
		
		$viewed_user = $auth->get_user($data[0]);

		$footer_array = array("username" => "");
		$footer = $this->view("Footer", $footer_array);
		
		$profile_content = array(
			"footer" => $footer, 
			"user" => $viewed_user
		);

		if(isset($viewed_user["ERROR"])){
			header("Location: /home");
			return;
		}

		$content = $this->view("Profile", $profile_content);
		
		$login_modal = $this->view("LoginModal", "");

		$contentwrap = $this->view("ContentWrapper", array(	"content" => $content, 
															"user" => ($user == null ? null : $user["username"]),
															"login_modal" => $login_modal));

		$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
												"body" => $contentwrap, 
												"body_padding" => true,
												"current_user" => $user));
		
		
		return $html;
	}
	
}


?>