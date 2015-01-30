<?php

final class FieldMeta{

	private $mName;
	private $mType;
	private $mDefaultValue;
	private $mIsNullable;

	function __construct($name, $type, $default_value, $nullable){
		$this->mName = $name;
		$this->mType = $type;
		$this->mDefaultValue = $default_value;
		$this->mIsNullable = $nullable;
	}

	public function getName(){
		return $this->mName;
	}

	public function getType(){
		return $this->mType;
	}

	public function getDefaultValue(){
		return $this->mDefaultValue;
	}

	public function isNullable(){
		return $this->mIsNullable;
	}
}

?>