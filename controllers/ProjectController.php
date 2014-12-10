<?php
require_once("../core/Controller.php");

class ProjectController extends Controller{
	public function create(){
		$auth = $this->model("Auth");
		$logged_in_user = $auth->get_current_user();

		if($logged_in_user == null){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		if(isset($_POST["title"]) && isset($_POST["subtitle"]) && isset($_POST["description"])){
			$chat = $this->model("chat");

			//Create public and private chat for project
			$private_chat_id = $chat->create_public();
			$public_chat_id = $chat->create_private();

			//Add creator to private chat
			$chat->add_user($private_chat_id, $logged_in_user["id"]);

			//Create the project itself
			$project = $this->model("project");
			$create_result = $project->create(
				$logged_in_user["id"], 
				array(
					"title" => htmlentities($_POST["title"]), 
					"subtitle" => htmlentities($_POST["subtitle"]), 
					"description" => htmlentities($_POST["description"]),
					"private_chat_id" => $private_chat_id,
					"public_chat_id" => $public_chat_id
				)
			);
			return json_encode($create_result);
		} else {
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}
	}
	
	function index(){
	
		global $locale;
		global $CONFIG;

		$auth = $this->model("Auth");
		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user->get_lang());

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
		
		$footer_array = array("username" => "");
		$footer = $this->view("Footer", $footer_array);
		
		$content = $this->view("Project", "");
		
		$login_modal = $this->view("LoginModal", "");

		$contentwrap = $this->view("ContentWrapper", array(	"content" => $content, 
															"user" => ($user == null ? null : $user->get_name()),
															"login_modal" => $login_modal));

		$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
												"body" => $contentwrap, 
												"body_padding" => true));
		
		
		return $html;
	}
}
?>