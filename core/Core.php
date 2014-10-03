<?php

require_once("Logger.php");

class Core{
	function __construct($url){
		//Initialize logger
		$logger = new Logger("projectie.log", Logger::DEBUG);
		$logger->log(Logger::DEBUG, "Processing request from ".$_SERVER['REMOTE_ADDR']."");

		//Parse URL
		$parsed_url = self::parseUrl($url);

		//After we parsed the URL, load controller
		$controller_filepath = self::controllerFilepath($parsed_url["controller"]);
		if(file_exists($controller_filepath)){
			require_once($controller_filepath);
		} else {
			require_once(self::controllerFilepath("home"));
		}

		//Instantiate controller
		$controller = new $parsed_url["controller"];

		//Check if method exists and call if it does
		if(method_exists($controller, $parsed_url["function"])){
			call_user_func(array($controller, $parsed_url["function"]), $parsed_url["params"]);
		} else {
			echo "ERROR: Function '".$parsed_url["function"]."' does not exist on controller '".$parsed_url["controller"]."'!";
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