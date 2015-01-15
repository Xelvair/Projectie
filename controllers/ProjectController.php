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
			$chat->add_user($private_chat["chat_id"], $logged_in_user["id"]);

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
		$tag = $this->model("Tag", $dbez);

		$project_id = $_POST["project_id"];

		$current_user_id = $auth->get_current_user()["id"];

		if(!$project->user_has_right($project_id, $current_user_id, "edit")){
			return json_encode(array("ERROR" => "ERR_NO_RIGHTS"));
		}

		$tag_expr = $exists_and_filled_out($_POST["tag_id"]) ? (integer)$_POST["tag_id"] : (string)$_POST["tag_name"];

		return json_encode($project->tag($tag, $project_id, $tag_expr));

	}

	public function untag(){}

	//$_POST["project_id"] : id of the project to fav
	public function favorite(){
		if(!filter_var($_POST["project_id"], FILTER_VALIDATE_INT)){
				return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS")); 
		}

		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$project = $this->model("Project", $dbez);

		$user = $auth->get_current_user();

		if(!$user)
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));

		$project_id = (int)$_POST["project_id"];

		return json_encode($project->favorite($project_id, $user["user_id"]));
	}

	//$_POST["project_id"] : id of the project to unfav
	public function unfavorite(){
		if(!filter_var($_POST["project_id"], FILTER_VALIDATE_INT)){
				return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS")); 
		}

		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$project = $this->model("Project", $dbez);

		$user = $auth->get_current_user();

		if(!$user)
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));

		$project_id = (int)$_POST["project_id"];

		return json_encode($project->unfavorite($project_id, $user["user_id"]));
	}

	//$_POST["project_id"] : project to post the news to
	//$_POST["content"] : content of the news post
	public function post_news(){
		if(!isset($_POST["project_id"]) ||
			 !isset($_POST["content"]))
		{
			return array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS");
		}

		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$project = $this->model("Project", $dbez);

		$user = $auth->get_current_user();

		if(!$user)
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));

		return json_encode($project->post_news([
			"author_id" => (int)$user["user_id"],
			"project_id" => (int)$_POST["project_id"],
			"content" => $_POST["content"]
		]));
	}

	//$_POST["project_news_id"] : id of the news to edit
	//$_POST["content"] : content of the new post
	public function edit_news(){
		if(!isset($_POST["project_news_id"]) ||
			 !isset($_POST["content"]))
		{
			return array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS");
		}

		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$project = $this->model("Project", $dbez);

		$user = $auth->get_current_user();

		return json_encode($project->edit_news([
			"project_news_id" => (int)$_POST["project_news_id"],
			"editor_id" => (int)$user["user_id"],
			"content" => $_POST["content"]
		]));
	}

	//$_POST["project_news_id"] : id of the news to remove
	public function remove_news(){
		if(!isset($_POST["project_news_id"])){
			return array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS");
		}

		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$project = $this->model("Project", $dbez);

		$user = $auth->get_current_user();

		return json_encode($project->remove_news([
			"project_news_id" => (int)$_POST["project_news_id"],
			"remover_id" => (int)$user["user_id"]
		]));
	}

	public function get_tag_meta($project_id){
		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$project = $this->model("Project", $dbez);

		$user = $auth->get_current_user();

		$can_edit_tags = false;

		if($user){
			$can_edit_tags = $project->user_has_right((int)$project_id, $user["user_id"], "edit");
		}

		return json_encode(array(
			"editable" => $can_edit_tags,
			"tags" => $project->get_tags($project_id)
		));
	}

	public function show($data){
		global $locale;
		global $CONFIG;

		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$project = $this->model("Project", $dbez);
		if(!isset($data[0])){
			return "No project id given.";
		}
		$data[0] = (int)$data[0];

		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}

		$project_obj = $project->get((int)$data[0]);
		if(!$project_obj){
			return "No project found for id ".$data[0];
		}
		
		$footer_array = array("user" => ($user == null ? null : $user["username"]));
		
		$tags = array("tags" => array(), "tag_box_title" => false);
		array_push($tags["tags"], array("tag_id" => 12, "name" => "dafuq r u?"));
		
		$tag_box = $this->view("TagBox", $tags);
		
		$member_list = $project->get_positions((int)$data[0]);
		$member_list = array_map(function($entry) use ($auth, $project, $project_obj, $user){
			$result = array_merge($entry, array("user" => $auth->get_user($entry["user_id"])), array("project" => $project->get($entry["project_id"])));
			write_log(Logger::DEBUG, print_r($entry, true));
			$flags = array();

			if($user){
				//If the currently shown user is the logged in user
				if(
					$project->exists_participation($project_obj["project_id"], $entry["user_id"]) &&
					$user["user_id"] == $result["user"]["user_id"]
				){
					array_push($flags, "LEAVE", "RIGHTS");
				}

				//If the current user is the creator of the project
				if($project_obj["creator_id"] == $user["user_id"]){
					array_push($flags, "RIGHTS", "RIGHTS_EDITABLE");
					
					if($entry["user_id"] != $user["user_id"] && !empty($entry["user_id"])){
						array_push($flags, "KICK");
					} else if($entry["user_id"] != $user["user_id"] && empty($entry["user_id"])){
						array_push($flags, "REMOVE");
					}
				}

				//If the current user is not a participator in the project
				if(
					!$project->exists_participation($project_obj["project_id"], $user["user_id"]) && 
					!$project->exists_participation_request($project_obj["project_id"], $user["user_id"]) &&
					empty($entry["user_id"])
				){
					array_push($flags, "PARTICIPATE");
				}
			}

			$flags = array_unique($flags);

			return array("project_position" => $result, "flags" => $flags);
		}, $member_list);

		return $this->view("HtmlBase", array(	
			"title" => "Projectie - Driving Development", 
			"body" => $this->view("ContentWrapper", array(	
				"content" => $this->view("Project", array(
					"news_feed" => $this->view("TitleDescriptionList", array(
						"list_title" =>  $locale['news_feed'],
						"entries" => array(
							array(
								"title" => "Trending Project 1",
								"desc" => "Test Desc 1",
								"thumb" => abspath("/public/images/default-profile-pic.png"),
								"creator" => array("id" => "1", "name" => "admin"),
								"source" => array("id" => "1", "name" => "Test Project"), 
								"time" => "09:12"
							),
							array(
								"title" => "Trending Project 2",
								"desc" => "Test Desc 2",
								"thumb" => abspath("/public/images/default-profile-pic.png"),
								"creator" => array("id" => "1", "name" => "admin"),
								"source" => array("id" => "1", "name" => "Test Project"), 
								"time" => "09:12"
							),
							array(
								"title" => "Trending Project 3",
								"desc" => "Test Desc 3",
								"thumb" => abspath("/public/images/default-profile-pic.png"),
								"creator" => array("id" => "1", "name" => "admin"),
								"source" => array("id" => "1", "name" => "Test Project"), 
								"time" => "09:12"
							)
						)
					)), 
					"project" => array(
						"participators" => $project->get_participators($data[0]), 
						"desc" => $project_obj["description"], 
						"subtitle" =>  $project_obj["subtitle"], 
						"title" => $project_obj["title"], 
						"header" => abspath("/public/images/default-banner.png"), 
						"time" => "14. 08. 2013 10:23", 
						"fav_count" => $project->get_fav_count($data[0]),
						"member_list" => $this->view_batch("ParticipationListTest", $member_list)
					),
					"tag_box" => $tag_box
				)), 
				"user" => ($user == null ? null : $user["username"]),
				"login_modal" => $this->view("LoginModal"),
				"footer" => $this->view("Footer", array(
					"user" => ($user == null ? null : $user["username"]))
				))
			), 
			"body_padding" => true,
			"current_user" => $user
		));
	}
}

?>