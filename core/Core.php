<?php

session_start();

$CONFIG = json_decode(file_get_contents(abspath("projectie.cfg")), true);

require_once("Debug.php");
require_once("Logger.php");
require_once("Locale.php");

//Initialize logger
$logger = new Logger("projectie.log", Logger::DEBUG);
$locale = new Locale();
$mysqli = new mysqli("localhost", "root", "", $CONFIG["db_name"]);

if($mysqli->connect_errno){
	write_log(Logger::ERROR, "Failed to connect to database!");
}

require_once("Db.php");

function write_log($loglevel, $message){
		global $logger;
		$logger->log($loglevel, $message);
}

function abspath($path){
	return "http://".preg_replace('#/+#','/', $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/".$path);
}

class Core{
	function __construct($url){
		global $mysqli;
		global $locale;

		write_log(Logger::DEBUG, "Processing request from ".$_SERVER['REMOTE_ADDR']);
		write_log(Logger::DEBUG, "Request info: ".$_SERVER['QUERY_STRING']);

		//Parse URL
		$parsed_url = self::parseUrl($url);

		//After we parsed the URL, load controller
		//If we fail to load, change the request url to home
		$controller_filepath = self::controllerFilepath($parsed_url["controller"]);
		if(file_exists($controller_filepath)){
			require_once($controller_filepath);
		} else {
			require_once(self::controllerFilepath("home"));
			$parsed_url["controller"] = "home";
			$parsed_url["function"] = "index";
			$parsed_url["params"] = null;
		}

		//Instantiate controller
		$controller_name = $parsed_url["controller"];
		if(class_exists($controller_name)){
			$controller = new $controller_name;
		} else {
			write_log(Logger::ERROR, "Failed to instantiate controller class'".$controller_name."'! Is the class named '".$controller_name."'?");
			return;
		}

		//Check if method exists and call if it does
		$function_name = $parsed_url["function"];
		if(method_exists($controller, $function_name)){
			echo call_user_func(array($controller, $function_name), $parsed_url["params"]);
		} else {
			write_log(Logger::ERROR, "Function '".$function_name."' doesn't exist on controller '".$controller_name."'!");
		}
	}

	private function parseUrl($url){
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

	private function controllerFilepath($controller){
		return "../controllers/".$controller.".php";
	}
}

?>