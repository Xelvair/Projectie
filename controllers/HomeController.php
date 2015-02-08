<?php
require_once("../core/Controller.php");

class HomeController extends Controller{
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

		$news = array();
		
		array_push($news, array("project_id" => 1, "fav_count" => 14, "participator_count" => 75, "title" => "Protestie", "description" => "I just came to say hello", "id" => 1));
		array_push($news, array("project_id" => 1, "fav_count" => 14, "participator_count" => 75, "title" => "Protestie", "description" => "I just came to say hello", "id" => 1));
		array_push($news, array("project_id" => 1, "fav_count" => 14, "participator_count" => 75, "title" => "Protestie", "description" => "I just came to say hello", "id" => 1));
		
		$html_news = array();
		foreach($news as $news){
			array_push($html_news, Core::view("ProjectReview", $news));
		}
		
		$new = $project->get_new_projects(3);

		$html_new = array();
		
		foreach($new as $new){
			array_push($html_new, Core::view("ProjectReview", $new));
		}
				
		$trending = array();
		array_push($trending, array("project_id" => 1, "fav_count" => 14, "participator_count" => 75, "title" => "Protestie", "description" => "I just came to say hello", "id" => 1));
		array_push($trending, array("project_id" => 1, "fav_count" => 14, "participator_count" => 75, "title" => "Protestie", "description" => "I just came to say hello", "id" => 1));
		array_push($trending, array("project_id" => 1, "fav_count" => 14, "participator_count" => 75, "title" => "Protestie", "description" => "I just came to say hello", "id" => 1));
		
		$html_trending = array();
		
		foreach($trending as $trending){
			array_push($html_trending, Core::view("ProjectReview", $trending));
		}
	
    $mainpagelists = array("top_project" => array("projects" => $project->get_new_projects(3), "editable" => false), "new" => $html_new, "trending" => $html_trending, "news" => $html_news);

		$content = Core::view("MainPageContent", $mainpagelists);

		$contentwrap = Core::view("ContentWrapper", array(	
			"content" => $content, 
			"user" => ($user == null ? null : $user)
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