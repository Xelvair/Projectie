<?php
require_once("../core/Controller.php");

class HomeController extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

		$auth = Core::model("Auth");
		$project = Core::model("Project");

		$current_user = $auth->get_current_user();
		if($current_user != null){
			$locale_load_result = $locale->load($current_user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
		
		$new = $project->get_new_projects(3);

		$html_new = array();
		
		foreach($new as $new){
			array_push($html_new, Core::view("ProjectReview", $new));
		}
				
		$trending = $project->get_trending_projects(3);
		
		$html_trending = array();
		
		foreach($trending as $trending){
			array_push($html_trending, Core::view("ProjectReview", $trending));
		}
	
    	$mainpagelists = array("top_project" => array("projects" => $project->get_top_projects(3), "editable" => false), "new" => $html_new, "trending" => $html_trending);

		$content = Core::view("MainPageContent", array_merge($mainpagelists, ["user" => $current_user]));

		$contentwrap = Core::view("ContentWrapper", array(	
			"content" => $content, 
			"user" => ($current_user == null ? null : $current_user)
		));

		$html = Core::view("HtmlBase", array(	
			"title" => "Projectie - Driving Development", 
			"body" => $contentwrap, 
			"body_padding" => true,
			"current_user" => $current_user,
			"dark" => true
		));

		return $html;
	}
}
?>