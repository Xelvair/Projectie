<?php

require_once("../core/Debug.php");
require_once("../core/Model.php");

class ProjectModel implements Model{
	private $dbez = null;

	public function __construct(DBEZModel $dbez){
		$this->dbez = $dbez;
	}

	public function create($creator_id, $info){
		// $info PARAMETERS
		// [title]: Title of the Project
		// [subtitle]: Subtitle of the Project
		// [description]: Description of the Project
		// [public_chat_id]: ID of public chat
		// [private_chat_id]: ID of private chat 

		if(	$info["title"] == "" ||
			$info["subtitle"] == "" ||
			$info["description"] == "" ||
			$info["public_chat_id"] == "" ||
			$info["private_chat_id"] == "")
		{
			write_log(Logger::WARNING, "Invalid parameters to project::create()!".callinfo());
			return array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS");
		}

		$project_id = $this->dbez->insert("project", [
			"creator_id" => $creator_id,
			"create_time" => time(),
			"title" => $info["title"],
			"subtitle" => $info["subtitle"],
			"description" => $info["description"],
			"public_chat_id" => $info["public_chat_id"],
			"private_chat_id" => $info["private_chat_id"],
			"active" => 1
		]);

		$project_participation_id = $this->dbez->insert("project_participation", [
			"project_id" => $project_id,
			"user_id" => $creator_id,
			"can_delete" => 0,
			"can_edit" => 0,
			"can_communicate" => 0,
			"can_add_participants" => 0,
			"can_remove_participants" => 0
		]);
		
		return array();
	}

	public function get_participators($project_id){
		return $this->dbez->find(
			"project_participation", 
			["project_id" => $project_id], 
			["project_participation_id", "user_id", "can_delete", "can_edit", "can_communicate", "can_add_participants", "can_remove_participants"],
			DBEZ_SLCT_KEY_AS_INDEX
		);
	}

	public function get($id){
		$project = $this->dbez->find("project", $id, ["project_id", "creator_id", "create_time", "title", "subtitle", "description", "public_chat_id", "private_chat_id"]);

		if(!$project){
			write_log(Logger::WARNING, "Failed to retrieve project #".$id." from databaase!");
			return array("ERROR" => "ERR_PROJECT_NONEXISTENT");
		}

		$project["participators"] = self::get_participators($id);

		return $project;
	}

	public function exists($project_id){
		return !!$this->dbez->find("project", $project_id, ["project_id"]);
	}

	public function set($creator_id, $id, $info){
		global $mysqli;

		$update_str = "";
		$is_first_attr = true;

		//Iterate through attributes of $info object,
		//Update for each existing valid attribute
		foreach($info as $attr_name => $attr_val){
			if(
				$attr_name == "creator_id" ||
				$attr_name == "create_time" ||
				$attr_name == "title" ||
				$attr_name == "subtitle" ||
				$attr_name == "description"
			)
			{
				$stmt = $mysqli->prepare("UPDATE project SET ".$attr_name." = ? WHERE project_id = ?");
				$stmt->bind_param("si", $attr_value, $id);
				
				if($stmt->execute()){
					write_log(Logger::ERROR, "Execution of query failed!".callinfo());
					throw new Exception("Execution of query failed!");
				}
			} else {
				write_log(Logger::WARNING, "Invalid attribute in info structure: '".$attr_name."'!".callinfo());
			}
		}
	}

	//REQUEST_TYPE: ENUM["USER_TO_PROJECT", "PROJECT_TO_USER"]
	public function request_participation($chat_obj, $project_id, $user_id, $request_type){
		global $mysqli;

		//Check parameters
		if(
			$request_type != "USER_TO_PROJECT" &&
			$request_type != "PROJECT_TO_USER"
		){
			write_log(Logger::ERROR, "Invalid parameter for function request_participation!".callinfo());
			return array("ERROR" => "ERR_INVALID_PARAMETERS");
		}

		if(self::exists_participation_request($project_id, $user_id)){
			write_log(Logger::ERROR, "User #".$user_id."has already requested participation at project #".$project_id."!".callinfo());
			return array("ERROR" => "ERR_PARTICIPATION_REQUEST_ALREADY_EXISTS");
		}

		if(self::exists_participation($project_id, $user_id)){
			write_log(Logger::ERROR, "User #".$user_id." is already participating in project #".$project_id."!".callinfo());
			return array("ERROR" => "ERR_PARTICIPATION_ALREADY_EXISTS");
		}

		$project = self::get($project_id);

		$title_obj = array(
			"type" => "recruitment",
			"project_id" => $project_id,
			"user_id" => $user_id
		);

		$recruitment_chat_title = json_encode($title_obj);

		$recruitment_chat = $chat_obj->create_private(0, $recruitment_chat_title);

		$project_participation_id = $this->dbez->insert("project_participation_request", [
			"project_id" => $project_id,
			"user_id" => $user_id,
			"request_type" => $request_type,
			"chat_id" => $recruitment_chat["chat_id"]
		]);

		if(!$project_participation_id){
			write_log(Logger::DEBUG, $project_id." ".$user_id." ".$request_type." ".print_r($recruitment_chat, true));
			return array("ERROR" => "ERR_DB_INSERT_FAILED");
		}

		$chat_obj->add_user($recruitment_chat["chat_id"], $user_id);

		$participators = self::get_participators($project_id);
		foreach($participators as $participator){
			if($participator["can_add_participants"] || 
				 $participator["can_communicate"])
			{
				$chat_obj->add_user($recruitment_chat["chat_id"], $participator["user_id"]);
			}
		}

		return array();
	}

	public function accept_participation($participation_req_id, $acceptor_id){
		global $mysqli;

		$result = $this->dbez->find("project_participation_request", ["project_participation_request_id" => $participation_req_id], ["project_id", "user_id", "request_type"]);

		//Check if participation request exists
		if(!$result){
			write_log(Logger::WARNING, "Tried to accept non-existent participation request!".callinfo());
			return array("ERROR" => "ERR_PARTICIPATION_REQUEST_NONEXISTENT");
		}

		extract($result[0], EXTR_OVERWRITE | EXTR_PREFIX_ALL, "res");

		//Determine who originally sent requesst
		if($res_request_type == "USER_TO_PROJECT"){
			if(self::user_has_right($res_project_id, $acceptor_id, "add_participants")){
				self::create_participation($participation_req_id);
			} else {
				write_log(Logger::WARNING, "User #".$acceptor_id." has no rights to accept participation request #".$participation_req_id."2!");
				return array("ERROR" => "ERR_NO_RIGHTS");
			}
		} else if($res_request_type == "PROJECT_TO_USER"){
			//If a project sent the request, check if the asked user is the currently logged in user
			if($acceptor_id == $res_user_id){
				self::create_participation($participation_req_id);
			}
		} else {
			write_log(Logger::ERROR, "Found corrupt db entry in project participation request table!");
			return array("ERROR" => "ERR_CORRUPT_DB_ENTRY");
		}

		return array();

	}

	public function user_has_right($project_id, $user_id, $right){
		$result = $this->dbez->find("project_participation", ["project_id" => $project_id, "user_id" => $user_id], "*");

		if(!$result){
			return false;
		}

		$row = $result[0];

		if(isset($row["can_".$right])){
			return (boolean)$row["can_".$right];
		} else {
			throw new Exception("No entry found for 'can_".$right."' in database!");
		}
	}

	public function create_participation($participation_req_id){
		$result = $this->dbez->find("project_participation_request", $participation_req_id, ["project_id", "user_id"]);

		if(!$result){
			write_log(Logger::ERROR, "Participation request #".$participation_req_id."doesn't exist!");
			return array("ERROR" => "ERR_NO_SUCH_PARTICIPATION_REQUEST");
		}

		extract($result, EXTR_OVERWRITE | EXTR_PREFIX_ALL, "res");

		if(self::exists_participation($res_project_id, $res_user_id)){
			write_log(Logger::ERROR, "User #".$res_user_id." is already participating in project #".$res_project_id."!".callinfo());
			return array("ERROR" => "ERR_PARTICIPATION_ALREADY_EXISTS");
		}

		//Remove entry in participation request table, since we're going to create the real deal now
		$this->dbez->delete("project_participation_request", $participation_req_id);

		$this->dbez->insert("project_participation", [
			"project_id" => $res_project_id,
			"user_id" => $res_user_id,
			"can_delete" => 0,
			"can_edit" => 0,
			"can_communicate" => 0,
			"can_add_participants" => 0,
			"can_remove_participants" => 0
		]);
	}

	public function get_all_projects(){
		return $this->dbez->find("project", [], "*");
	}

	public function exists_participation_request($project_id, $user_id){
		return !!$this->dbez->find("project_participation_request", ["project_id" => $project_id, "user_id" => $user_id], ["project_participation_request_id"]);
	}

	public function exists_participation($project_id, $user_id){
		return !!$this->dbez->find("project_participation", ["project_id" => $project_id, "user_id" => $user_id], ["project_participation_id"]);
	}

	public function tag($tag_model, $project_id, $tag){
		if(!self::exists($project_id)){
			return array("ERROR" => "ERR_PROJECT_NONEXISTENT");
		}

		if(gettype($tag) != "integer" && gettype($tag) != "string"){
			throw new InvalidArgumentException("request_tag function expects integer or string. ".gettype($tag)." given");
		}

		//Get tag from database
		$tag_entry = $tag_model->request_tag($tag);

		if(sizeof($tag_entry) <= 0){
			return array("ERROR" => "ERR_TAG_NONEXISTENT");
		}

		if(self::is_tagged($tag_model, $project_id, $tag)){
			return array("ERROR" => "ERR_PROJECT_ALREADY_TAGGED");
		}

		$tag_id = $tag_entry["tag_id"];

		$this->dbez->insert("project_tag", ["project_id" => $project_id, "tag_id" => $tag_id]);
	
		return array();
	}

	public function is_tagged($tag_model, $project_id, $tag){
		if(gettype($tag) != "integer" && gettype($tag) != "string"){
			throw new InvalidArgumentException("request_tag function expects integer or string. ".gettype($tag)." given");
		}

		$tag_entry = $tag_model->get_tag($tag);

		if(sizeof($tag_entry) <= 0){
			throw new InvalidArgumentException("request_tag function cannot find '".$tag."' in database!");
		}

		$tag_id = $tag_entry["tag_id"];

		return !!$this->dbez->find("project_tag", ["project_id" => $project_id, "tag_id" => $tag_id], ["project_tag_id"]);
	}

	public function untag($tag_model, $project_id, $tag){
		$tag_entry = $tag_model->get_tag($tag);

		$tag_id = $tag_entry["tag_id"];

		return !!$this->dbez->delete("project_tag", ["project_id" => $project_id, "tag_id" => $tag_id]);
	}

	public function get_tags($project_id){
		global $mysqli;

		$query_get_tags = $mysqli->prepare("SELECT t.tag_id AS tag_id, t.name AS name FROM project_tag pt LEFT JOIN tag t ON(pt.tag_id = t.tag_id) WHERE pt.project_id = ?");
		$query_get_tags->bind_param("i", $project_id);
		$query_get_tags->execute();
		$result = $query_get_tags->get_result();

		$query_get_tags->close();

		return $result->fetch_all(MYSQLI_ASSOC);
	}

	public function add_picture($id, $picture_id){}
	public function remove_picture($id){}

}

?>