<?php
require_once("../core/Controller.php");

class MyProjectsController extends Controller{
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

		$projects_list = $project->get_participated_projects((integer)$user["user_id"]);
		$projects_html_list = [];
		foreach($projects_list as $entry){
			array_push($projects_html_list, Core::view("ProjectReview", $entry));
		}
		
		$content = Core::view("ListPage", array("list" => $projects_html_list, "list_title" => $locale["my_projects"]));
		
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