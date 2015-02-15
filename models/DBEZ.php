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

define("DBEZ_SLCT_KEY_AS_INDEX", 1 << 0);
define("DBEZ_INSRT_RETURN_ROW", 1 << 0);

class DBEZ{

	public static function insert($table, $data, $flags = 0){
		global $mysqli;

		if(!$table || !$data){
			throw new Exception("Invalid parameter sent to DBEZ::insert()!");
		}

		$query = self::generate_insert_query_string($table, $data);

		$result = $mysqli->query($query);

		if(!$result){
			throw new Exception("Query '".$query."' failed!");
		}

		if($flags & DBEZ_INSRT_RETURN_ROW){
			return self::find($table, $mysqli->insert_id, "*");
		} else {
			return $mysqli->insert_id;
		}
	}

	public static function find($table, $search, $result_format, $flags = 0){
		if(!$table || !$result_format){
			throw new Exception("Invalid parameter sent to DBEZ::find()!");
		}

		switch(gettype($search)){
			case "integer":
				return self::find_by_id($table, $search, $result_format, $flags);
				break;
			case "array":
				return self::find_by_array($table, $search, $result_format, $flags);
				break;
			default:
				throw new Exception("Invalid parameter sent to DBEZ::find()!");
				break;
		}
	}

	public static function delete($table, $search){
		if(!$table || !$search){
			throw new Exception("Invalid parameter sent to DBEZ::find()!");
		}

		switch(gettype($search)){
			case "integer":
				return self::delete_by_id($table, $search);
				break;
			case "array":
				return self::delete_by_array($table, $search);
				break;
			default:
				throw new Exception("Invalid parameter sent to DBEZ::delete()!");
				break;
		}
	}

	public static function update($table, $search, $update){
		if(!$table || !$update){
			throw new Exception("Invalid parameter sent to DBEZ::update()!");
		}

		switch(gettype($search)){
			case "integer":
				return self::update_by_id($table, $search, $update);
				break;
			case "array":
				return self::update_by_array($table, $search, $update);
				break;
			default:
				throw new Exception("Invalid parameter sent to DBEZ::update()!");
				break;
		}
	}

	public static function find_by_id($table, $search_id, $result_format, $flags = 0){
		$table_meta = self::load_table_meta($table);
		$primary_key_field = self::find_primary_key($table_meta);

		$result = self::find_by_array($table, [$primary_key_field["Field"] => $search_id], $result_format, $flags);
		return $result ? $result[0] : array();
	}

	public static function find_by_array($table, $search_array, $result_format, $flags = 0){
		global $mysqli;

		$query = self::generate_select_query_string($table, $search_array, $result_format);

		$result = $mysqli->query($query);
		$result_arr = $result->fetch_all(MYSQLI_ASSOC);

		if(!$result){
			throw new Exception("Query '".$query."' failed!");
		}

		$table_meta = self::load_table_meta($table);
		
		//If the caller choose to have the primary key as index in the result array,
		//we need to create and populate a new array and assign this to the original array
		if($flags & DBEZ_SLCT_KEY_AS_INDEX){
			$temp_arr = array();

			$primary_key_field = self::find_primary_key($table_meta)["Field"];

			for($i = 0; $i < sizeof($result_arr); $i++){
				$temp_arr[$result_arr[$i][$primary_key_field]] = $result_arr[$i];
			}

			$result_arr = $temp_arr;
		}

		self::fix_types($result_arr, $table_meta);

		return $result_arr;
	}

	public static function delete_by_id($table, $search_id){
		$table_meta = self::load_table_meta($table);
		$primary_key_field = self::find_primary_key($table_meta);

		return self::delete_by_array($table, [$primary_key_field["Field"] => $search_id]);
	}

	public static function delete_by_array($table, $search_array){
		global $mysqli;

		if(!$table || !$search_array){
			throw new Exception("Invalid parameter sent to DBEZ::insert()!");
		}

		$query = self::generate_delete_query_string($table, $search_array);

		$result = $mysqli->query($query);

		if(!$result){
			throw new Exception("Query '".$query."' failed!");
		}

		return true;
	}

	public static function update_by_id($table, $search_id, $update){
		$table_meta = self::load_table_meta($table);
		$primary_key_field = self::find_primary_key($table_meta);

		return self::update_by_array($table, [$primary_key_field["Field"] => $search_id], $update);
	}

	public static function update_by_array($table, $search_array, $update){
		global $mysqli;

		if(!$table || !$search_array){
			throw new Exception("Invalid parameter sent to DBEZ::update()!");
		}

		$query = self::generate_update_query_string($table, $search_array, $update);

		$result = $mysqli->query($query);

		if(!$result){
			throw new Exception("Query '".$query."' failed!");
		}

		return true;
	}

	public static function load_table_meta($table){
		global $mysqli;

		$result = $mysqli->query("SHOW COLUMNS FROM ".$table);
		
		return $result->fetch_all(MYSQLI_ASSOC);
	}

	public static function get_field_meta($table_meta, $field_name){
		foreach($table_meta as $field_meta){
			if($field_meta["Field"] == $field_name){
				return $field_meta;
			}
		}
		throw new Exception("Field not found!");
	}

	public static function get_field_type($field_meta){
		if(strpos($field_meta["Type"], "varchar") === 0){
			return "string";
		} else if (strpos($field_meta["Type"], "text") === 0){
			return "string";
		} else if (strpos($field_meta["Type"], "int") === 0){
			return "integer";
		} else if (strpos($field_meta["Type"], "bigint") === 0){
			return "integer";
		} else if (strpos($field_meta["Type"], "tinyint") === 0){
			return "integer";
		} else if (strpos($field_meta["Type"], "bit") === 0){
			return "boolean";
		} else if (strpos($field_meta["Type"], "enum") === 0){
			return "string";
		} else if (strpos($field_meta["Type"], "float") === 0){
			return "float";
		} else {
			throw new Exception("Failed to find type of field: ".$field_meta["Type"]);
		}
	}

	//turns anything that shouldn't be a string into a not-string
	//dammit, mysqli.
	public static function fix_types(&$result_array, $table_meta){
		foreach($result_array as &$result_entry){
			foreach($result_entry as $field_key => &$field_value){
				$field_meta = self::find_field($table_meta, $field_key);
				$field_type = self::get_field_type($field_meta);

				switch($field_type){
					case "integer":
						$field_value = (int)$field_value;
						break;
					case "boolean":
						$field_value = (boolean)$field_value;
						break;
					default:
						break;
				}
			}		
		}
	}

	public static function find_primary_key($table_meta){
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

	public static function find_field($table_meta, $field){
		foreach($table_meta as $field_meta){
			if($field_meta["Field"] == $field){
				return $field_meta;
			}
		}
		throw new Exception("Field not found!");
	}

	public static function generate_select_query_string($table, $search_array, $result_format){
		global $mysqli;

		if(gettype($result_format) == "array"){
			$query_str = "SELECT ".implode(", ", $result_format)." FROM ".$table;
		} else if (gettype($result_format) == "string" && $result_format == "*"){
			$query_str = "SELECT * FROM ".$table;
		} else {
			throw new Exception("Parameter $result_format is of invalid type".gettype($result_format)."!");
		}


		if(sizeof($search_array) > 0){

			$query_str .= " WHERE";

			$table_meta = self::load_table_meta($table);

			$is_first = true;
			foreach($search_array as $search_entry_key => $search_entry_value){
				$field_meta = self::get_field_meta($table_meta, $search_entry_key);
				$field_type = self::get_field_type($field_meta);

				if(gettype($search_entry_value) != $field_type){
					throw new Exception("Type mismatch! ".$field_type." required but ".gettype($search_entry_value)." given for field '".$field_meta["Field"]."'!");
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

	public static function generate_insert_query_string($table, $data){
		global $mysqli;

		$table_meta = self::load_table_meta($table);

		$query_str = "INSERT INTO ".$table." (".implode(", ", array_keys($data)).")";

		$query_str .= " VALUES (";

		$is_first = true;
		foreach($data as $field => $value){
			$field_meta = self::find_field($table_meta, $field);
			$field_type = self::get_field_type($field_meta);

			if(gettype($value) != $field_type){
				throw new Exception("Type mismatch! ".$field_type." required but ".gettype($value)." given for field '".$field_meta["Field"]."'!");
			}

			$is_first ? $is_first = false : $query_str .= ", ";

			if($field_type == "string")
				$query_str .= '"';

			$query_str .= $mysqli->real_escape_string($value);

			if($field_type == "string")
				$query_str .= '"';
		}

		$query_str .= ")";

		return $query_str;
	}

	public static function generate_delete_query_string($table, $search){
		global $mysqli;

		$table_meta = self::load_table_meta($table);

		$query_str = "DELETE FROM ".$table." WHERE";

		$is_first = true;
		foreach($search as $field => $value){
			$field_meta = self::get_field_meta($table_meta, $field);
			$field_type = self::get_field_type($field_meta);

			if(gettype($value) != $field_type){
				throw new Exception("Type mismatch! ".$field_type." required but ".gettype($value)." given for field '".$field_meta["Field"]."'!");
			}

			$is_first ? $is_first = false : $query_str .= " AND";

			$query_str .= " ".$field." = ";

			if($field_type == "string")
				$query_str .= '"';

			$query_str .= $mysqli->real_escape_string($value);

			if($field_type == "string")
				$query_str .= '"';

		}

		return $query_str;
	}

	public static function generate_update_query_string($table, $search, $update){
		global $mysqli;

		$table_meta = self::load_table_meta($table);

		$query_str = "UPDATE ".$table." SET ";

		$is_first = true;
		foreach($update as $field => $value){
			$field_meta = self::get_field_meta($table_meta, $field);
			$field_type = self::get_field_type($field_meta);

			if(gettype($value) != $field_type){
				throw new Exception("Type mismatch! ".$field_type." required but ".gettype($value)." given for field '".$field_meta["Field"]."'!");
			}

			$is_first ? $is_first = false : $query_str .= ", ";

			$query_str .= $mysqli->real_escape_string($field)." = ";

			if($field_type == "string")
				$query_str .= '"';

			$query_str .= $mysqli->real_escape_string($value);

			if($field_type == "string")
				$query_str .= '"';
		}

		$query_str .= " WHERE ";

		$is_first = true;
		foreach($search as $field => $value){
			$field_meta = self::get_field_meta($table_meta, $field);
			$field_type = self::get_field_type($field_meta);

			if(gettype($value) != $field_type){
				throw new Exception("Type mismatch! ".$field_type." required but ".gettype($value)." given for field '".$field_meta["Field"]."'!");
			}

			$is_first ? $is_first = false : $query_str .= " AND ";

			$query_str .= $mysqli->real_escape_string($field)." = ";

			if($field_type == "string")
				$query_str .= '"';

			$query_str .= $mysqli->real_escape_string($value);

			if($field_type == "string")
				$query_str .= '"';
		}

		return $query_str;
	}
}

?>