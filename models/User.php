<?php

class User extends ActiveRecord{
	public function getPicture(){
		return $this->getRelative("Picture", "picture_id");
	}

	public function getTags(){
		$tags = DBEZ::find("user_tag", ["user_id" => $this->user_id], ["tag_id"]);

		$result = [];
		foreach($tags as $tag){
			array_push($result, Tag::get((int)$tag["tag_id"]));
		}

		return $result;
	}

	public function getCreatedProjectsCount(){
		$result = DBEZ::find("project", ["creator_id" => $this->user_id], ["project_id"]);

		return sizeof($result);
	}

	public function getParticipatedProjectsCount(){
		$result = DBEZ::find("project_position", ["user_id" => $this->user_id], ["project_position_id"]);

		return sizeof($result);
	}

	public function getCreatedProjects(){
		$project_list = DBEZ::find("project", ["creator_id" => $this->user_id], "*");

		$result = [];
		foreach($project_list as $project){
			array_push($result, Project::get((int)$project["project_id"]));
		}

		return $result;
	}

	public function getJoinedProjects(){
		global $mysqli;

		$stmt_get_projects = $mysqli->prepare("SELECT p.project_id FROM project_position pp LEFT JOIN project p ON(pp.project_id = p.project_id) WHERE p.creator_id <> pp.user_id AND pp.user_id = ?");
		$stmt_get_projects->bind_param("i", $this->user_id);
		$stmt_get_projects->execute();

		$project_list = $stmt_get_projects->get_result()->fetch_all(MYSQLI_ASSOC);

		$result = [];
		foreach($project_list as $project){
			array_push($result, Project::get((int)$project["project_id"]));
		}

		return $result;
	}
}

?>