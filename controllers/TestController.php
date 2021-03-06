<?php

require_once(abspath_lcl("/core/Controller.php"));

class TestController extends Controller{
	public function auth(){
		global $locale;
		global $CONFIG;

		$locale->load("en-us");

		$auth = Core::model("Auth");
		$logged_in_user = $auth->get_current_user();
		if($logged_in_user){
			$login_name = $logged_in_user["username"];
		} else {
			$login_name = null;
		}

		$content = Core::view("LoginTest", array("login" => $login_name));
		return Core::view("HtmlBase", array(
			"title" => "Login Test", 
			"body" => $content, 
			"body_padding" => false, 
			"current_user" => $logged_in_user,
			"dark" => true
		));
	}

	public function project(){
		$project = Core::model("Project");
	
		$project_list = $project->get_all_projects();

		$content = Core::view("ProjectTest", array("projects" => $project_list));

		return Core::view("HtmlBase", array(	"title" => "Project Test",
																					"body" => $content,
																					"body_padding" => false));
	}

	public function tagbox(){
		$content = Core::view("TagBoxTest", ["project_id" => 1, "editable" => true]);

		return Core::view("HtmlBase", [ 
			"title" => "Tagbox Test",
			"body" => $content,
			"body_padding" => false
		]);
	}

	public function chat(){
		global $locale;

		$locale->load("en-us");

		$auth = Core::model("Auth");
		$chat = Core::model("Chat");
		$user = $auth->get_current_user();

		$chat_list = array();
		array_push($chat_list, $chat->get_chat(1));
		if($user){
			foreach($user["chat_participations"] as $chat_row){
				array_push($chat_list, $chat->get_chat($chat_row["chat_id"]));
			}
		}


		$content = Core::view("ChatTest", array("user_id" => $user["id"], "username" => $user["username"], "chat_list" => $chat_list));
		return Core::view("HtmlBase", array(	"title" => "Chat Test",
												"body" => $content,
												"body_padding" => false));
	}

	public function participationlist(){
		$content = Core::view("ParticipationListTest");
		return Core::view("HtmlBase", array(	"title" => "ParticipationList Test",
												"body" => $content,
												"body_padding" => false));
	}

	public function test(){
		$user = new User(1);
		$user->username = "faget";

		return print_r($user->getRelative("Picture", "picture_id"), true);

		return print_r($user, true);
	}

	public function posttest(){
		return print_r($_POST, true)."<br>".print_r($_FILES, true);
	}

	public function carousel(){
		return Core::view("HtmlBase", [
			"title" => "Projectie Carousel Test",
			"body" => Core::view("ProjectBanner", [
				"projects" => [
					[
						"project_id" => 1,
						"fav_count" => 24233,
						"participator_count" => 13
					],
					[
						"project_id" => 5,
						"fav_count" => 1233,
						"participator_count" => 5
					]
				]
			]),
			"body_padding" => false
		]);
	}

	public function picture_upload(){
		$auth = Core::model("Auth");

		$current_user = $auth->get_current_user();

		if(isset($_FILES["picture"])){

			if(!$current_user){
				return array("ERROR" => "ERR_NOT_LOGGED_IN");
			}

			return var_export(Picture::storeFromPost($_FILES["picture"], $current_user["user_id"]), true);

		} else {
			return Core::view("HtmlBase", [
				"title" => "Picture Upload",
				"body" => Core::view("PictureUploadTest"),
				"body_padding" => false
			]);
 		}
	}
}

?>