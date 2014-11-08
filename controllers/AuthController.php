<?php

require_once(abspath_lcl("/core/Controller.php"));

class AuthController extends Controller{
	public function login(){
		if(isset($_POST["email"]) && isset($_POST["password"])){
			$auth = $this->model("Auth");
			return json_encode($auth->login($_POST["email"], $_POST["password"]));
		} else {
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}
	}

	public function logout(){
		$auth = $this->model("Auth");
		$auth->logout();
		return json_encode(array());
	}

	public function register(){
		if(isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["lang"]) && isset($_POST["password"])){
			$auth = $this->model("Auth");
			$result_register = $auth->register($_POST["email"], $_POST["username"], $_POST["lang"], $_POST["password"]);
			if(isset($result_register["ERROR"])){
				return json_encode($result_register);
			} else {
				return json_encode($auth->login($_POST["email"], $_POST["password"]));
			}
		} else {
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}
	}
}

?>