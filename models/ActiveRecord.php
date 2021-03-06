<?php

abstract class ActiveRecord{

	const UPDATE = 0;
	const INSERT = 1;


	/* PUBLIC STATIC */
	public static final function getTableMeta(){
		return TableMeta::load(static::getTableName());
	}

	public static function getTableName(){
		return get_called_class();
	}

	public static final function get($filter){
		switch(gettype($filter)){
			case "integer":
				return static::getById($filter);
				break;
			case "array":
				return static::getByArray($filter);
				break;
			default:
				throw new UnexpectedValueException("ActiveRecord::get(): Parameter has invalid type!");
		}
	}

	public static final function store($content){
		switch(gettype($content)){
			case "object":
				static::storeSingle($content);
				break;
			case "array":
				static::storeArray($content);
				break;
			default:
				throw new InvalidArgumentError("Invalid argument passed to ActiveRecord::store()! either array or object!");
				break;
		}
	}

	public final function getRelative($class_name, $field_name){
		//TODO: Cache result so that we don't have to do a db access everytime we access a relative
		if(!isset($this->$field_name)){
			$primary_key_field = static::getTableMeta()->getPrimaryKey();

			if(!isset($this->$primary_key_field)){
				throw new RuntimeException("Unable to retrieve attribute '".$field_name."', object has neither '".$field_name."'' nor '".$primary_key_field."'!");
			}

			$db_this = static::get($this->$primary_key_field);

			$this->$field_name = $db_this->$field_name;
		}

		return $class_name::get($this->$field_name);
	}

	/* PRIVATE STATIC */
	private static final function storeArray($activerecord_array){
		foreach($activerecord_array as $activerecord){
			static::storeSingle($activerecord);
		}
	}

	private static final function storeSingle($activerecord){
		$store_operation_info = static::getStoreOperationInfo($activerecord);

		$fields = $store_operation_info["fields"];
		$operation_type = $store_operation_info["operation_type"];

		if($store_operation_info["operation_type"] == self::INSERT){

			$insert_array = array();
			foreach($fields as $field){
				$insert_array[$field] = $activerecord->$field;
			}

			return !!DBEZ::insert($activerecord->getTableName(), $insert_array);

		} else if ($store_operation_info["operation_type"] == self::UPDATE){

			$update_array = array();
			foreach($fields as $field){
				$update_array[$field] = $activerecord->$field;
			}

			$index_field = $store_operation_info["index_field"];

			return !!DBEZ::update($activerecord->getTableName(), $activerecord->$index_field, $update_array);
			
		} else {
			throw new RuntimeError("Something very bad happened.");
		}
	}

	private static final function getStoreOperationInfo($activerecord){
		$store_operation_info = [
			"operation_type" => self::INSERT,
			"fields" => array(),
			"index_field" => null
		];

		$table_meta = static::getTableMeta();

		$field_metas = $table_meta->getFields();

		foreach($field_metas as $field_meta){
			$field_name              = $field_meta->getName();
			$field_is_nullable       = $field_meta->isNullable();
			$field_type              = $field_meta->getType();
			$field_default_value     = $field_meta->getDefaultValue();
			$field_has_default_value = $field_default_value !== null; 
			$field_is_primary_key    = ($table_meta->getPrimaryKey() == $field_name);

			if(!isset($activerecord->$field_name)){
				if(!$field_is_nullable && !$field_is_primary_key && !$field_has_default_value){
					throw new RuntimeException("ActiveRecord::validate(): tried to store with unset required field '".$field_name."'!");
				}
			} else {
				if(gettype($activerecord->$field_name) !== $field_type){
					throw new RuntimeException("ActiveRecord::validate(): tried to store with invalid type ".gettype($activerecord->$field_name)." for field '".$field_name."' (".$field_type.")!");
				}

				//If an ID is set, we update
				if($field_is_primary_key){
					$store_operation_info["operation_type"] = self::UPDATE;
					$store_operation_info["index_field"] = $field_name;
				}

				array_push($store_operation_info["fields"], $field_name);
			}
		}

		return $store_operation_info;
	}

	//returns a single instance of the called class which extends ActiveRecord
	private static final function getById($id){
		$row = DBEZ::find(static::getTableName(), $id, "*");

		$subclass_name = get_called_class();
		$obj = new $subclass_name($id);

		foreach($row as $field_name => $field_val){
			$obj->$field_name = $field_val;
		}

		return $obj;
	}

	//returns an array of the called class which extends ActiveRecords
	private static final function getByArray($filter){
		$result = DBEZ::find(static::getTableName(), $filter, "*");

		$subclass_name = get_called_class();

		$obj_array = array();

		foreach($result as $row){
			$obj = new $subclass_name();

			foreach($row as $field_name => $field_val){
				$obj->$field_name = $field_val;
			}

			array_push($obj_array, $obj);
		}

		return $obj_array;
	}

	/* PUBLIC */
	public function __construct($idx){
		$table_meta = static::getTableMeta();
		$primary_key = $table_meta->getPrimaryKey();

		$this->$primary_key = $idx;
	}
}

?>