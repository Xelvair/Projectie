<?php

error_reporting(E_ALL);

session_start();

$CONFIG = array();

require_once("Debug.php");
require_once("Logger.php");
require_once("StringLocale.php");
require_once("Validate.php");

//Initialize logger
$logger = new Logger("projectie.log", Logger::DEBUG);
$locale = new StringLocale();
$mysqli = null;

require_once("Db.php");

function __autoload($classname_full){
	$classname_parts = explode("\\", $classname_full);

	$classname = end($classname_parts);
	$classname_folder = implode("/", array_slice($classname_parts, 0, sizeof($classname_parts) - 1));

	$class_path = abspath_lcl("/models/".$classname_folder."/".$classname.".php");

	if(!file_exists($class_path)){
		write_log(Logger::ERROR, "Failed to load class '".$classname_full."'! Path: ".$class_path);
	} else {
		require_once($class_path);
	}
}

function write_log($loglevel, $message){
	global $logger;
	$logger->log($loglevel, $message);
}

function abspath($path){
	return "http://".preg_replace('#/+#','/', $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/".$path);
}

function abspath_lcl($path){
	return preg_replace('#/+#','/', $_SERVER["DOCUMENT_ROOT"]."/".$path);
}

function cast($val, $type){
	switch($type){
		case "integer":
			return (integer)$val;
			break;
		case "string":
			return (string)$val;
			break;
		case "float":
			return (float)$val;
			break;
		case "boolean":
			return (boolean)$val;
			break;
		case "array":
			return (array)$val;
			break;
		case "object":
			return (object)$val;
			break;
		default:
			throw new UnexpectedValueException();
			break;
	}
}

class Core{
	function __construct($url){
		global $mysqli;
		global $locale;
		global $CONFIG;

		//Initiate RNG
		srand(time());

		write_log(Logger::DEBUG, "Processing request from ".$_SERVER['REMOTE_ADDR']);
		write_log(Logger::DEBUG, "Request info: ".$_SERVER['QUERY_STRING']);

		//Parse URL
		$parsed_url = self::parseUrl($url);

		//If the config & DB isn't initialized, forward to /install/
		if(!self::load_and_check_config()){
			$parsed_url = array("controller" => "install", "function" => "index", "params" => null);
		} else {
			$mysqli = new mysqli("127.0.0.1", "root", "", $CONFIG["db_name"]);

			if($mysqli->connect_errno){
				write_log(Logger::ERROR, "Failed to connect to database!");
			} else {
				$mysqli->set_charset("utf8");
			}
		}

		//After we parsed the URL, load controller
		//If we fail to load, change the request url to home
		$controller_filepath = self::controllerFilepath($parsed_url["controller"]);
		if(file_exists($controller_filepath)){
			require_once($controller_filepath);
		} else {
			require_once(self::controllerFilepath("home"));
			$parsed_url["controller"] = "Home";
			$parsed_url["function"] = "index";
			$parsed_url["params"] = null;
		}
		
		//Instantiate Controller
		$controller_name = $parsed_url["controller"]."Controller";

		if(class_exists($controller_name)){
			$controller = new $controller_name;
		} else {
			write_log(Logger::ERROR, "Failed to instantiate controller class'".$controller_name."'! Is the class named '".$controller_name."'?");
			return;
		}

		//Check if method exists and call if it does
		$function_name = $parsed_url["function"];
		if(!$function_name){
			$function_name = "index";
		}
		
		if(method_exists($controller, $function_name)){
			$controller_result = call_user_func(array($controller, $function_name), $parsed_url["params"]);

			if(isset($_GET["redirect"])){
				//If we have a redirect, don't output HTML, instead forward to new URL
				header("Location: ".abspath($_GET["redirect"]));
			} else {
				//Else, just output the generated HTML and call it a day
				echo $controller_result;
			}
		} else {
			write_log(Logger::ERROR, "Function '".$function_name."' doesn't exist on controller '".$controller_name."'!");
		}
	}

	private static function parseUrl($url){
		$result = array();
		$result["params"] = array();

		if($url != ""){
			$url = explode("/", $url);
			$result["controller"] = $url[0];
			if(sizeof($url) > 1){
				$result["function"] = $url[1];
				if(sizeof($url) > 2){
					for($i = 0; $i < sizeof($url) - 2; $i += 1){
						array_push($result["params"], $url[$i + 2]);
					}
				}
			} else {
				$result["function"] = "index";
			}
		} else {
			$result["controller"] = "home";
			$result["function"] = "index";
		}
		return $result;
	}

	private static function controllerFilepath($controller){
		return "../controllers/".$controller."Controller.php";
	}

	private static function load_and_check_config(){
		global $CONFIG;
		//Check if file exists
		if(!file_exists(abspath_lcl("projectie.cfg"))){
			return false;
		}
		$config_str = file_get_contents(abspath_lcl("projectie.cfg"));

		//Check if config file is valid JSON
		$config_obj = json_decode($config_str, true);
		if($config_obj == null){
			return false;
		}

		//Check if config file has required fields
		if(	!isset($config_obj["db_name"]) ||
				!isset($config_obj["db_checksum"]))
		{
			return false;
		}

		//Check if install.sql is in sync with db state
		if($config_obj["db_checksum"] != md5_file(abspath_lcl("/sql/install.sql"))){
			return false;
		}

		$CONFIG = $config_obj;
		return true;
	}

	public static function model($model){
		//Determine filepath
		$model = $model."Model";
		$model_filepath = self::modelFilepath($model);
		if(file_exists($model_filepath)){
			include_once($model_filepath);
		} else {
			write_log(Logger::ERROR, "Failed to load model file '".$model."'! Is the file named properly?");
			return null;
		}

		//Instantiate object
		if(class_exists($model)){
			$ref = new ReflectionClass($model);
			$params = func_get_args();
			array_shift($params);
				return $ref->newInstanceArgs($params);
		} else {
			write_log(Logger::ERROR, "Failed to instantiate model class'".$model."'! Is the class named '".$model."'?");
			return null;
		}

		return $model_obj;
	}

	public static function view($view, $data = array()){
		$view = $view."View";
		$view_filepath = self::viewFilepath($view);

		if(!file_exists($view_filepath)){
			write_log(Logger::ERROR, "Failed to load view file '".$view."'! Is the file named properly?");
			return null;
		}

		$_DATA = $data;

		ob_start();

		include($view_filepath);

		return ob_get_clean();
	}

	public static function view_batch($view, $data_batch){
		$content = "";
		foreach($data_batch as $data){
			$content .= Core::view($view, $data);
		}
		return $content;
	}

	private static function viewFilepath($view){
		return "../views/".$view.".php";
	}

	private static function modelFilepath($model){
		return "../models/".$model.".php";
	}
}

?>