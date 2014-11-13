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

	public function project(){
		$project = $this->model("Project");
	
		$project_list = $project->get_all_projects();

		$content = $this->view("ProjectTest", array("projects" => $project_list));

		return $this->view("HtmlBase", array(	"title" => "Project Test",
																					"body" => $content,
																					"body_padding" => false));
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