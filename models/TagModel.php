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
			return self::get_tag($mysqli->insert_id);
		}
	}

	//returns an associative array of the requested tag
	//if the tag in question doesn't exist, returns an empty array
	//parameter may be a string, in which case the tag is searched by name
	//or an int, in which case it is searched by id instead
	//any other types will throw an exception 
	public function get_tag($tag){
		switch(gettype($tag)){
			case "integer":
				return DBEZ::find("tag", $tag, ["tag_id", "name"]);
				break;
			case "string":
				$result = DBEZ::find("tag", ["name" => $tag], ["tag_id", "name"]);
				return $result ? $result[0] : array();
				break;
			default:
				throw new InvalidArgumentException("get_tag function expects integer or string. ".gettype($tag)." given.");
				break;
		}
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
					return self::create_tag($tag);
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

		if($tag_entry){
			return DBEZ::delete("tag", $tag_entry["tag_id"]);
		}

		return false;
	}

	public function get_recommendations($search_string){
		global $mysqli;

		$search_string = "%".$search_string."%";

		$stmt_get_tags = $mysqli->prepare("SELECT tag_id, name FROM tag WHERE name LIKE ? LIMIT 25");
		$stmt_get_tags->bind_param("s", $search_string);
		$stmt_get_tags->execute();

		$result = $stmt_get_tags->get_result()->fetch_all(MYSQLI_ASSOC);

		return $result;
	}
}

?>