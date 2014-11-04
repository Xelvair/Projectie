<?php

require_once(abspath_lcl("/core/Controller.php"));

class TestController extends Controller{
	public function auth(){
		global $locale;
		global $CONFIG;

		$locale->load("en-us");

		$auth = $this->model("Auth");
		$logged_in_user = $auth->get_current_user();
		if($logged_in_user){
			$login_name = $logged_in_user->get_name();
		} else {
			$login_name = null;
		}

		$content = $this->view("LoginTest", array("login" => $login_name));
		return $this->view("HtmlBase", array("title" => "Login Test", "body" => $content, "body_padding" => false));
	}

	public function login_action(){
		if(isset($_POST["email"]) && isset($_POST["password"])){
			$auth = $this->model("Auth");
			return json_encode($auth->login($_POST["email"], $_POST["password"]));
		} else {
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}
	}

	public function logout_action(){
		$auth = $this->model("Auth");
		$auth->logout();
		return json_encode(array());
	}

	public function register_action(){
		if(isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["password"])){
			$auth = $this->model("Auth");
			return json_encode($auth->register($_POST["email"], $_POST["username"], $_POST["password"]));
		} else {
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}
	}

	public function project(){
		global $locale;

		$locale->load("en-us");

		$content = $this->view("ProjectTest", array());
		return $this->view("HtmlBase", array(	"title" => "Project Test",
												"body" => $content,
												"body_padding" => false));
	}

	public function project_action(){
		if(isset($_POST["title"]) && isset($_POST["subtitle"]) && isset($_POST["description"])){
			$project = $this->model("project");
			$auth = $this->model("Auth");
			$logged_in_user = $auth->get_current_user();
			$create_result = $project->create($logged_in_user->get_id(), array(	"title" => htmlentities($_POST["title"]), 
																				"subtitle" => htmlentities($_POST["subtitle"]), 
																				"description" => htmlentities($_POST["description"])));

			return json_encode($create_result);
		} else {
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}
	}

	public function chat(){
		global $locale;

		$locale->load("en-us");

		$content = $this->view("ChatTest", array());
		return $this->view("HtmlBase", array(	"title" => "Chat Test",
												"body" => $content,
												"body_padding" => false));
	}
}

?>