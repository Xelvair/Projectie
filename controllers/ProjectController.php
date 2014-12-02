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

	public function tag(){
		$exists_and_filled_out = function(&$var){
			return (isset($var) && $var != "");
		}

		if
		(
			!$exists_and_filled_out($_POST["project_id"]) ||
			!(
				$exists_and_filled_out($_POST["tag_id"]) ||
				$exists_and_filled_out($_POST["tag_name"])
			)
		){
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}

		$project_id = $_POST["project_id"];

		$auth = $this->model("Auth");
		$project = $this->model("Project");

		if($project->user_has_right()){

		}
	}

	public function untag(){

	}
}
?>