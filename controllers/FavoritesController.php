<?php
require_once("../core/Controller.php");

class FavoritesController extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

		$dbez = Core::model("DBEZ");
		$auth = Core::model("Auth", $dbez);
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
			$footer = Core::view("Footer", $footer_array);
			
			$login_modal = Core::view("LoginModal", "");
			
			$user_review = Core::view("UserReview", "");
			
			$list_content = array();
			array_push($list_content, array("title" => "Test 1", "desc" => "Test desc", "thumb" => abspath("/public/images/default-profile-pic.png"), "time" => "20:23"));
			array_push($list_content, array("title" => "Test 2", "desc" => "Test desc", "thumb" => abspath("/public/images/default-profile-pic.png"), "time" => "20:23"));
			array_push($list_content, array("title" => "Test 3", "desc" => "Test desc", "thumb" => abspath("/public/images/default-profile-pic.png"), "time" => "20:23"));
			array_push($list_content, array("title" => "Test 4", "desc" => "Test desc", "thumb" => abspath("/public/images/default-profile-pic.png"), "time" => "20:23"));
			array_push($list_content, array("title" => "Test 5", "desc" => "Test desc", "thumb" => abspath("/public/images/default-profile-pic.png"), "time" => "20:23"));
			array_push($list_content, array("title" => "Test 6", "desc" => "Test desc", "thumb" => abspath("/public/images/default-profile-pic.png"), "time" => "20:23"));
			
			$list = array();
			foreach($list_content as $entry){
				array_push($list, Core::view("Entry", $entry));
			}
			
			$content = Core::view("ListPage", array("list" => $list, "list_title" => $locale["favorites"], "user_review" => $user_review));
			
			$contentwrap = Core::view("ContentWrapper", array(	
				"content" => $content, 
				"user" => $user,
				"login_modal" => $login_modal,
				"footer" => $footer)
			);
		
			$html = Core::view("HtmlBase", array(	
				"title" => "Projectie - Driving Development", 
				"body" => $contentwrap, 
				"body_padding" => true,
				"current_user" => $user)
			);
		
		
	return $html;
		
	}
}

?>