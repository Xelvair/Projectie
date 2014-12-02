<?php
require_once("../core/Controller.php");

class ProfileController extends Controller{
	
	
	function index(){
		global $locale;
		global $CONFIG;

		$auth = $this->model("Auth");
		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
		
		$footer_array = array("username" => "");
		$footer = $this->view("Footer", $footer_array);
		
		$profile_content = array("footer" => $footer, "profile_pic" => abspath("/public/images/question_mark_big.png"), "username" => "Max da Boss", "sum_projects_created" => "123", "sum_projects_involved" => "34", "skill" => array("PHP|1","CSS|2", "JAVASCRIPT|3", "BOOTSTRAP|4", "C#|5"), "created_project" => array(), "involved_project" => array());
		
		array_push($profile_content["created_project"], array("title" => "Trending Project 1", "desc" => "Test Desc 1", "thumb" => abspath("/public/images/question_mark_small.png")));
		array_push($profile_content["created_project"], array("title" => "Trending Project 2", "desc" => "Test Desc 2", "thumb" => abspath("/public/images/question_mark_small.png")));
		array_push($profile_content["created_project"], array("title" => "Trending Project 3", "desc" => "Test Desc 3", "thumb" => abspath("/public/images/question_mark_small.png")));
		
		array_push($profile_content["involved_project"], array("title" => "Trending Project 1", "desc" => "Test Desc 1", "thumb" => abspath("/public/images/question_mark_small.png")));
		array_push($profile_content["involved_project"], array("title" => "Trending Project 2", "desc" => "Test Desc 2", "thumb" => abspath("/public/images/question_mark_small.png")));
		array_push($profile_content["involved_project"], array("title" => "Trending Project 3", "desc" => "Test Desc 3", "thumb" => abspath("/public/images/question_mark_small.png")));
		
		$content = $this->view("Profile", $profile_content);
		
		$login_modal = $this->view("LoginModal", "");

		$contentwrap = $this->view("ContentWrapper", array(	"content" => $content, 
															"user" => ($user == null ? null : $user["username"]),
															"login_modal" => $login_modal));

		$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
												"body" => $contentwrap, 
												"body_padding" => true));
		
		
		return $html;
	}
	
}


?>