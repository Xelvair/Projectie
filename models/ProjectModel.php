<?php

require_once("../core/Debug.php");
require_once("../core/Model.php");

class ProjectModel implements Model{
	public function create($creator_id, $info){
		// $info PARAMETERS
		// [title]: Title of the Project
		// [subtitle]: Subtitle of the Project
		// [description]: Description of the Project
		// [public_chat_id]: ID of public chat
		// [private_chat_id]: ID of private chat 
		global $mysqli;

		if(	$info["title"] == "" ||
			$info["subtitle"] == "" ||
			$info["description"] == "" ||
			$info["public_chat_id"] == "" ||
			$info["private_chat_id"] == "")
		{
			write_log(Logger::WARNING, "Invalid parameters to project::create()!".callinfo());
			return array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS");
		}

		$stmt_create_proj = $mysqli->prepare("INSERT INTO project (creator_id, create_time, title, subtitle, description, public_chat_id, private_chat_id, active) VALUES (?, ?, ?, ?, ?, ?, ?, true)");
		$stmt_create_proj->bind_param("iisssii", $creator_id, time(), $info["title"], $info["subtitle"], $info["description"], $info["public_chat_id"], $info["private_chat_id"]);

		//If we failed, exit
		if(!$stmt_create_proj->execute()){
			write_log(Logger::ERROR, "Creation of project '".$info["title"]."' failed, query error!");
			return array("ERROR" => "ERR_DB_INSERT_FAILED");
		}

		//Save project ID for participation entry
		$project_id = $mysqli->insert_id;

		$stmt_create_proj->free_result();

		//If successful, create the participation entry for the creator
		$stmt_create_participation = $mysqli->prepare("
			INSERT INTO project_participation (project_id, user_id, can_delete, can_edit, can_communicate, can_add_participants, can_remove_participants)
			VALUES(?, ?, true, true, true, true, true)
		");
		$stmt_create_participation->bind_param("ii", $project_id, $creator_id);
		if($stmt_create_participation->execute()){
			return array();
		} else {
			return array("ERROR" => "ERR_DB_INSERT_FAILED");
		}

	}

	public function get($id){
		global $mysqli;

		$stmt = $mysqli->prepare("SELECT project_id, creator_id, create_time, title, subtitle, description, public_chat_id, private_chat_Id FROM project WHERE project_id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($result["id"], $result["creator_id"], $result["create_time"], $result["title"], $result["subtitle"], $result["description"], $result["public_chat_id"], $result["private_chat_id"]);

		if($stmt->fetch()){
			write_log(Logger::WARNING, "Failed to retrieve project #".$id." from databaase!");
			return array("ERROR" => "ERR_PROJECT_NONEXISTENT");
		} else {
			return $result;
		}

	}

	public function exists($project_id){
		global $mysqli;

		$stmt_check_project = $mysqli->prepare("SELECT project_id FROM project WHERE project_id = ?");
		$stmt_check_project->bind_param("i", $project_id);
		$stmt_check_project->execute();
		$stmt_check_project->store_result();

		$result = $stmt_check_project->num_rows;

		$stmt_check_project->close();

		return ($result > 0);
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
				}
			} else {
				write_log(Logger::WARNING, "Invalid attribute in info structure: '".$attr_name."'!".callinfo());
			}
		}
	}

	//REQUESTER_TYPE: ENUM["USER", "PROJECT"]
	public function request_participation($project_id, $user_id, $requester_type){
		global $mysqli;

		//Check parameters
		if(
			$requester_type != "USER" &&
			$requester_type != "PROJECT"
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

		//If it doesn't exist, insert request into database
		$query_create_request = $mysqli->prepare("
			INSERT INTO project_participation_request (project_id, user_id, requester_type)
			VALUES (?, ?, ?)
		");

		$query_create_request->bind_param("iis", $project_id, $user_id, $requester_type);
		return $query_create_request->execute();
	}

	public function accept_participation($participation_req_id, $acceptor_id){
		global $mysqli;

		$query_check_participation = $mysqli->prepare("SELECT project_id, user_id, requester_type FROM project_participation_request WHERE project_participation_request_id = ?");
		$query_check_participation->bind_param("i", $participation_req_id);
		$query_check_participation->execute();
		$query_check_participation->store_result();
		$query_check_participation->bind_result($res_project_id, $res_user_id, $res_requester_type);

		//Check if participation request exists
		if(!$query_check_participation->fetch()){
			write_log(Logger::WARNING, "Tried to accept non-existent participation request!".callinfo());
			return array("ERROR" => "ERR_PARTICIPATION_REQUEST_NONEXISTENT");
		}

		//Determine who originally sent requesst
		if($res_requester_type == "USER"){
			if(self::user_has_right($res_project_id, $acceptor_id, "add_participants")){
				self::create_participation($participation_req_id);
			} else {
				write_log(Logger::WARNING, "User #".$acceptor_id." has no rights to accept participation request #".$participation_req_id."2!");
				return array("ERROR" => "ERR_NO_RIGHTS");
			}
		} else if($res_requester_type == "PROJECT"){
			//If a project sent the request, check if the asked user is the currently logged in user
			if($acceptor_id == $res_user_id){
				self::create_participation($participation_req_id);
			}
		} else {
			write_log(Logger::ERROR, "Found corrupt db entry in project participation request table!");
			return array("ERROR" => "ERR_CORRUPT_DB_ENTRY");
		}

		$query_check_participation->close();

	}

	public function user_has_right($project_id, $user_id, $right){
		global $mysqli;

		$stmt_get_rights = $mysqli->prepare("SELECT * FROM project_participation WHERE project_id = ? AND user_id = ?");
		$stmt_get_rights->bind_param("ii", $project_id, $user_id);
		$stmt_get_rights->execute();

		$result = $stmt_get_rights->get_result();

		if($result->num_rows <= 0){
			return false;
		}

		$row = $result->fetch_all(MYSQLI_ASSOC)[0];

		$stmt_get_rights->close();

		if(isset($row["can_".$right])){
			return (boolean)$row["can_".$right];
		}

		return false;
	}

	public function create_participation($participation_req_id){
		global $mysqli;

		$stmt_get_participation_req = $mysqli->prepare("SELECT project_id, user_id FROM project_participation_request WHERE project_participation_request_id = ?");
		$stmt_get_participation_req->bind_param("i", $participation_req_id);
		$stmt_get_participation_req->execute();
		$stmt_get_participation_req->store_result();
		$stmt_get_participation_req->bind_result($res_project_id, $res_user_id);

		if(!$stmt_get_participation_req->fetch()){
			$stmt_get_participation_req->close();
			write_log(Logger::ERROR, "Participation request #".$participation_req_id."doesn't exist!");
			return array("ERROR" => "ERR_NO_SUCH_PARTICIPATION_REQUEST");
		}

		if(self::exists_participation($res_project_id, $res_user_id)){
			$stmt_get_participation_req->close();
			write_log(Logger::ERROR, "User #".$res_user_id." is already participating in project #".$res_project_id."!".callinfo());
			return array("ERROR" => "ERR_PARTICIPATION_ALREADY_EXISTS");
		}

		$stmt_get_participation_req->close();

		//Remove entry in participation request table, since we're going to create the real deal now
		$stmt_remove_participation_req = $mysqli->prepare("DELETE FROM project_participation_request WHERE project_participation_request_id = ?");
		$stmt_remove_participation_req->bind_param("i", $participation_req_id);
		$stmt_remove_participation_req->execute();

		$stmt_create_participation = $mysqli->prepare("
			INSERT INTO project_participation (project_id, user_id, can_delete, can_edit, can_communicate, can_add_participants, can_remove_participants)
			VALUES(?, ?, false, false, false, false, false)
		");
		$stmt_create_participation->bind_param("ii", $res_project_id, $res_user_id);
		$stmt_create_participation->execute();
	}

	public function get_all_projects(){
		global $mysqli;

		$result = $mysqli->query("SELECT * FROM project");
		return $result->fetch_all(MYSQLI_ASSOC);
	}

	public function exists_participation_request($project_id, $user_id){
		global $mysqli;

		//Check whether this request already exists
		$query_check_req_existence = $mysqli->prepare("SELECT project_participation_request_id FROM project_participation_request WHERE project_id = ? AND user_id = ?");
		$query_check_req_existence->bind_param("ii", $project_id, $user_id);
		$query_check_req_existence->execute();

		$result = $query_check_req_existence->fetch() ? true : false;

		$query_check_req_existence->close();

		return $result;
	}

	public function exists_participation($project_id, $user_id){
		global $mysqli;

		$query_check_participation = $mysqli->prepare("SELECT project_participation_id FROM project_participation WHERE project_id = ? and user_id = ?");
		$query_check_participation->bind_param("ii", $project_id, $user_id);
		$query_check_participation->execute();

		$result = $query_check_participation->fetch() ? true : false;

		$query_check_participation->close();

		return $result;
	}

	public function tag($tag_model, $project_id, $tag){
		global $mysqli;

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

		$query_tag_project = $mysqli->prepare("INSERT INTO project_tag (project_id, tag_id) VALUES (?, ?)");
		$query_tag_project->bind_param("ii", $project_id, $tag_id);
		$query_tag_project->execute();
	
		return array();
	}

	public function is_tagged($tag_model, $project_id, $tag){
		global $mysqli;

		if(gettype($tag) != "integer" && gettype($tag) != "string"){
			throw new InvalidArgumentException("request_tag function expects integer or string. ".gettype($tag)." given");
		}

		$tag_entry = $tag_model->get_tag($tag);

		if(sizeof($tag_entry) <= 0){
			throw new InvalidArgumentException("request_tag function cannot find '".$tag."' in database!");
		}

		$tag_id = $tag_entry["tag_id"];

		$query_check_tag = $mysqli->prepare("SELECT project_tag_id FROM project_tag WHERE project_id = ? AND tag_id = ?");
		$query_check_tag->bind_param("ii", $project_id, $tag_id);
		$query_check_tag->execute();
		$query_check_tag->store_result();

		$result = $query_check_tag->num_rows;

		$query_check_tag->close();

		return ($result > 0);
	}

	public function untag($tag_model, $project_id, $tag){
		global $mysqli;

		$tag_entry = $tag_model->get_tag($tag);

		$tag_id = $tag_entry["tag_id"];

		$query_untag_project = $mysqli->prepare("DELETE FROM project_tag WHERE project_id = ? AND tag_id = ?");
		$query_untag_project->bind_param("ii", $project_id, $tag_id);
		$query_untag_project->execute();

		return array();
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