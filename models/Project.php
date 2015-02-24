<?php
class Project extends ActiveRecord{
	public function getTitlePicture(){
		return $this->getRelative("Picture", "title_picture_id");
	}

	public function getMemberCount(){
		global $mysqli;

		$stmt_get_member_count = $mysqli->prepare("SELECT COUNT(project_position_id) as member_count FROM project_position WHERE user_id IS NOT NULL AND project_id = ?");
		$stmt_get_member_count->bind_param("i", $this->project_id);
		$stmt_get_member_count->execute();

		return $stmt_get_member_count->get_result()->fetch_assoc()["member_count"];
	}

	public function getFavCount(){
		$favs = DBEZ::find("project_fav", ["project_id" => $this->project_id], ["project_fav_id"]);

		return sizeof($favs);
	}


}
?>