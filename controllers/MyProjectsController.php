<?php
require_once("../core/Controller.php");

class MyProjectsController extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

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
		
			$footer_array = array("user" => ($user == null ? null : $user["username"]));
			$footer = $this->view("Footer", $footer_array);
			
			$login_modal = $this->view("LoginModal", "");
			
			$user_review = $this->view("UserReview", "");
			
			$list_content = array("entries" => array());
			array_push($list_content["entries"], array("title" => "Test 1", "desc" => "Test desc", "thumb" => abspath("/public/images/default-profile-pic.png"), "time" => "20:23"));
			array_push($list_content["entries"], array("title" => "Test 1", "desc" => "Test desc", "thumb" => abspath("/public/images/default-profile-pic.png"), "time" => "20:23"));
			array_push($list_content["entries"], array("title" => "Test 1", "desc" => "Test desc", "thumb" => abspath("/public/images/default-profile-pic.png"), "time" => "20:23"));
			array_push($list_content["entries"], array("title" => "Test 1", "desc" => "Test desc", "thumb" => abspath("/public/images/default-profile-pic.png"), "time" => "20:23"));
			
			$list = $this->view("TitleDescriptionList", $list_content);
			
			$content = $this->view("ListPage", array("list" => $list, "list_title" => $locale["my_projects"], "user_review" => $user_review));
			
			$contentwrap = $this->view("ContentWrapper", array(	"content" => $content, 
															"user" => ($user == null ? null : $user["username"]),
															"login_modal" => $login_modal,
															"footer" => $footer));
		
			$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
													"body" => $contentwrap, 
													"body_padding" => true,
													"current_user" => $user));
		
		
	return $html;
		
	}
}

?>