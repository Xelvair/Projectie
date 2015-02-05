<?php
require_once("../core/Controller.php");

class MyProjectsController extends Controller{
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
		
		$list_content = array();
		array_push($list_content, array("title" => "Test 1", "desc" => "Test desc", "thumb" => abspath("/public/images/default-banner.png"), "members" => 30, "favs" => 20, "id" => 1));
		array_push($list_content, array("title" => "Test 1", "desc" => "Test desc", "thumb" => abspath("/public/images/default-banner.png"), "members" => 30, "favs" => 20, "id" => 1));
		array_push($list_content, array("title" => "Test 1", "desc" => "Test desc", "thumb" => abspath("/public/images/default-banner.png"), "members" => 30, "favs" => 20, "id" => 1));
		array_push($list_content, array("title" => "Test 1", "desc" => "Test desc", "thumb" => abspath("/public/images/default-banner.png"), "members" => 30, "favs" => 20, "id" => 1));
		
		$list = array();
		foreach($list_content as $entry){
			array_push($list, Core::view("ProjectReview", $entry));
		}
		
		$content = Core::view("ListPage", array("list" => $list, "list_title" => $locale["my_projects"]));
		
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