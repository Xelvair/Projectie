<?php
require_once("../core/Controller.php");

class ProfileController extends Controller{
	public function show($data){
		global $locale;
		global $CONFIG;

		if(!isset($data[0]) || !filter_var($data[0], FILTER_VALIDATE_INT)){
			header("Location: /home");
			return;
		}

		$auth = Core::model("Auth");
		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}

		if(isset($viewed_user["ERROR"])){
			header("Location: /home");
			return;
		}
		
		$profile_content = array(
			"user" => User::get((int)$data[0]),
			"viewer_can_edit" => $user["user_id"] == $data[0]
		);

		$content = Core::view("Profile", $profile_content);

		$contentwrap = Core::view("ContentWrapper", array(
			"content" => $content, 
			"user" => $user
		));

		$html = Core::view("HtmlBase", array(	
			"title" => "Projectie - Driving Development", 
			"body" => $contentwrap, 
			"body_padding" => true,
			"current_user" => $user,
			"dark" => true
		));
		
		
		return $html;
	}
	
}


?>