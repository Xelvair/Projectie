<?php
require_once("../core/Controller.php");

class HomeController extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

		$auth = $this->model("Auth");
		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
	
		$new_list = array("entries" => array(), "list_title" => $locale["new_projects"]);
		array_push($new_list["entries"], array("title" => "New Project 1", "desc" => "Test Desc 1", "thumb" => abspath("/public/images/question_mark_small.png")));
		$html_new = $this->view("TitleDescriptionList", $new_list);
		
		$news_list = array("entries" => array(), "list_title" => $locale["news"]);
		array_push($news_list["entries"], array("title" => "News 1", "desc" => "Test Desc 1", "thumb" => abspath("/public/images/question_mark_small.png")));
		$html_news = $this->view("TitleDescriptionList", $news_list);
		
		$trending_list = array("entries" => array(), "list_title" => $locale["trending_projects"]);
		array_push($trending_list["entries"], array("title" => "Trending Project 1", "desc" => "Test Desc 1", "thumb" => abspath("/public/images/question_mark_small.png")));
		array_push($trending_list["entries"], array("title" => "Trending Project 2", "desc" => "Test Desc 2", "thumb" => abspath("/public/images/question_mark_small.png")));
		array_push($trending_list["entries"], array("title" => "Trending Project 3", "desc" => "Test Desc 3", "thumb" => abspath("/public/images/question_mark_small.png")));
		$html_trending = $this->view("TitleDescriptionList", $trending_list);
		
		$footer_array = array("username" => "");
		$footer = $this->view("Footer", $footer_array);
		
	

	
        $mainpagelists = array("top_project" => array(), "left_col" => $html_new, "mid_col" => $html_trending, "right_col" => $html_news, "footer" => $footer);
		array_push($mainpagelists["top_project"], array("title" => "Test Project 1", "desc" => "Test Desc 1", "thumb" => abspath("/public/images/header.jpg")));
		array_push($mainpagelists["top_project"], array("title" => "Test Project 2", "desc" => "Test Desc 2", "thumb" => abspath("/public/images/header.jpg")));
		array_push($mainpagelists["top_project"], array("title" => "Test Project 3", "desc" => "Test Desc 3", "thumb" => abspath("/public/images/header.jpg")));

		$content = $this->view("MainPageContent", $mainpagelists);

		$login_modal = $this->view("LoginModal", "");

		$contentwrap = $this->view("ContentWrapper", array(	"content" => $content, 
															"user" => ($user == null ? null : $user["username"]),
															"login_modal" => $login_modal));

		$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
												"body" => $contentwrap, 
												"body_padding" => true,
												"current_user" => $user));
		return $html;
	}
}
?>