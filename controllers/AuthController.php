<?php

require_once(abspath_lcl("/core/Controller.php"));

class AuthController extends Controller{
	public function login(){
		$valid = validate($_POST, [
			"email" => "string|email",
			"password" => "string"
		]);

		if(!$valid){
			return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS"));
		}

		$auth = Core::model("Auth");
		return json_encode($auth->login($_POST["email"], $_POST["password"]));
	}

	public function logout(){
		$auth = Core::model("Auth");
		$auth->logout();
		return json_encode(array());
	}

	public function register(){
		$valid = validate($_POST,[
			"email" => "email",
			"username" => "string",
			"lang" => "string",
			"password" => "string"
		]);

		if(!$valid){
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}

		$auth = Core::model("Auth");
		$result_register = $auth->register($_POST["email"], $_POST["username"], $_POST["lang"], $_POST["password"]);
		if(isset($result_register["ERROR"])){
			return json_encode($result_register);
		} else {
			return json_encode($auth->login($_POST["email"], $_POST["password"]));
		}

	}
	
	//data[0] : id of the requested user
	public function get_user($data){
		$auth = Core::model("Auth");

		if(isset($data[0]) && !!$data[0]){
			return json_encode($auth->get_user($data[0]));
		} else {
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}
	}

	//	user_id : always required, should be id of logged in user
	//	email : set when email change
	//  old_password : old password when pw change
	//	new_password : new password when pw change
	//	lang : set lang when lang change
	public function set_user(){
		$auth = Core::model("Auth");

		$user = $auth->get_current_user();

		if(!validate($_POST, ["user_id" => "int"], VALIDATE_CAST)){
			return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS"));
		}	

		if(!$user || ($user["user_id"] != $_POST["user_id"])){
			return json_encode(array("ERROR" => "ERR_NO_RIGHTS"));
		}

		if($_FILES["profile_picture"]["error"] == 0){
			try{
				$picture = Picture::storeFromPost($_FILES["profile_picture"], (int)$user["user_id"]);

				$_POST["picture_id"] = $picture->picture_id;
			} catch (Exception $e){
				return json_encode(array("ERROR" => $e->getMessage()));
			}
		}

		return json_encode($auth->set_user($_POST));
	}

	# $_POST["tag_id"] || $_POST["tag_name"]
	public function tag($data){
		$exists_and_filled_out = function(&$var){
			return (isset($var) && !empty($var));
		};

		if
		(
			!$exists_and_filled_out($data[0]) ||
			!(
				$exists_and_filled_out($_POST["tag_id"]) ||
				$exists_and_filled_out($_POST["tag_name"])
			)
		){
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}

		if($exists_and_filled_out($_POST["tag_id"])){
			if(!filter_var($_POST["tag_id"], FILTER_VALIDATE_INT)){
				return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS")); 
			}
		}

		$auth = Core::model("Auth");
		$tag = Core::model("Tag");

		$current_user = $auth->get_current_user();

		if(!$current_user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		$user_id = (integer)$data[0];

		$current_user_id = $auth->get_current_user()["id"];

		return json_encode($auth->tag($tag, $user_id, (integer)$_POST["tag_id"]));
	}

	public function untag($data){
		if(sizeof($data) <= 0){
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}

		if(!isset($_POST["tag_id"])){
			return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS"));
		}

		$user_id = (int)$data[0];
		$tag_id = (int)$_POST["tag_id"];

		$auth = Core::model("Auth");
		$tag = Core::model("Tag");

		$current_user = $auth->get_current_user();

		return json_encode($auth->untag($tag, $user_id, $tag_id));
	}

	public function get_tags($data){
		if(sizeof($data) <= 0){
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}

		$auth = Core::model("Auth");
		
		return json_encode($auth->get_tags((int)$data[0]));
	}
}

?>