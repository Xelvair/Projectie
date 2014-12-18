<?php

require_once("../core/Model.php");

class AuthModel implements Model{
	private $loggedInUser;
	private $dbez;

	function __construct(DBEZModel $dbez){
		global $mysqli;

		$this->dbez = $dbez;

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

		$result = $this->dbez->insert("user", [
			"create_time" => time(), 
			"email" => $email, 
			"username" => $username, 
			"lang" => $lang, 
			"is_admin" => 0, 
			"password_salt" => $password_salt, 
			"password_hash" => $password_hash, 
			"active" => 1
		]);

		if($result){
			write_log(Logger::DEBUG, "Registered account '".$username."'!");
			return array();
		} else {
			write_log(Logger::ERROR, "Failed to register account!");
			return array("ERROR" => "ERR_DB_INSERT_FAILED");
		}
	}

	public function login($email, $password){
		global $mysqli;

		$result = $this->dbez->find("user", ["email" => $email, "active" => 1], ["user_id", "password_hash", "password_salt"]);

		if(empty($result)){
			write_log(Logger::WARNING, "Failed to login, email '".$email."' not found!");
			return array("ERROR" => "ERR_USER_NOT_FOUND");
		}

		extract($result[0], EXTR_OVERWRITE | EXTR_PREFIX_ALL, "res");

		//Check the password
		$password_hash = md5($password.$res_password_salt);
		if($password_hash != $res_password_hash){
			//If login failed, unset all values and exit
			$this->logout();

			write_log(Logger::DEBUG, "Failed login: incorrect password.");

			return array("ERROR" => "ERR_INCORRECT_PASSWORD");
		} else {
			//If login succeeded, write to the session and set values
			$_SESSION["login_user_id"] = $res_user_id;
			$loggedInUser = self::get_user($res_user_id);

			write_log(Logger::DEBUG, "User #".$res_user_id." logged in.");

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

		$result = $this->dbez->find("user", ["email" => $email, "active" => 1], ["user_id"]);

		return !empty($result);
	}

	public function username_exists($username){
		global $mysqli;

		$result = $this->dbez->find("user", ["username" => $username, "active" => 1], ["user_id"]);

		return !empty($result);
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
		return $this->dbez->find("project", ["creator_id" => $user_id, "active" => 1], ["project_id", "create_time", "title", "subtitle"], true);
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
			$user_participations[$id] = $row;
		}

		$query_get_projects->close();

		return $user_participations;
	}	

	public function get_chat_participations($user_id){
		return $this->dbez->find("chat_participation", ["participant_id" => $user_id], ["chat_participation_id", "chat_id"], true);
	}

	public function exists($user_id){
		$result = $this->dbez->find("user", $user_id, ["user_id"]);

		return !empty($result);
	}

	public function get_user($user_id){
		$result = $this->dbez->find("user", ["user_id" => $user_id, "active" => 1], ["user_id", "create_time", "username", "email", "lang", "is_admin"])[0];

		//fixing compatibility issue with lots of stuff
		$result["id"] = $result["user_id"];

		$result += array(
			"created_projects" => self::get_created_projects($user_id),
			"project_participations" => self::get_user_participations($user_id),
			"chat_participations" => self::get_chat_participations($user_id),
			"tags" => self::get_tags($user_id)
		);

		write_log(Logger::DEBUG, print_r($result, true));

		return $result;
	}

	public function tag($tag_model, $user_id, $tag){
		global $mysqli;

		if(!self::exists($user_id)){
			return array("ERROR" => "ERR_USER_NONEXISTENT");
		}

		if(gettype($tag) != "integer" && gettype($tag) != "string"){
			throw new InvalidArgumentException("request_tag function expects integer or string. ".gettype($tag)." given");
		}

		//Get tag from database
		$tag_entry = $tag_model->request_tag($tag);

		if(sizeof($tag_entry) <= 0){
			return array("ERROR" => "ERR_TAG_NONEXISTENT");
		}

		if(self::is_tagged($tag_model, $user_id, $tag)){
			return array("ERROR" => "ERR_PROJECT_ALREADY_TAGGED");
		}

		$tag_id = $tag_entry["tag_id"];

		$query_tag_project = $mysqli->prepare("INSERT INTO user_tag (user_id, tag_id) VALUES (?, ?)");
		$query_tag_project->bind_param("ii", $user_id, $tag_id);
		$query_tag_project->execute();
	
		return array();
	}

	public function is_tagged($tag_model, $user_id, $tag){
		global $mysqli;

		if(gettype($tag) != "integer" && gettype($tag) != "string"){
			throw new InvalidArgumentException("request_tag function expects integer or string. ".gettype($tag)." given");
		}

		$tag_entry = $tag_model->get_tag($tag);

		if(sizeof($tag_entry) <= 0){
			throw new InvalidArgumentException("request_tag function cannot find '".$tag."' in database!");
		}

		$tag_id = $tag_entry["tag_id"];

		$result = $this->dbez->find("user_tag", ["user_id" => $user_id, "tag_id" => $tag_id], ["user_tag_id"]);

		return !empty($result);
	}

	public function untag($tag_model, $user_id, $tag){
		global $mysqli;

		$tag_entry = $tag_model->get_tag($tag);

		$tag_id = $tag_entry["tag_id"];

		$query_untag_project = $mysqli->prepare("DELETE FROM user_tag WHERE user_id = ? AND tag_id = ?");
		$query_untag_project->bind_param("ii", $user_id, $tag_id);
		$query_untag_project->execute();

		return array();
	}

	public function get_tags($user_id){
		global $mysqli;

		$query_get_tags = $mysqli->prepare("SELECT t.tag_id AS tag_id, t.name AS name FROM user_tag ut LEFT JOIN tag t ON(ut.tag_id = t.tag_id) WHERE ut.user_id = ?");
		$query_get_tags->bind_param("i", $user_id);
		$query_get_tags->execute();
		$result = $query_get_tags->get_result();

		$query_get_tags->close();

		return $result->fetch_all(MYSQLI_ASSOC);
	}
}

?>