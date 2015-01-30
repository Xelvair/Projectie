<?php

require_once("../core/Model.php");

class AuthModel implements Model{
	private $loggedInUser;

	function __construct(){
		global $mysqli;

		if(isset($_SESSION["login_user_id"])){

			$user = self::get_user($_SESSION["login_user_id"]);

			if(!$user){
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

	public function validate_email($email){
		if(!preg_match("/^[a-zA-Z0-9-.]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,4}$/", $email)){
			write_log(Logger::DEBUG, "Tried to register malformed email <".$email.">!");
			throw new Exception("ERR_INVALID_EMAIL");
		}

		if(self::email_exists($email)){
			write_log(Logger::DEBUG, "Tried to register already existing email <".$email.">!");
			throw new Exception("ERR_EMAIL_ALREADY_EXISTS");
		}
	}

	public function validate_username($username){
		if(!preg_match("/^[a-zA-Z0-9.-]+$/", $username)){
			write_log(Logger::DEBUG, "Tried to register malformed username '".$username.">!");
			throw new Exception("ERR_INVALID_USERNAME");
		}

		if(self::username_exists($username)){
			write_log(Logger::DEBUG, "Tried to register already existing username '".$username.">!");
			throw new Exception("ERR_USERNAME_IN_USE");
		}
	}

	public function validate_password($password){
		if(strlen($password) < 7){
			throw new Exception("ERR_INVALID_PASSWORD");
		}
	}

	public function validate_lang($lang){
		if(!file_exists(abspath_lcl("/locale/".$lang.".locale"))){
			throw new Exception("ERR_INVALID_LANG");
		}
	}

	public function register($email, $username, $lang, $password){ 
		try {
			self::validate_email($email);
			self::validate_username($username);
			self::validate_lang($lang);
			self::validate_password($password);
		} catch (Exception $e){
			return array("ERROR" => $e->getMessage());
		}

		$password_salt = substr(md5(time()), 0, 8);

		$password_hash = md5($password.$password_salt);

		$result = DBEZ::insert("user", [
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

	public function password_check($password, $hash, $salt){
		return ($hash == md5($password.$salt));
	}

	public function login($email, $password){
		global $mysqli;

		$result = DBEZ::find("user", ["email" => $email, "active" => 1], ["user_id", "password_hash", "password_salt"]);

		if(!$result){
			write_log(Logger::WARNING, "Failed to login, email '".$email."' not found!");
			return array("ERROR" => "ERR_USER_NOT_FOUND");
		}

		extract($result[0], EXTR_OVERWRITE | EXTR_PREFIX_ALL, "res");

		if(!self::password_check($password, $res_password_hash, $res_password_salt)){
			$this->logout();

			write_log(Logger::DEBUG, "Failed login: incorrect password.");

			return array("ERROR" => "ERR_INCORRECT_PASSWORD");
		}

		$_SESSION["login_user_id"] = $res_user_id;
		$loggedInUser = self::get_user($res_user_id);

		write_log(Logger::DEBUG, "User #".$res_user_id." logged in.");

		return array();
	}

	public function logout(){
		if(isset($_SESSION["login_user_id"])){
			unset($_SESSION["login_user_id"]);
		}
		$this->loggedInUser = null;

		return array();
	}

	public function email_exists($email){
		return !!DBEZ::find("user", ["email" => $email, "active" => 1], ["user_id"]);
	}

	public function username_exists($username){
		return !!DBEZ::find("user", ["username" => $username, "active" => 1], ["user_id"]);
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
		return DBEZ::find("project", ["creator_id" => (int)$user_id, "active" => 1], ["project_id", "create_time", "title", "subtitle"], DBEZ_SLCT_KEY_AS_INDEX);
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
			LEFT OUTER JOIN project_position AS pp 
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
			$user_participations[$id] = $row;
		}

		$query_get_projects->close();

		return $user_participations;
	}	

	public function get_chat_participations($user_id){
		return DBEZ::find("chat_participation", ["participant_id" => (int)$user_id], ["chat_participation_id", "chat_id"], DBEZ_SLCT_KEY_AS_INDEX);
	}

	public function exists($user_id){
		return !!DBEZ::find("user", $user_id, ["user_id"]);
	}

	public function get_user($user_id){
		$result = DBEZ::find("user", ["user_id" => (int)$user_id, "active" => 1], ["user_id", "create_time", "username", "email", "lang", "is_admin"]);

		if(!$result)
			return array();

		$user = $result[0];

		//fixing compatibility issue with lots of stuff
		$user["id"] = $user["user_id"];

		$user += array(
			"created_projects" => self::get_created_projects($user_id),
			"project_participations" => self::get_user_participations($user_id),
			"chat_participations" => self::get_chat_participations($user_id),
			"tags" => self::get_tags($user_id),
			"fav_projects" => self::get_fav_projects($user_id)
		);

		return $user;
	}

	public function set_user($info){
		global $mysqli;

		$on_success_queries = array();

		if(!validate($info, ["user_id" => "int"], VALIDATE_CAST)){
			return array("ERROR" => "ERR_INVALID_ARGUMENTS");
		}

		$user = self::get_user($info["user_id"]);
		if(!$user){
			return array("ERROR" => "ERR_USER_NOT_FOUND");
		}

		try{
			if(validate($info, ["email" => "string"]) && $info["email"] != $user["email"]){
				self::validate_email($info["email"]);

				$stmt_set_email = $mysqli->prepare("UPDATE user SET email = ? WHERE user_id = ?");
				$stmt_set_email->bind_param("si", $info["email"], $info["user_id"]);

				array_push($on_success_queries, $stmt_set_email);
			}

			if(validate($info, ["lang" => "string"])){
				self::validate_lang($info["lang"]);

				$stmt_set_lang = $mysqli->prepare("UPDATE user SET lang = ? WHERE user_id = ?");
				$stmt_set_lang->bind_param("si", $info["lang"], $info["user_id"]);

				array_push($on_success_queries, $stmt_set_lang);
			}

			if(validate($info, ["username" => "string"])){
				self::validate_username($info["username"]);

				$stmt_set_username = $mysqli->prepare("UPDATE user SET username = ? WHERE user_id = ?");
				$stmt_set_username->bind_param("si", $info["username"], $info["user_id"]);

				array_push($on_success_queries, $stmt_set_username);
			}

			if(validate($info, ["old_password" => "string", "new_password" => "string"])){
				$user_info = DBEZ::find("User", $info["user_id"], ["password_hash", "password_salt"]);

				if(self::password_check($info["old_password"], $user_info["password_hash"], $user_info["password_salt"])){
					self::validate_password($info["new_password"]);

					$password_salt = substr(md5(time()), 0, 8);

					$password_hash = md5($info["new_password"].$password_salt);

					$stmt_set_password = $mysqli->prepare("UPDATE user SET password_hash = ?, password_salt = ? WHERE user_id = ?");
					$stmt_set_password->bind_param("ssi", $password_hash, $password_salt, $info["user_id"]);
					array_push($on_success_queries, $stmt_set_password);
				} else {
					return array("ERROR" => "ERR_INCORRECT_OLD_PASSWORD");
				}
			}
		} catch (Exception $e){
			return array("ERROR" => $e->getMessage());
		}

		foreach($on_success_queries as $stmt){
			$stmt->execute();
		}

		return array();
	}

	public function tag($tag_model, $user_id, $tag){
		global $mysqli;

		if(!self::exists($user_id))
			return array("ERROR" => "ERR_USER_NONEXISTENT");

		if(gettype($tag) != "integer" && gettype($tag) != "string")
			throw new InvalidArgumentException("request_tag function expects integer or string. ".gettype($tag)." given");

		//Get tag from database
		$tag_entry = $tag_model->request_tag($tag);

		if(sizeof($tag_entry) <= 0)
			return array("ERROR" => "ERR_TAG_NONEXISTENT");

		if(self::is_tagged($tag_model, $user_id, $tag))
			return array("ERROR" => "ERR_PROJECT_ALREADY_TAGGED");

		$tag_id = $tag_entry["tag_id"];

		DBEZ::insert("user_tag", ["user_id" => $user_id, "tag_id" => $tag_id]);
	
		return array();
	}

	public function is_tagged($tag_model, $user_id, $tag){
		if(gettype($tag) != "integer" && gettype($tag) != "string"){
			throw new InvalidArgumentException("request_tag function expects integer or string. ".gettype($tag)." given");
		}

		$tag_entry = $tag_model->get_tag($tag);

		if(sizeof($tag_entry) <= 0){
			throw new InvalidArgumentException("request_tag function cannot find '".$tag."' in database!");
		}

		$tag_id = $tag_entry["tag_id"];

		return !!DBEZ::find("user_tag", ["user_id" => $user_id, "tag_id" => $tag_id], ["user_tag_id"]);
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

	public function get_fav_projects($user_id){
		return DBEZ::find("project_fav", ["user_id" => (int)$user_id], "*");
	}
}

?>