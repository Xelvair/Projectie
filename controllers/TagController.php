<?php
require_once("../core/Controller.php");

class TagController extends Controller{
	function create(){
		if(!isset($_POST["tag_name"])){
			return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS"));
		}

		$auth = $this->model("Auth");
		$current_user = $auth->get_current_user();

		if($current_user == null){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		$tag = $this->model("Tag");

		return json_encode($tag->create_tag($_POST["tag_name"]));
	}
}
?>