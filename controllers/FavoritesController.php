<?php
require_once("../core/Controller.php");

class FavoritesController extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

		$auth = Core::model("Auth");
		$project = Core::model("Project");

		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
		
		$list_content = $project->get_favorites((integer)$user["user_id"]);
		
		$list = array();
		foreach($list_content as $entry){
			array_push($list, Core::view("ProjectReview", $entry));
		}
		
		$content = Core::view("ListPage", array("list" => $list, "list_title" => $locale["favorites"]));
		
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