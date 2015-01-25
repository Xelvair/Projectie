<?php

require_once(abspath_lcl("/core/Controller.php"));

class TestController extends Controller{
	public function auth(){
		global $locale;
		global $CONFIG;

		$locale->load("en-us");

		$dbez = Core::model("DBEZ");
		$auth = Core::model("Auth", $dbez);
		$logged_in_user = $auth->get_current_user();
		if($logged_in_user){
			$login_name = $logged_in_user["username"];
		} else {
			$login_name = null;
		}

		$content = Core::view("LoginTest", array("login" => $login_name));
		return Core::view("HtmlBase", array("title" => "Login Test", "body" => $content, "body_padding" => false, "current_user" => $logged_in_user));
	}

	public function project(){
		$dbez = Core::model("dbez");
		$project = Core::model("Project", $dbez);
	
		$project_list = $project->get_all_projects();

		$content = Core::view("ProjectTest", array("projects" => $project_list));

		return Core::view("HtmlBase", array(	"title" => "Project Test",
																					"body" => $content,
																					"body_padding" => false));
	}

	public function tagbox(){
		$content = Core::view("TagBoxTest");

		return Core::view("HtmlBase", [ 
			"title" => "Tagbox Test",
			"body" => $content,
			"body_padding" => false
		]);
	}

	public function chat(){
		global $locale;

		$locale->load("en-us");

		$dbez = Core::model("DBEZ");
		$auth = Core::model("Auth", $dbez);
		$chat = Core::model("Chat", $dbez);
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
		$arr = [
			"faget" => 5,
			"lel" => "hi",
			"ayy" => "lmao@web.de",
			"gtc" => array(
				"mom" => "faygt"
			)
		];

		$format_arr = [
			"faget" => "int|min(3)",
			"lel" => "string",
			"ayy" => "email",
			"gtc" => array(
				"mom" => "int"
			)
		];

		return validate($arr, $format_arr);

	}
}



?>