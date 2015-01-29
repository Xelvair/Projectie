<?php

class ValidationException extends Exception{
	private $trace = array();
	private $format = null;

	public function __construct($format, $trace){
		$this->format = $format;
		$this->trace = array($trace);
		$this->message = "Failed validation! '".$this->format."' for field trace: ".implode("->", $this->trace);
	}

	public function add_trace($trace){
		array_unshift($this->trace, $trace);
		$this->message = "Failed validation! '".$this->format."' for field trace: ".implode("->", $this->trace);
	}
}

define("VALIDATE_STRICT", 1 << 0);
define("VALIDATE_CAST", 1 << 1);
define("VALIDATE_THROW", 1 << 2);
define("VALIDATE_NOTHROW", 1 << 3);


function validate_format(&$var, $format, $action = VALIDATE_STRICT){
	if(preg_match("/^min\(([0-9]+)\)$/", $format, $matches)){
		switch(gettype($var)){
			case "string":
				return strlen($var) >= $matches[1];
				break;
			case "integer":
				return $var >= $matches[1];
				break;
			case "float":
				return $var >= $matches[1];
				break;
			case "array":
				return sizeof($var) >= $matches[1];
				break;
			default:
			 return false;
		}
	} else if(preg_match("/^max\(([0-9]+)\)$/", $format, $matches)){
		switch(gettype($var)){
			case "string":
				return strlen($var) <= $matches[1];
				break;
			case "integer":
				return $var <= $matches[1];
				break;
			case "float":
				return $var <= $matches[1];
				break;
			case "array":
				return sizeof($var) <= $matches[1];
				break;
			default:
			 return false;
		}
	} else if(preg_match("/^int$/", $format)){
		if(gettype($var) === "integer"){
			return $var;
		} else if(gettype($var) === "array"){
			return false;
		} else {
			switch($action){
				case VALIDATE_CAST:
					$cast = (int)$var;
					if(!empty($cast)) $var = $cast;
					return true;
				case VALIDATE_STRICT:
					return false;
			}
		}
	} else if(preg_match("/^bool$/", $format)){
		if(gettype($var) === "boolean"){
			return $var;
		} else if(gettype($var) === "array"){
			return false;
		} else {
			switch($action){
				case VALIDATE_CAST:
					$cast = (bool)$var;
					if(!empty($cast)) $var = $cast;
					return true;
				case VALIDATE_STRICT:
					return false;
			}
		}
	} else if(preg_match("/^string$/", $format)){
		if(gettype($var) === "string"){
			return $var;
		} else if(gettype($var) === "array"){
			return false;
		} else {
			switch($action){
				case VALIDATE_CAST:
					$cast = (string)$var;
					if(!empty($cast)) $var = $cast;
					return true;
				case VALIDATE_STRICT:
					return false;
			}
		}
	} else if(preg_match("/^float$/", $format)){
		if(gettype($var) === "float"){
			return $var;
		} else if(gettype($var) === "array"){
			return false;
		} else {
			switch($action){
				case VALIDATE_CAST:
					$cast = (float)$var;
					if(!empty($cast)) $var = $cast;
					return true;
				case VALIDATE_STRICT:
					return false;
			}
		}
	} else if(preg_match("/^array$/", $format)){
		if(gettype($var) === "array"){
			return $var;
		} else if(gettype($var) === "object"){
			$var = (array)$var;
			return true;
		} else {
			return false;
		}
	} else if(preg_match("/^email$/", $format)){
		if(gettype($var) != "string"){
			return false;
		}
		return !!preg_match("/^[a-zA-Z0-9-.]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,4}$/", $var);
	} else {
		throw new InvalidArgumentException($format);
	}
}

function validate_format_array(&$var, $format_array, $action = VALIDATE_STRICT){
	$formats = explode("|", $format_array);
	foreach($formats as $format){
		if(!validate_format($var, $format, $action)){
			return false;
		}
	}
	return true;
}

function validate($arr, $format_arr, $action = VALIDATE_NOTHROW){
	foreach($format_arr as $field => $format){
		if(!isset($arr[$field])){
			if($action & VALIDATE_THROW){
				throw new ValidationException("undefined", $field);
			} else if($action & VALIDATE_NOTHROW){
				return false;
			}
		}
		if(gettype($format) == "array"){
			try{
				if(!validate($arr[$field], $format, $action)){
					return false;
				}
			} catch (ValidationException $e){
				$e->add_trace($field);
				throw $e;
			}
		} else if(!validate_format_array($arr[$field], $format)){
			if($action & VALIDATE_THROW){
				throw new ValidationException($format, $field);
			} else if($action & VALIDATE_NOTHROW){
				return false;
			}
		}
	}

	return true;
}

?>