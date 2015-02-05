<?php
require_once("../core/Controller.php");

class AboutController extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

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
		
		$content = Core::view("About");
			
		$contentwrap = Core::view("ContentWrapper", array(	
			"content" => $content, 
			"user" => $user)
		);
		
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