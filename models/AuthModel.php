<?php

require_once("../core/Model.php");

class AuthModel implements Model{
	private $loggedInUser;

	function __construct(){
		global $mysqli;
		if(isset($_SESSION["login_user_id"])){

			$user = self::get_user($_SESSION["login_user_id"]);

			if(isset($user["ERROR"])){
				write_log(Logger::WARNING, "Logged out user ".$_SESSION["login_user_id"]." - id doesn't exist in database!");
				unset($_SESSION["login_user_id"]);
				return;
			}

			$this->loggedInUser = $user;

			write_log(Logger::DEBUG, "Auth constructed with logged in user #".$_SESSION["login_user_id"]."!");
		} else {
			write_log(Logger::DEBUG, "Auth not fully constructed - guest visit.");
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

		$stmt = $mysqli->prepare("INSERT INTO user (create_time, email, username, lang, is_admin, password_salt, password_hash, active) VALUES(?, ?, ?, ?, false, ?, ?, true)");
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
		$stmt = $mysqli->prepare("SELECT user_id, password_hash, password_salt FROM user WHERE email = ? AND active = true");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($res_user_id, $res_password_hash, $res_password_salt);
		if(!$stmt->fetch()){
			$stmt->close();
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

			$stmt->close();
			return array("ERROR" => "ERR_INCORRECT_PASSWORD");
		} else {
			//If login succeeded, write to the session and set values
			$_SESSION["login_user_id"] = $res_user_id;
			$loggedInUser = self::get_user($res_user_id);

			write_log(Logger::DEBUG, "User #".$res_user_id." logged in.");

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

	public function get_created_projects($user_id){
		global $mysqli;

		$query_get_projects = $mysqli->prepare("SELECT project_id, create_time, title, subtitle FROM project WHERE creator_id = ? and active = true");
		$query_get_projects->bind_param("i", $user_id);
		$query_get_projects->execute();

		$result = $query_get_projects->get_result();

		$created_projects = array();

		// currently, the result array looks like this:
		// [n] -> [project_id, create_time, title, ...]
		// we want to change it to such a format:
		// [project_id] -> [create_time, title, ...]
		// so we extract the id from the array, and rebuild a new one with the project_id as index
		while($row = $result->fetch_assoc()){
			$id = $row["project_id"];
			unset($row["project_id"]);
			$created_projects[$id] = array_values($row);
		}

		$query_get_projects->close();

		return $created_projects;
	}

	public function get_user_participations($user_id){
		global $mysqli;

		$query_get_projects = $mysqli->prepare("
			SELECT 
				p.project_id AS project_id, 
				p.title AS title, 
				p.subtitle AS subtitle, 
				p.create_time AS create_time 
			FROM project AS p 
			LEFT OUTER JOIN project_participation AS pp 
				ON p.project_id = pp.project_id 
			WHERE pp.user_id = ?");
		$query_get_projects->bind_param("i", $user_id);
		$query_get_projects->execute();

		$result = $query_get_projects->get_result();

		$user_participations = array();

		// currently, the result array looks like this:
		// [n] -> [project_id, create_time, title, ...]
		// we want to change it to such a format:
		// [project_id] -> [create_time, title, ...]
		// so we extract the id from the array, and rebuild a new one with the project_id as index
		while($row = $result->fetch_assoc()){
			$id = $row["project_id"];
			unset($row["project_id"]);
			$user_participations[$id] = array_values($row);
		}

		$query_get_projects->close();

		return $user_participations;
	}	

	public function get_chat_participations($user_id){
		global $mysqli;

		$query_get_chat_participations = $mysqli->prepare("
			SELECT chat_participation_id, chat_id
			FROM chat_participation
			WHERE participant_id = ?
		");

		$query_get_chat_participations->bind_param("i", $user_id);
		$query_get_chat_participations->execute();

		$result = $query_get_chat_participations->get_result();

		$chat_participations = array();

		while($row = $result->fetch_assoc()){
			$id = $row["chat_participation_id"];
			unset($row["chat_participation_id"]);
			$chat_participations[$id] = $row;
		}

		return $chat_participations;
	}

	public function get_user($user_id){
		global $mysqli;
		$stmt = $mysqli->prepare("SELECT user_id, create_time, username, email, lang, is_admin FROM user WHERE user_id = ?");
		$stmt->bind_param("i", $user_id);
		$stmt->execute();
		$stmt->store_result();

		$stmt->bind_result($res_user_id, $res_create_time, $res_username, $res_email, $res_lang, $res_is_admin);

		if(!$stmt->fetch()){
			write_log(Logger::WARNING, "User #".$user_id." doesn't exist!");
			return array("ERROR" => "ERR_USER_NOT_FOUND");
		}

		$user_obj = array(
			"id" => $res_user_id,
			"create_time" => $res_create_time,
			"username" => $res_username,
			"email" => $res_email,
			"lang" => $res_lang,
			"is_admin" => $res_is_admin,
			"created_projects" => array(),
			"project_participations" => array(),
			"chat_participations" => array()
		);

		$stmt->close();

		$user_obj["created_projects"] = self::get_created_projects($user_id);
		$user_obj["project_participations"] = self::get_user_participations($user_id);
		$user_obj["chat_participations"] = self::get_chat_participations($user_id);

		return $user_obj;
	}
}

?>