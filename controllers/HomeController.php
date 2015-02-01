<?php
require_once("../core/Controller.php");

class HomeController extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

		$auth = Core::model("Auth");
		$project = Core::model("Project");

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
		
		$news = array();
		
		array_push($news, array("thumb" => abspath("/public/images/header.jpg"), "favs" => 14, "members" => 75, "title" => "Protestie", "desc" => "I just came to say hello", "id" => 1));
		
		array_push($news, array("thumb" => abspath("/public/images/header.jpg"), "favs" => 103432, "members" => 12, "title" => "Protestie", "desc" => "I just came to say hello", "id" => 1));
		
		array_push($news, array("thumb" => abspath("/public/images/default-banner.png"), "favs" => 30, "members" => 42, "title" => "Protestie", "desc" => "I just came to say hello, but with more text so i can test the overflow and max-height css property 'n stuff. and know i am just sitting here writing this unnessecery text waiting for a better life. wow i'm getting deep again. such overflow. oh god this takes me half of my  life time. I have to do something against it. Ok let me see: lsakfdjasd fasflkdöaslfkdl sdöflkasölkfd safdlmöaslfkd sadöflasöf asödlföslafwe fpasf aefoijasfpoka efasofkosafe safüokaefa sfokasef saüosakefnasüf saefpküasefajsef aseofasekfknasf asoefköoasef asefpmsfkasöfekmöslamf safoeasöof asöfmalsf awefölmasf  asödlmsf asödlfölasdf. Maybe this helps a bit.", "id" => 1));
		
		$html_news = array();
		
		foreach($news as $news){
			array_push($html_news, Core::view("ProjectReview", $news));
		}
		
		
		$new = array();
		array_push($new, array("thumb" => abspath("/public/images/default-banner.png"), "favs" => 103432, "members" => 12, "title" => "Protestie", "desc" => "I just came to say hello", "id" => 1));
		
		array_push($new, array("thumb" => abspath("/public/images/header.jpg"), "favs" => 30, "members" => 42, "title" => "Protestie", "desc" => "I just came to say hello, but with more text so i can test the overflow and max-height css property 'n stuff. and know i am just sitting here writing this unnessecery text waiting for a better life. wow i'm getting deep again. such overflow. oh god this takes me half of my  life time. I have to do something against it. Ok let me see: lsakfdjasd fasflkdöaslfkdl sdöflkasölkfd safdlmöaslfkd sadöflasöf asödlföslafwe fpasf aefoijasfpoka efasofkosafe safüokaefa sfokasef saüosakefnasüf saefpküasefajsef aseofasekfknasf asoefköoasef asefpmsfkasöfekmöslamf safoeasöof asöfmalsf awefölmasf  asödlmsf asödlfölasdf. Maybe this helps a bit.", "id" => 1));
		
		array_push($new, array("thumb" => abspath("/public/images/header.jpg"), "favs" => 14, "members" => 75, "title" => "Protestie", "desc" => "I just came to say hello", "id" => 1));
		
		$html_new = array();
		
		foreach($new as $new){
			array_push($html_new, Core::view("ProjectReview", $new));
		}
				
		$trending = array();
		array_push($trending, array("thumb" => abspath("/public/images/default-banner.png"), "favs" => 30, "members" => 42, "title" => "Protestie", "desc" => "I just came to say hello, but with more text so i can test the overflow and max-height css property 'n stuff. and know i am just sitting here writing this unnessecery text waiting for a better life. wow i'm getting deep again. such overflow. oh god this takes me half of my  life time. I have to do something against it. Ok let me see: lsakfdjasd fasflkdöaslfkdl sdöflkasölkfd safdlmöaslfkd sadöflasöf asödlföslafwe fpasf aefoijasfpoka efasofkosafe safüokaefa sfokasef saüosakefnasüf saefpküasefajsef aseofasekfknasf asoefköoasef asefpmsfkasöfekmöslamf safoeasöof asöfmalsf awefölmasf  asödlmsf asödlfölasdf. Maybe this helps a bit.", "id" => 1));
		array_push($trending, array("thumb" => abspath("/public/images/header.jpg"), "favs" => 103432, "members" => 12, "title" => "Protestie", "desc" => "I just came to say hello", "id" => 1));
		
		array_push($trending, array("thumb" => abspath("/public/images/header.jpg"), "favs" => 14, "members" => 75, "title" => "Protestie", "desc" => "I just came to say hello", "id" => 1));
		
		$html_trending = array();
		
		foreach($trending as $trending){
			array_push($html_trending, Core::view("ProjectReview", $trending));
		}
		
	
		
		$footer_array = array("user" => ($user == null ? null : $user["username"]));
		$footer = Core::view("Footer", $footer_array);
		
		$user_review = Core::view('UserReview', "");
	
    $mainpagelists = array("top_project" => array(), "new" => $html_new, "trending" => $html_trending, "news" => $html_news, "user_review" => $user_review);
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