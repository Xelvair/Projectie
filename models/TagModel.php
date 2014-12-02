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
			return $mysqli->insert_id;
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

	//returns an associative array of the requested tag
	//if the tag in question doesn't exist, returns an empty array
	//parameter may be a string, in which case the tag is searched by name
	//or an int, in which case it is searched by id instead
	//any other types will throw an exception 
	public function get_tag($tag){
		global $mysqli;

		switch (gettype($tag)){
			case "integer":
				$stmt_check_existence = $mysqli->prepare("SELECT tag_id, name FROM tag WHERE tag_id = ?");
				$stmt_check_existence->bind_param("i", $tag);
				break;
			case "string":
				$stmt_check_existence = $mysqli->prepare("SELECT tag_id, name FROM tag WHERE name = ?");
				$stmt_check_existence->bind_param("s", $tag);
				break;
			default:
				throw new InvalidArgumentException("get_tag function expects integer or string. ".gettype($tag)." given.");
				break;
		}

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

	//similar to get_tag, except that if a string is passed, it will create the tag if it doesn't already exists
	//returns an empty array if an id is passed and that id is nonexistent
	public function request_tag($tag){
		//Check parameters
		if(gettype($tag) != "integer" && gettype($tag) != "string"){
			throw new InvalidArgumentException("request_tag function expects integer or string. ".gettype($tag)." given");
		}

		$tag_entry = self::get_tag($tag);

		if(sizeof($tag_entry) <= 0){
			switch(gettype($tag)){
				case "string":
					$new_tag_id = self::create_tag($tag);
					return self::get_tag($new_tag_id);
					break;
				case "integer":
					return array();
					break;
			}
		} else {
			return $tag_entry;
		}
	}

	public function delete_tag($is_admin, $tag){
		global $mysqli;

		if(!$is_admin){
			return array("ERROR" => "ERR_NO_RIGHTS");
		}

		//Check parameters
		if(gettype($tag) != "integer" && gettype($tag) != "string"){
			throw new InvalidArgumentException("request_tag function expects integer or string. ".gettype($tag)." given");
		}

		$tag_entry = self::get_tag($tag);

		if(sizeof($tag_entry) > 0){
			$stmt_delete_tag = $mysqli->prepare("DELETE FROM tag WHERE tag_id = ?");
			$stmt_delete_tag->bind_param("i", $tag_entry);
			$stmt_delete_tag->execute();
		}
	}
}

?>