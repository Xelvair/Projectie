<?php

final class TableMeta{

	/* PUBLIC STATIC */
	const CONFIG_PATH = "/table_meta.cfg";

	public static function load($table_name){
		return self::loadFromDatabase($table_name);
	}

	/* PRIVATE STATIC */
	private static function loadFromDatabase($table_name){
		global $mysqli;

		$result_rows = $mysqli->query("SHOW COLUMNS FROM ".$table_name);
		if($result_rows === false){
			throw new UnexpectedValueException("Couldn't load table meta for table '".$table_name."'!");
		}

		$fields = $result_rows->fetch_all(MYSQLI_ASSOC);

		$field_metas = array();
		$primary_key = "";

		foreach($fields as $field){
			$field_name = $field["Field"];
			$field_type = DBEZ::get_field_type($field);
			$field_is_nullable = ($field["Null"] === "YES");
			$field_default_value = $field["Default"] === null ? null : cast($field["Default"], $field_type);

			array_push($field_metas, new FieldMeta($field_name, $field_type, $field_default_value, $field_is_nullable));

			if($field["Key"] === "PRI"){
				$primary_key = $field_name;
			}
		}

		return new TableMeta($table_name, $field_metas, $primary_key);
	}
 
	/* PRIVATE */
	private $mTableName;
	private $mFields;
	private $mPrimaryKey;

	/* PUBLIC */
	function __construct($table_name, $fields, $primary_key){
		$this->mTableName = $table_name;
		$this->mFields = $fields;
		$this->mPrimaryKey = $primary_key;
	}

	public function getFields(){
		return $this->mFields;
	}

	public function getTableName(){
		return $this->mTableName;
	}

	public function getPrimaryKey(){
		return $this->mPrimaryKey;
	}



}

?>