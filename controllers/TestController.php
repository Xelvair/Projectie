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
			$login_user = $auth->login($_POST["email"], $_POST["password"]);
			print_r($login_user);
			if($login_user){
				header("Location: ".abspath("/test/auth/login_success"));
			} else {
				header("Location: ".abspath("/test/auth/login_failure"));
			}
		} else {
			header("Location: ".abspath("/test/auth"));
		}
	}

	public function logout(){
		$auth = $this->model("Auth");
		$auth->logout();
		header("Location: ".abspath("/test/auth"));
	}

	public function register_action(){
		if(isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["password"])){
			$auth = $this->model("Auth");
			if($auth->register($_POST["email"], $_POST["username"], $_POST["password"])){
				header("Location: ".abspath("/test/auth/register_success"));
			} else {
				header("Location: ".abspath("/test/auth/register_failure"));
			}
		} else {
			header("Location: ".abspath("/test/auth"));
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
			if($create_result){
				header("Location: ".abspath("/test/project/success"));
			} else {
				header("Location: ".abspath("/test/project/failure"));
			}
		} else {
			header(abspath("/test/project"));
		}
	}
}

?>