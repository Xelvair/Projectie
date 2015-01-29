<?php
require_once("../core/Controller.php");

class AboutController extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

		$dbez = Core::model("DBEZ");
		$auth = Core::model("Auth", $dbez);
		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
		
		$content = Core::view("About");
		
		$footer_array = array("user" => ($user == null ? null : $user["username"]));
		$footer = Core::view("Footer", $footer_array);
		
		$login_modal = Core::view("LoginModal");
			
			$contentwrap = Core::view("ContentWrapper", array(	
				"content" => $content, 
				"user" => $user,
				"login_modal" => $login_modal,
				"footer" => $footer)
			);
		
			$html = Core::view("HtmlBase", array(	
				"title" => "Projectie - Driving Development", 
				"body" => $contentwrap, 
				"body_padding" => true,
				"current_user" => $user)
			);
		
		
	return $html;
		
	}
}