<?php

require_once("../core/Model.php");
require_once("../models/UserModel.php");

class AuthModel implements Model{
	function __construct(){
		global $mysqli;
		if(isset($_SESSION["login_user_id"])){
			write_log(Logger::DEBUG, $_SESSION["login_user_id"]);
			$stmt_load_user = $mysqli->prepare("SELECT user_id, create_time, email, username, lang from user WHERE user_id = ? AND active = true");
			$stmt_load_user->bind_param("s", $_SESSION["login_user_id"]);
			$stmt_load_user->execute();
			$stmt_load_user->bind_result($user_id, $create_time, $email, $username, $lang);

			if($stmt_load_user->fetch()){
				$this->loggedInUser = new UserModel($user_id, $create_time, $username, $email, $lang);

				$stmt_load_user->close();

				write_log(Logger::DEBUG, "Auth constructed with logged in user '".$username."'!");
			} else {
				$stmt_load_user->close();

				write_log(Logger::WARNING, "Logged out user ".$_SESSION["login_user_id"]." - id doesn't exist in database!");
				unset($_SESSION["login_user_id"]);
			}
		}
	}

	public function register($email, $username, $lang, $password /*blahblah*/){ 
		global $mysqli;

		//All kinds of checks on the parameters
		if(!preg_match("/^[a-zA-Z0-9-.]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,4}$/", $email)){
			return array("ERROR" => "ERR_INVALID_EMAIL");
		}

		//Check pw length
		if(strlen($password) < 7){
			return array("ERROR" => "ERR_INVALID_PASSWORD");
		}

		//Check if username has no special chars
		if(!preg_match("/^[a-zA-Z0-9.-]+$/", $username)){
			return array("ERROR" => "ERR_INVALID_USERNAME");
		}

		//Check if file for chosen locale exists
		if(!file_exists(abspath_lcl("/locale/".$lang.".locale"))){
			return array("ERROR" => "ERR_INVALID_LANG");
		}

		//Check if email already exists
		if(self::email_exists($email)){
			write_log(Logger::DEBUG, "Tried to register already existing email <".$email.">!");
			return array("ERROR" => "ERR_EMAIL_IN_USE");
		}

		//Check if username already exists
		if(self::username_exists($username)){
			write_log(Logger::DEBUG, "Tried to register already existing username '".$$username.">!");
			return array("ERROR" => "ERR_USERNAME_IN_USE");
		}

		$password_salt = substr(md5(time()), 0, 8);

		$password_hash = md5($password.$password_salt);

		$stmt = $mysqli->prepare("INSERT INTO user (create_time, email, username, lang, password_salt, password_hash, active) VALUES(?, ?, ?, ?, ?, ?, true)");
		$stmt->bind_param("isssss", time(), $email, $username, $lang, $password_salt, $password_hash);
		if($stmt->execute()){
			write_log(Logger::DEBUG, "Registered account '".$username."'!");
			return array();
		} else {
			write_log(Logger::ERROR, "Failed to register account!");
			return array("ERROR" => "ERR_DB_INSERT_FAILED");
		}
	}

	public function login($email, $password){
		global $mysqli;

		//Load user from database
		$stmt = $mysqli->prepare("SELECT user_id, create_time, email, username, lang, password_hash, password_salt FROM user WHERE email = ? AND active = true");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->bind_result($res_user_id, $res_create_time, $res_email, $res_username, $res_lang, $res_password_hash, $res_password_salt);
		if(!$stmt->fetch()){
			write_log(Logger::WARNING, "Failed to login, email '".$email."' not found!");
			return array("ERROR" => "ERR_USER_NOT_FOUND");
		}
		
		//Check the password
		$password_hash = md5($password.$res_password_salt);
		if($password_hash != $res_password_hash){
			//If login failed, unset all values and exit
			$this->logout();

			write_log(Logger::DEBUG, "Failed login: incorrect password.");

			$stmt->close();
			return array("ERROR" => "ERR_INCORRECT_PASSWORD");
		} else {
			//If login succeeded, write to the session and set values
			$_SESSION["login_user_id"] = $res_user_id;
			$loggedInUser = new UserModel($res_user_id, $res_create_time, $res_username, $res_email, $res_lang);

			write_log(Logger::DEBUG, "User '".$res_username."' logged in.");

			$stmt->close();
			return array();
		}
	}

	public function logout(){
		if(isset($_SESSION["login_user_id"])){
			unset($_SESSION["login_user_id"]);
		}
		$this->loggedInUser = null;

		return array();
	}

	public function email_exists($email){
		global $mysqli;

		$stmt = $mysqli->prepare("SELECT user_id FROM user WHERE email = ? AND active = true");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$mail_exists = $stmt->fetch() ? true : false;
		$stmt->close();
		return $mail_exists;
	}

	public function username_exists($username){
		global $mysqli;

		$stmt = $mysqli->prepare("SELECT user_id FROM user WHERE username = ? AND active = true");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$username_exists = $stmt->fetch() ? true : false;
		$stmt->close();
		return $username_exists;
	}

	public function get_current_user(){
		return $this->loggedInUser;
	}

	public function deactivate($requester, $user_id){
		if($requester == $user_id){
			$stmt = $mysqli->prepare("UPDATE user SET active = false WHERE user_id = ?");
			$stmt->bind_param("i", $user_id);
			$stmt->execute();
			$stmt->close();
		}
	}

	private $loggedInUser = null;
}

?>