<?php
require_once("../core/Controller.php");

class DebugController extends Controller{
	function index(){
		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$user = $auth->get_current_user();

		$content = $this->view("Debug");

		$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
												"body" => $content, 
												"body_padding" => false,
												"current_user" => $user));

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