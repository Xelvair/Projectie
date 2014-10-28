<?php

require_once("../core/Controller.php");

class Test extends Controller{
	public function auth(){
		global $locale;
		global $CONFIG;

		$locale->load("en-us");

		$auth = $this->model("Auth");
		$logged_in_user = $auth->getLoggedInUser();
		if($logged_in_user){
			$login_name = $logged_in_user->getName();
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
}

?>