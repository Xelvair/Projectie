<?php

abstract class ActiveRecord{

	const UPDATE = 0;
	const INSERT = 1;

	public static final function getTableMeta(){
		return TableMeta::load(self::getTableName());
	}

	public static function getTableName(){
		return get_called_class();
	}

	public static final function get($filter){
		switch(gettype($filter)){
			case "integer":
				return self::getById($filter);
				break;
			case "array":
				return self::getByArray($filter);
				break;
			default:
				throw new UnexpectedValueException("ActiveRecord::get(): Parameter has invalid type!");
		}
	}

	public static final function store($activerecord){
		$store_operation_info = self::getStoreOperationInfo($activerecord);

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

			return !!DBEZ::update($activerecord->getTableName(), $update_array);
			
		} else {
			throw new RuntimeError("Something very bad happened.");
		}
	}

	private static final function getStoreOperationInfo($activerecord){
		$store_operation_info = [
			"operation_type" => self::INSERT,
			"fields" => array()
		];

		$table_meta = TableMeta::load(self::getTableName());

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
				}

				array_push($store_operation_info["fields"], $field_name);
			}
		}

		return $store_operation_info;
	}

	private static final function getById($id){
		$row = DBEZ::find(self::getTableName(), $id, "*");

		$subclass_name = get_called_class();
		$obj = new $subclass_name();

		foreach($row as $field_name => $field_val){
			$obj->$field_name = $field_val;
		}

		return $obj;
	}

	private static final function getByArray($filter){
		$result = DBEZ::find(self::getTableName(), $filter, "*");

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
}

?>