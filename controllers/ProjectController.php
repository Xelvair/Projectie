<?php
require_once("../core/Controller.php");

class ExampleController extends Controller{
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
			$chat->add_user($private_chat_id, $creator_id);

			//Create the project itself
			$project = $this->model("project");
			$create_result = $project->create(
				$logged_in_user->get_id(), 
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
}
?>