<?php

require_once("../core/Model.php");

class TagModel implements Model{
	public function create_tag($name){
		global $mysqli;

		if(self::get_tag($name)){
			return array("ERROR" => "ERR_ALREADY_EXISTS");
		}

		$stmt_insert = $mysqli->prepare("INSERT INTO tag (name) VALUES (?)");
		$stmt_insert->bind_param("s", $name);
		$result = $stmt_insert->execute();

		if(!$result){
			return array("ERROR" => "ERR_DB_INSERT_FAILED");
		} else {
			return array($mysqli->insert_id);
		}
	}

	public function autocomplete_tag($name){
		global $mysqli;

		$autocomplete_term = $name."%";

		$stmt_autocomplete = $mysqli->prepare("SELECT tag_id, name FROM tag WHERE name LIKE ?");
		$stmt_autocomplete->bind_param("s", $autocomplete_term);
		$stmt_autocomplete->execute();

		$stmt_autocomplete->bind_result($res_tag_id, $res_name);

		$autocomplete_obj = array();

		while($stmt_autocomplete->fetch()){
			array_push($autocomplete_obj, array($res_tag_id, $res_name));
		}

		return $autocomplete_obj;
	}

	public function get_tag_by_id($tag_id){
		global $mysqli;
		$stmt_check_existence = $mysqli->prepare("SELECT tag_id, name FROM tag WHERE tag_id = ?");
		$stmt_check_existence->bind_param("i", $tag_id);
		$stmt_check_existence->execute();
		$stmt_check_existence->bind_result($res_tag_id, $res_name);

		$result = array();

		if($stmt_check_existence->fetch()){
			$result["tag_id"] = $res_tag_id;
			$result["name"] = $res_name;
		}

		$stmt_check_existence->close();

		return $result;
	}

	public function get_tag_by_name($name){
		global $mysqli;
		$stmt_check_existence = $mysqli->prepare("SELECT tag_id, name FROM tag WHERE name = ?");
		$stmt_check_existence->bind_param("s", $name);
		$stmt_check_existence->execute();
		$stmt_check_existence->bind_result($res_tag_id, $res_name);

		$result = array();

		if($stmt_check_existence->fetch()){
			$result["tag_id"] = $res_tag_id;
			$result["name"] = $res_name;
		}

		$stmt_check_existence->close();

		return $result;
	}

	//Tries to retrieve tag from DB, if said tag doesn't exist, create one
	//Always returns a valid tag-id
	public function request_tag($name){
		$tag = self::get_tag_by_name($name);

		if(sizeof($tag) == 0){
			return self::create_tag($name);
		} else {
			return $tag;
		}
	}
}

?>