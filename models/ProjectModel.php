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
		if($stmt_create_proj->execute()){
			write_log(Logger::DEBUG, "Created project '".$info["title"]."'!");
			return array();
		} else {
			write_log(Logger::ERROR, "Creation of project '".$info["title"]."' failed, query error!");
			return array("ERROR" => "ERR_DB_INSERT_FAILED");
		}
	}

	public function get($id){}
	public function set($creator_id, $id, $info){}
	public function add_picture($id, $picture_id){}
	public function remove_picture($id){}

}

?>