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
		
		$footer_array = array("user" => ($user == null ? null : $user["username"]));
		$footer = $this->view("Footer", $footer_array);
		
		$projects_involved = array("entries" => array(), "list_title" => $locale["projects_involved"]);
		array_push($projects_involved["entries"], array("title" => "Trending Project 1", "desc" => "Test Desc 1", "thumb" => abspath("/public/images/default-profile-pic.png"), "creator" => array("id" => "1", "name" => "admin"), "source" => array("id" => "1", "name" => "Test Project"), "time" => "09:12"));
		$projects_involved_list = $this->view("TitleDescriptionList", $projects_involved);
		
		$projects_created = array("entries" => array(), "list_title" => $locale["projects_created"]);
		array_push($projects_created["entries"], array("title" => "Trending Project 1", "desc" => "Test Desc 1", "thumb" => abspath("/public/images/default-profile-pic.png"), "creator" => array("id" => "1", "name" => "admin"), "source" => array("id" => "1", "name" => "Test Project"), "time" => "09:12"));
		array_push($projects_created["entries"], array("title" => "Trending Project 1", "desc" => "Test Desc 1", "thumb" => abspath("/public/images/default-profile-pic.png"), "creator" => array("id" => "1", "name" => "admin"), "source" => array("id" => "1", "name" => "Test Project"), "time" => "09:12"));
		array_push($projects_created["entries"], array("title" => "Trending Project 1", "desc" => "Test Desc 1", "thumb" => abspath("/public/images/default-profile-pic.png"), "creator" => array("id" => "1", "name" => "admin"), "source" => array("id" => "1", "name" => "Test Project"), "time" => "09:12"));
		
		$projects_created_list = $this->view("TitleDescriptionList", $projects_created);
		
		$user_review = $this->view('UserReview', "");
		
		
		$profile_content = array("footer" => $footer, "profile_pic" => abspath("/public/images/question_mark_big.png"), "username" => "Max da Boss", "sum_projects_created" => "123", "sum_projects_involved" => "34", "skill" => array("PHP|1","CSS|2", "JAVASCRIPT|3", "BOOTSTRAP|4", "C#|5"), "projects_created" => $projects_created_list, "projects_involved" => $projects_involved_list, "user_review" => $user_review);
		
		
		
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