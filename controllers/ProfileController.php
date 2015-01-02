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

		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
		
		$viewed_user = $auth->get_user($data[0]);

		if(isset($viewed_user["ERROR"])){
			header("Location: /home");
			return;
		}

		$footer_array = array("username" => "");
		$footer = $this->view("Footer", $footer_array);

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
		
		$profile_content = array(
			"footer" => $footer, 
			"user" => $viewed_user,
			"projects_created" => $projects_created_list,
			"projects_involved" => $projects_involved_list
		);

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