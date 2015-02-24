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
}

?>