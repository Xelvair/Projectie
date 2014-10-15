<?php

class UserSession{
	function __construct(){
		if(isset($_SESSION["login_user_id"])){
			//retrieve user information from db
		}
	}

	public function register($email, $username, $password /*blahblah*/){ 
		global $mysqli;

		//Check if account name or email already exists
		$stmt_check_existence = $mysqli->prepare("SELECT user_id FROM user WHERE email = ? OR username = ?");
		$stmt_check_existence->bind_param("ss", $email, $username);
		$stmt_check_existence->execute();
		if($stmt_check_existence->get_result()->num_rows > 0){
			write_log(Logger::DEBUG, "Tried to register already existing account '".$username."<".$email.">'!");
			return false;
		}

		$password_salt = substr(md5(time()), 0, 8);

		$password_hash = md5($password.$password_salt);

		$stmt = $mysqli->prepare("INSERT INTO user (email, username, password_salt, password_hash) VALUES(?, ?, ?, ?)");
		$stmt->bind_param("ssss", $email, $username, $password_salt, $password_hash);
		if($stmt->execute()){
			write_log(Logger::DEBUG, "Registered account '".$username."'!");
			return true;
		} else {
			write_log(Logger::ERROR, "Failed to register account!");
			return false;
		}
	}

	public function login($email, $password){
		global $mysqli;

		//Load user from database
		$stmt = $mysqli->prepare("SELECT * FROM user WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		
		//Move user info into array
		$userinfo = $stmt->get_result()->fetch_assoc();
		
		//Check the password
		$password_hash = md5($password.$userinfo["password_salt"]);
		if($password_hash != $userinfo["password_hash"]){
			//If login failed, unset all values and exit
			$this->logout();
			write_log(Logger::DEBUG, "Failed login: incorrect password.");
			return false;
		}

		//If login succeeded, write to the session and set values
		$_SESSION["login_user_id"] = $userinfo["user_id"];
		$user_id = $userinfo["user_id"];
		$email = $userinfo["email"];
		$username = $userinfo["username"];
		write_log(Logger::DEBUG, "User '".$userinfo["username"]."' logged in.");
	}

	public function logout(){
		if(isset($_SESSION["login_user_id"])){
			unset($_SESSION["login_user_id"]);
		}
		$user_id = null;
		$email = null;
		$username = null;
		write_log(Logger::DEBUG, "User '".$username."' logged out.");
	}

	private function isLoggedIn(){
		return isset($_SESSION["login_user_id"]);
	}

	public $user_id = null;
	public $username = null;
	public $email	= null;
}

?>