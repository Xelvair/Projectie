<?php

require_once("../core/Debug.php");
require_once("../core/Model.php");

class ProjectModel implements Model{
	public function create($creator_id, $info){
		// $info PARAMETERS
		// [title]: Title of the Project
		// [subtitle]: Subtitle of the Project
		// [description]: Description of the Project
		global $mysqli;

		if(	$info["title"] == "" ||
			$info["subtitle"] == "" ||
			$info["description"] == "")
		{
			write_log(Logger::WARNING, "Invalid parameters to project::create()!".callinfo());
			return array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS");
		}

		$stmt_create_proj = $mysqli->prepare("INSERT INTO project (creator_id, create_time, title, subtitle, description) VALUES (?, ?, ?, ?, ?)");
		$stmt_create_proj->bind_param("iisss", $creator_id, time(), $info["title"], $info["subtitle"], $info["description"]);

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

		$stmt = $mysqli->prepare("SELECT project_id, creator_id, create_time, title, subtitle, description FROM project WHERE project_id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($result["id"], $result["creator_id"], $result["create_time"], $result["title"], $result["subtitle"], $result["description"]);

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

	public function get_all_projects(){
		global $mysqli;

		$result = $mysqli->query("SELECT * FROM project");
		return $result->fetch_all(MYSQLI_ASSOC);
	}

	public function add_picture($id, $picture_id){}
	public function remove_picture($id){}

}

?>