<?php
require_once("../core/Controller.php");

class DebugController extends Controller{
	function index(){
		$auth = Core::model("Auth");
		$user = $auth->get_current_user();

		$content = Core::view("Debug");

		$html = Core::view("HtmlBase", array(	
			"title" => "Projectie - Driving Development", 
			"body" => $content, 
			"body_padding" => false,
			"current_user" => $user,
			"dark" => true
		));

		return $html;
	}

	function debug(){
		$result = eval($_POST["code"]);

		if(is_array($result)){
			return print_r($result, true);
		} else {
			return var_export($result);
		}
	}
}
?>