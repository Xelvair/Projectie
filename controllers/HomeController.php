<?php
require_once("../core/Controller.php");

class HomeController extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

		$dbez = Core::model("DBEZ");
		$auth = Core::model("Auth", $dbez);
		$project = Core::model("Project", $dbez);

		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
	
		$new_list = array("entries" => $project->get_new_projects(3), "list_title" => $locale["new_projects"]);
		$html_new = Core::view("TitleDescriptionList", $new_list);
		
		$news_list = array("entries" => array(), "list_title" => $locale["news"]);
		array_push($news_list["entries"], array("title" => "Trending Project 1", "description" => "Test Desc 1", "thumb" => abspath("/public/images/default-profile-pic.png")));
		array_push($news_list["entries"], array("title" => "Trending Project 1", "description" => "Test Desc 1", "thumb" => abspath("/public/images/default-profile-pic.png")));
		array_push($news_list["entries"], array("title" => "Trending Project 1", "description" => "Test Desc 1", "thumb" => abspath("/public/images/default-profile-pic.png")));
		$html_news = Core::view("TitleDescriptionList", $news_list);
		
		$trending_list = array("entries" => array(), "list_title" => $locale["trending_projects"]);
		array_push($trending_list["entries"], array("title" => "Trending Project 1", "description" => "Test Desc 1", "thumb" => abspath("/public/images/default-profile-pic.png")));
		array_push($trending_list["entries"], array("title" => "Trending Project 1", "description" => "Test Desc 1", "thumb" => abspath("/public/images/default-profile-pic.png")));
		array_push($trending_list["entries"], array("title" => "Trending Project 1", "description" => "Test Desc 1", "thumb" => abspath("/public/images/default-profile-pic.png")));
		$html_trending = Core::view("TitleDescriptionList", $trending_list);
		
		$footer_array = array("user" => ($user == null ? null : $user["username"]));
		$footer = Core::view("Footer", $footer_array);
		
		$user_review = Core::view('UserReview', "");
	
    $mainpagelists = array("top_project" => array(), "left_col" => $html_new, "mid_col" => $html_trending, "right_col" => $html_news, "user_review" => $user_review);
		array_push($mainpagelists["top_project"], array("title" => "Test Project 1", "description" => "Test Desc 1", "thumb" => abspath("/public/images/header.jpg")));
		array_push($mainpagelists["top_project"], array("title" => "Test Project 2", "description" => "Test Desc 2", "thumb" => abspath("/public/images/header.jpg")));
		array_push($mainpagelists["top_project"], array("title" => "Test Project 3", "description" => "Test Desc 3", "thumb" => abspath("/public/images/header.jpg")));

		$content = Core::view("MainPageContent", $mainpagelists);

		$login_modal = Core::view("LoginModal", "");

		$contentwrap = Core::view("ContentWrapper", array(	"content" => $content, 
															"user" => ($user == null ? null : $user),
															"login_modal" => $login_modal,
															"footer" => $footer));

		$html = Core::view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
												"body" => $contentwrap, 
												"body_padding" => true,
												"current_user" => $user));
		return $html;
	}
}
?>