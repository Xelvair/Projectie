<?php
require_once("../core/Controller.php");

class HomeController extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

		$locale->load("en-us");

	
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

	
		$mainpagelists = array("top_project" => array(), "left_col" => $html_new, "mid_col" => $html_trending, "right_col" => $html_news);
		array_push($mainpagelists["top_project"], array("title" => "Test Project 1", "desc" => "Test Desc 1", "thumb" => abspath("/public/images/header.jpg")));
		array_push($mainpagelists["top_project"], array("title" => "Test Project 2", "desc" => "Test Desc 2", "thumb" => abspath("/public/images/header.jpg")));
		array_push($mainpagelists["top_project"], array("title" => "Test Project 3", "desc" => "Test Desc 3", "thumb" => abspath("/public/images/header.jpg")));

		$content = $this->view("MainPageContent", $mainpagelists);
		
		$contentwrap = $this->view("ContentWrapper", array("content" => $content));
		$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
												"body" => $contentwrap, 
												"body_padding" => true));
		return $html;
	}
}
?>