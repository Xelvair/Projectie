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

	//$_POST["request_type"] : type of requester, either PROJECT_TO_USER or USER_TO_PROJECT
	//$_POST["requester_id"] : id of the sender of the request
	//$_POST["requestee_id"] : id of the receiver of the request
	public function request_participation(){
		if(	
			!isset($_POST["request_type"]) || 
			!isset($_POST["requester_id"]) ||
			!isset($_POST["requestee_id"])
		){
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}

		$auth = $this->model("Auth");
		$project =$this->model("Project");
		$current_user = $auth->get_current_user();

		if(!$current_user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		switch($_POST["request_type"]){
			case "PROJECT_TO_USER":
				if($project->user_has_right($_POST["requester_id"], $current_user["id"], "add_participants")){
					return json_encode($project->request_participation($_POST["requester_id"], $_POST["requestee_id"], "PROJECT"));
				} else {
					return json_encode(array("ERROR" => "ERR_NO_RIGHTS"));
				}
				break;
			case "USER_TO_PROJECT":
				return json_encode($project->request_participation($_POST["requestee_id"], $_POST["requester_id"], "USER"));
				break;
			default:
				return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS")); 
				break;
		}
	}

	//$_POST["project_participation_request_id"]
	public function accept_participation(){
		$project = $this->model("Project");
		$user = $this->model("User");

		$current_user = $user->get_current_user();

		if(!$current_user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		$project->accept_request($_POST["project_participation_request_id"], $current_user["id"]);
	}

	public function tag(){
		$exists_and_filled_out = function(&$var){
			return (isset($var) && !empty($var));
		};

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

		if($exists_and_filled_out($_POST["tag_id"])){
			if(!filter_var($_POST["tag_id"], FILTER_VALIDATE_INT)){
				return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS")); 
			}
		}

		$auth = $this->model("Auth");
		$project = $this->model("Project");
		$tag = $this->model("Tag");

		$project_id = $_POST["project_id"];

		$current_user_id = $auth->get_current_user()["id"];

		if(!$project->user_has_right($project_id, $current_user_id, "edit")){
			return json_encode(array("ERROR" => "ERR_NO_RIGHTS"));
		}

		$tag_expr = $exists_and_filled_out($_POST["tag_id"]) ? (integer)$_POST["tag_id"] : (string)$_POST["tag_name"];

		return json_encode($project->tag($tag, $project_id, $tag_expr));

	}

	public function untag(){

	}
}
?>