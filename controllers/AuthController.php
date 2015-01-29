<?php

require_once(abspath_lcl("/core/Controller.php"));

class AuthController extends Controller{
	public function login(){
		if(isset($_POST["email"]) && isset($_POST["password"])){
			$dbez = Core::model("DBEZ");
			$auth = Core::model("Auth", $dbez);
			return json_encode($auth->login($_POST["email"], $_POST["password"]));
		} else {
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}
	}

	public function logout(){
		$dbez = Core::model("DBEZ");
		$auth = Core::model("Auth", $dbez);
		$auth->logout();
		return json_encode(array());
	}

	public function register(){
		if(isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["lang"]) && isset($_POST["password"])){
			$dbez = Core::model("DBEZ");
			$auth = Core::model("Auth", $dbez);
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
		$dbez = Core::model("DBEZ");
		$auth = Core::model("Auth", $dbez);

		if(isset($data[0]) && !!$data[0]){
			return json_encode($auth->get_user($data[0]));
		} else {
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}
	}
	
	    //      user_id : always required, should be id of logged in user
        //      email : set when email change
        //  	old_password : old password when pw change
        //      new_password : new password when pw change
        //      lang : set lang when lang change
        public function set_user(){
                write_log(Logger::DEBUG, print_r($_POST, true));
				
				try{
					validate($_POST, ["user_id" => "int"], VALIDATE_CAST);
				} catch (ValidationException $e){
                        return json_encode(array("ERROR" => $e->getMessage()));
                }
				
				$_POST["user_id"] = (int)$_POST["user_id"];
 
                $dbez = Core::model("DBEZ");
                $auth = Core::model("Auth", $dbez);
 
                $user = $auth->get_current_user();
 
                if(!$user || ($user["user_id"] != $_POST["user_id"])){
                        return json_encode(array("ERROR" => "ERR_NO_RIGHTS"));
                }
 
                return json_encode($auth->set_user($_POST));
        }
}

?>