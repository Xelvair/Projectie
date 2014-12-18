<?php

/*
	A single column meta is structured like this
	Array
	(
		[Field] => <field name>
		[Type] => <field type> (e.g. int(11), varchar(32), ...)
		[Null] => NO 					//Either YES or NO if it can be null
		[Key] => PRI 					//PRI if it's primary key, else nothing
		[Default] => 					//default value
		[Extra] => auto_increment 		//extra stuff
	)

	load_table_meta returns an array of these column metas.
*/

require_once("../core/Model.php");

class DBEZModel implements Model{
	public function find($table, $search, $result_fields){
		switch(gettype($search)){
			case "integer":
				return self::find_by_id($table, $search, $result_fields);
				break;
			case "array":
				return self::find_by_array($table, $search, $result_fields);
				break;
			default:
				throw new Exception("Invalid parameter sent to DBEZ::find()!");
				break;
		}
	}

	public function find_by_id($table, $search_id, $result_fields){
		$table_meta = self::load_table_meta($table);
		$primary_key_field = self::find_primary_key($table_meta);

		return self::find_by_array($table, [$primary_key_field["Field"] => $search_id], $result_fields);
	}

	public function find_by_array($table, $search_array, $result_fields){
		global $mysqli;

		$query = self::generate_query_string($table, $search_array, $result_fields);

		$result = $mysqli->query($query);

		if(!$result){
			throw new Exception("Query '".$query."' failed!");
		}

		return $result->fetch_all(MYSQLI_ASSOC);
	}

	public function load_table_meta($table){
		global $mysqli;

		$result = $mysqli->query("SHOW COLUMNS FROM ".$table);
		return $result->fetch_all(MYSQLI_ASSOC);
	}

	public function get_field_meta($table_meta, $field_name){
		foreach($table_meta as $field_meta){
			if($field_meta["Field"] == $field_name){
				return $field_meta;
			}
		}
		throw new Exception("Field not found!");
	}

	public function get_field_type($field_meta){
		if(strpos($field_meta["Type"], "varchar") === 0){
			return "string";
		} else if (strpos($field_meta["Type"], "int") === 0){
			return "integer";
		} else if (strpos($field_meta["Type"], "tinyint") === 0){
			return "boolean";
		} else if (strpos($field_meta["Type"], "enum") === 0){
			return "string";
		} else {
			throw new Exception("Failed to find type of field: ".$field_meta["Type"]);
		}
	}

	public function find_primary_key($table_meta){
		$primary_key = null;

		foreach($table_meta as $field_meta){
			if($field_meta["Key"] == "PRI"){
				if($primary_key == null){
					$primary_key = $field_meta;
				} else {
					throw new Exeption("Duplicate primary key detected!");
				}
			}
		}

		if($primary_key){
			return $primary_key;
		} else {
			throw new Exception("No primary key found!");
		}
	}

	public function generate_query_string($table, $search_array, $result_array){
		global $mysqli;

		$query_str = "SELECT";

		//add all requested fields to query
		$is_first = true;
		foreach ($result_array as $result_entry){

			//on every entry but the first, add a semicolon
			$is_first ? $is_first = false : $query_str .= ",";

			$query_str .= " ".$result_entry;
		}

		$query_str .= " FROM ".$table;

		if(sizeof($search_array) > 0){

			$query_str .= " WHERE";

			$table_meta = self::load_table_meta($table);

			$is_first = true;
			foreach($search_array as $search_entry_key => $search_entry_value){
				$field_meta = self::get_field_meta($table_meta, $search_entry_key);
				$field_type = self::get_field_type($field_meta);

				if(gettype($search_entry_value) != $field_type){
					throw new Exception("Type mismatch! ".$field_type." required but ".gettype($search_entry_value)." given!");
				}

				//on every entry but the first, add a semicolon
				$is_first ? $is_first = false : $query_str .= " AND";

				$query_str .= " ".$search_entry_key." = ";

				if($field_type == "string")
					$query_str .= '"';

				$query_str .= $mysqli->real_escape_string($search_entry_value);

				if($field_type == "string")
					$query_str .= '"';
			}
		}	
		return $query_str;
	}
}

?>