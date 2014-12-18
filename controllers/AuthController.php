<?php

require_once(abspath_lcl("/core/Controller.php"));

class AuthController extends Controller{
	public function login(){
		if(isset($_POST["email"]) && isset($_POST["password"])){
			$dbez = $this->model("DBEZ");
			$auth = $this->model("Auth", $dbez);
			return json_encode($auth->login($_POST["email"], $_POST["password"]));
		} else {
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}
	}

	public function logout(){
		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$auth->logout();
		return json_encode(array());
	}

	public function register(){
		if(isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["lang"]) && isset($_POST["password"])){
			$dbez = $this->model("DBEZ");
			$auth = $this->model("Auth", $dbez);
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
	
	//data[0] : id of the requested user
	public function get_user($data){
		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);

		if(isset($data[0]) && !empty($data[0])){
			return json_encode($auth->get_user($data[0]));
		} else {
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}
	}
}

?>