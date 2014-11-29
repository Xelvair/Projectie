<?php
require_once("../core/Controller.php");

class DebugController extends Controller{
	function index(){
		$content = $this->view("Debug");

		$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
												"body" => $content, 
												"body_padding" => false));

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