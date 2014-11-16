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

		$stmt_create_proj = $mysqli->prepare("INSERT INTO project (creator_id, create_time, title, subtitle, description, public_chat_id, private_chat_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
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
			//IF a user sent the request, check if we have the rights to accept in behalf of the project
			$query_check_accept_rights = $mysqli->prepare("SELECT can_add_participants FROM project_participation WHERE project_id = ? and user_id = ?");
			$query_check_accept_rights->bind_param("ii", $res_project_id, $acceptor_id);
			$query_check_accept_rights->execute();
			$query_check_accept_rights->store_result();
			$query_check_accept_rights->bind_result($res_can_add_participants);

			if(!$query_check_accept_rights->fetch()){
				write_log(Logger::WARNING, "User #".$acceptor_id." has no rights to accept participation request #".$participation_req_id."1!");
				return array("ERROR" => "ERR_NO_RIGHTS");
			}

			if($res_can_add_participants){
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

	} //Such ugly code, very spaghetti, much refactor soon (maybe)

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

	public function add_picture($id, $picture_id){}
	public function remove_picture($id){}

}

?>