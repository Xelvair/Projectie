<?php

require_once("../core/Model.php");

class TagModel implements Model{
	public function create_tag($name){
		global $mysqli;

		if(self::tag_exists($name)){
			return array("ERROR" => "ERR_ALREADY_EXISTS");
		}

		$stmt_insert = $mysqli->prepare("INSERT INTO tag (name) VALUES (?)");
		$stmt_insert->bind_param("s", $mysqli->real_escape_string($name));
		$result = $stmt_insert->execute();

		if(!$result){
			return array("ERROR" => "ERR_DB_INSERT_FAILED");
		} else {
			return array();
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

	public function tag_exists($name){
		global $mysqli;
		$stmt_check_existence = $mysqli->prepare("SELECT tag_id FROM tag WHERE name = ?");
		$stmt_check_existence->bind_param("s", $mysqli->real_escape_string($name));
		$stmt_check_existence->execute();

		$exists = ($stmt_check_existence->get_result()->num_rows > 0);

		$stmt_check_existence->close();

		return $exists;
	}
}

?>