<?php
require_once("../core/Controller.php");

class ProjectController extends Controller{
	public function create(){
		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$chat = $this->model("Chat", $dbez);

		$logged_in_user = $auth->get_current_user();

		if($logged_in_user == null){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		if(isset($_POST["title"]) && isset($_POST["subtitle"]) && isset($_POST["description"])){
			$project = $this->model("project", $dbez);

			//Create public and private chat for project
			$public_chat = $chat->create_public(0, $_POST["title"]." Public Chat");
			$private_chat = $chat->create_private(0, $_POST["title"]." Private Chat");

			//Add creator to private chat
			write_log(Logger::DEBUG, print_r($chat->add_user($private_chat["chat_id"], $logged_in_user["id"]), true));

			//Create the project itself
			$create_result = $project->create(
				$logged_in_user["id"], 
				array(
					"title" => htmlentities($_POST["title"]), 
					"subtitle" => htmlentities($_POST["subtitle"]), 
					"description" => htmlentities($_POST["description"]),
					"private_chat_id" => $private_chat["chat_id"],
					"public_chat_id" => $public_chat["chat_id"]
				)
			);
			return json_encode($create_result);
		} else {
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}
	}

	//$_POST["request_type"] : type of requester, either PROJECT_TO_USER or USER_TO_PROJECT
	//$_POST["requester_id"] : id of the sender of the request
	//$_POST["requestee_id"] : id of the receiver of the request
	public function request_participation(){
		if(	
			!isset($_POST["request_type"]) || 
			!isset($_POST["requester_id"]) ||
			!isset($_POST["requestee_id"])
		){
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}

		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$chat = $this->model("Chat", $dbez);
		
		$project = $this->model("Project", $dbez);
		$current_user = $auth->get_current_user();

		if(!$current_user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		switch($_POST["request_type"]){
			case "PROJECT_TO_USER":
				if($project->user_has_right($_POST["requester_id"], $current_user["id"], "add_participants")){
					return json_encode($project->request_participation($chat, $_POST["requester_id"], $_POST["requestee_id"], "PROJECT_TO_USER"));
				} else {
					return json_encode(array("ERROR" => "ERR_NO_RIGHTS"));
				}
				break;
			case "USER_TO_PROJECT":
				return json_encode($project->request_participation($chat, $_POST["requestee_id"], $_POST["requester_id"], "USER_TO_PROJECT"));
				break;
			default:
				return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS")); 
				break;
		}
	}

	//$_POST["project_participation_request_id"]
	public function accept_participation(){
		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$project = $this->model("Project", $dbez);

		$current_user = $user->get_current_user();

		if(!$current_user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		$project->accept_participation($_POST["project_participation_request_id"], $current_user["id"]);
	}

	public function tag(){
		$exists_and_filled_out = function(&$var){
			return (isset($var) && !empty($var));
		};

		if
		(
			!$exists_and_filled_out($_POST["project_id"]) ||
			!(
				$exists_and_filled_out($_POST["tag_id"]) ||
				$exists_and_filled_out($_POST["tag_name"])
			)
		){
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}

		if($exists_and_filled_out($_POST["tag_id"])){
			if(!filter_var($_POST["tag_id"], FILTER_VALIDATE_INT)){
				return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS")); 
			}
		}

		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$project = $this->model("Project", $dbez);
		$tag = $this->model("Tag");

		$project_id = $_POST["project_id"];

		$current_user_id = $auth->get_current_user()["id"];

		if(!$project->user_has_right($project_id, $current_user_id, "edit")){
			return json_encode(array("ERROR" => "ERR_NO_RIGHTS"));
		}

		$tag_expr = $exists_and_filled_out($_POST["tag_id"]) ? (integer)$_POST["tag_id"] : (string)$_POST["tag_name"];

		return json_encode($project->tag($tag, $project_id, $tag_expr));

	}

	public function untag(){}
	
	public function index(){
	
		global $locale;
		global $CONFIG;

		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
		
		$footer_array = array("username" => "");
		$footer = $this->view("Footer", $footer_array);
		
		$user_review = $this->view("UserReview", "");
		
		$news_feed_content = array("entries" => array(), "list_title" =>  "News Feed");
		array_push($news_feed_content["entries"], array("title" => "Project Created", "desc" => "Today with created our glorious project", "thumb" => abspath("/public/images/default-profile-pic.png")));
		array_push($news_feed_content["entries"], array("title" => "Project Created", "desc" => "Today with created our glorious project", "thumb" => abspath("/public/images/default-profile-pic.png")));
		array_push($news_feed_content["entries"], array("title" => "Project Created", "desc" => "Today with created our glorious project", "thumb" => abspath("/public/images/default-profile-pic.png")));
		
		$news_feed = $this->view("TitleDescriptionList", $news_feed_content);
		
		$content = $this->view("Project", array("footer" => $footer, "user_review" => $user_review, "news_feed" => $news_feed));
		
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