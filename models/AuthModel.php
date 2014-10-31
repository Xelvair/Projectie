<?php

require_once("../core/Model.php");
require_once("../models/UserModel.php");

class AuthModel implements Model{
	function __construct(){
		global $mysqli;
		if(isset($_SESSION["login_user_id"])){
			write_log(Logger::DEBUG, $_SESSION["login_user_id"]);
			$stmt_load_user = $mysqli->prepare("SELECT user_id, create_time, email, username from user WHERE user_id = ?");
			$stmt_load_user->bind_param("s", $_SESSION["login_user_id"]);
			$stmt_load_user->execute();
			$stmt_load_user->bind_result($user_id, $create_time, $email, $username);

			if($stmt_load_user->fetch()){
				$this->loggedInUser = new UserModel($user_id, $create_time, $username, $email);
				write_log(Logger::DEBUG, "Auth constructed with logged in user '".$username."'!");
			} else {
				write_log(Logger::WARNING, "Logged out user ".$_SESSION["login_user_id"]." - id doesn't exist in database!");
				unset($_SESSION["login_user_id"]);
			}
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

		$stmt = $mysqli->prepare("INSERT INTO user (create_time, email, username, password_salt, password_hash) VALUES(?, ?, ?, ?, ?)");
		$stmt->bind_param("issss", time(), $email, $username, $password_salt, $password_hash);
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
		$stmt = $mysqli->prepare("SELECT user_id, create_time, email, username, password_hash, password_salt FROM user WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->bind_result($res_user_id, $res_create_time, $res_email, $res_username, $res_password_hash, $res_password_salt);
		if(!$stmt->fetch()){
			write_log(Logger::WARNING, "Failed to login, email '".$email."' not found!");
			return false;
		}
		
		//Check the password
		$password_hash = md5($password.$res_password_salt);
		if($password_hash != $res_password_hash){
			//If login failed, unset all values and exit
			$this->logout();
			write_log(Logger::DEBUG, "Failed login: incorrect password.");
			return false;
		} else {
			//If login succeeded, write to the session and set values
			$_SESSION["login_user_id"] = $res_user_id;
			$loggedInUser = new UserModel($res_user_id, $res_create_time, $res_username, $res_email);
			write_log(Logger::DEBUG, "User '".$res_username."' logged in.");

			return true;
		}
	}

	public function logout(){
		if(isset($_SESSION["login_user_id"])){
			unset($_SESSION["login_user_id"]);
		}
		$this->loggedInUser = null;
	}

	public function getLoggedInUser(){
		return $this->loggedInUser;
	}

	private $loggedInUser = null;
}

?>