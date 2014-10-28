<?php
require_once("../core/Controller.php");

class Home extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

		$locale->load("en-us");

		$trending_projects = array("entries" => array());
		array_push($trending_projects["entries"], array("title" => "Test Project 1", "desc" => "Test Desc 1", "thumb" => abspath("/public/images/question_mark_big.png")));
		array_push($trending_projects["entries"], array("title" => "Test Project 2", "desc" => "Test Desc 2", "thumb" => abspath("/public/images/question_mark_big.png")));
		array_push($trending_projects["entries"], array("title" => "Test Project 3", "desc" => "Test Desc 3", "thumb" => abspath("/public/images/question_mark_big.png")));

		$content = $this->view("TrendingProjectsSidebar", $trending_projects);
		$contentwrap = $this->view("ContentWrapper", array("content" => $content));
		$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
												"body" => $contentwrap, 
												"body_padding" => true));
		return $html;
	}
}
?>