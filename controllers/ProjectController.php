<?php
require_once("../core/Controller.php");

class ProjectController extends Controller{
	public function create(){
		$auth = Core::model("Auth");
		$chat = Core::model("Chat");

		$logged_in_user = $auth->get_current_user();

		if($logged_in_user == null){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		if(isset($_POST["title"]) && isset($_POST["subtitle"]) && isset($_POST["description"])){
			$project = Core::model("project");

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

		$auth = Core::model("Auth");
		$chat = Core::model("Chat");
		$project = Core::model("Project");

		$current_user = $auth->get_current_user();

		if(!$current_user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		switch($_POST["request_type"]){
			case "PROJECT_TO_USER":
				if($project->user_has_right($_POST["requester_id"], $current_user["id"], "add_participants")){
					return json_encode($project->request_participation($chat, (int)$_POST["requester_id"], (int)$_POST["requestee_id"], "PROJECT_TO_USER"));
				} else {
					return json_encode(array("ERROR" => "ERR_NO_RIGHTS"));
				}
				break;
			case "USER_TO_PROJECT":
				return json_encode($project->request_participation($chat, (int)$_POST["requestee_id"], (int)$_POST["requester_id"], "USER_TO_PROJECT"));
				break;
			default:
				return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS")); 
				break;
		}
	}

	//$_POST["project_participation_request_id"]
	public function accept_participation_request(){
		$auth = Core::model("Auth");
		$project = Core::model("Project");

		$current_user = $auth->get_current_user();

		if(!$current_user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		return json_encode($project->accept_participation_request((int)$_POST["project_participation_request_id"], $current_user["id"]));
	}

	//$_POST["project_position_id"]
	public function cancel_participation(){
		$auth = Core::model("Auth");
		$project = Core::model("Project");

		$current_user = $auth->get_current_user();

		if(!$current_user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		return json_encode($project->cancel_participation((int)$_POST["project_position_id"], $current_user["id"]));
	}

	//$_POST["project_id"]
	//$_POST["position_title"]	
	public function add_position(){
		if(	
			!isset($_POST["project_id"]) || 
			!isset($_POST["position_title"])
		){
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}

		$project_id = (int)$_POST["project_id"];
		$project_title = htmlentities($_POST["position_title"]);

		$project = Core::model("Project");
		$auth = Core::model("Auth");

		$current_user = $auth->get_current_user();

		if(!$current_user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		return json_encode($project->add_position($project_id, $project_title, $current_user["user_id"]));
	}

	//$_POST["project_position_id"]
	public function remove_position(){
		$project = Core::model("Project");
		$auth = Core::model("Auth");

		$current_user = $auth->get_current_user();

		if(!$current_user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		$project_position_id = (int)$_POST["project_position_id"];

		if(!$project_position_id){
			return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS"));
		}
	
		return json_encode($project->remove_position($project_position_id, $current_user["user_id"]));
	}

	# $_POST["tag_id"] || $_POST["tag_name"]
	public function tag($data){
		$exists_and_filled_out = function(&$var){
			return (isset($var) && !empty($var));
		};

		if
		(
			!$exists_and_filled_out($data[0]) ||
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

		$auth = Core::model("Auth");
		$project = Core::model("Project");
		$tag = Core::model("Tag");

		$current_user = $auth->get_current_user();

		if(!$current_user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		$project_id = (integer)$data[0];

		$current_user_id = $auth->get_current_user()["id"];

		if(!$project->user_has_right($project_id, $current_user_id, "edit")){
			return json_encode(array("ERROR" => "ERR_NO_RIGHTS"));
		}

		return json_encode($project->tag($tag, $project_id, (integer)$_POST["tag_id"]));

	}

	public function get_tags($data){
		if(sizeof($data) <= 0){
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}

		$project = Core::model("Project");
		
		return json_encode($project->get_tags((int)$data[0]));
	}

	public function untag($data){
		if(sizeof($data) <= 0){
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}

		if(!isset($_POST["tag_id"])){
			return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS"));
		}

		$project_id = (int)$data[0];
		$tag_id = (int)$_POST["tag_id"];

		$auth = Core::model("Auth");
		$project = Core::model("Project");
		$tag = Core::model("Tag");

		$current_user = $auth->get_current_user();

		if(!$current_user || !$project->user_has_right($project_id, $current_user["user_id"], "edit")){
			return json_encode(array("ERROR" => "ERR_NO_RIGHTS"));
		}

		return json_encode($project->untag($tag, $project_id, $tag_id));
	}

	//$_POST["project_id"] : id of the project to fav
	public function favorite(){
		if(!filter_var($_POST["project_id"], FILTER_VALIDATE_INT)){
				return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS")); 
		}

		$auth = Core::model("Auth");
		$project = Core::model("Project");

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

		$auth = Core::model("Auth");
		$project = Core::model("Project");

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
			return json_encode(array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS"));
		}

		$auth = Core::model("Auth");
		$project = Core::model("Project");

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

		$auth = Core::model("Auth");
		$project = Core::model("Project");

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

		$auth = Core::model("Auth");
		$project = Core::model("Project");

		$user = $auth->get_current_user();

		return json_encode($project->remove_news([
			"project_news_id" => (int)$_POST["project_news_id"],
			"remover_id" => (int)$user["user_id"]
		]));
	}
	
	public function post_html(){
		$auth = Core::model("Auth");
		$user = $auth->get_current_user();
		$post = array();
		if(isset($_POST)){
		
		array_push($post, array(
			"creator" => array("id" => $user["user_id"], "name" => $user["username"]),
			"time" => $_POST["time"],
			"content" => $_POST["content"], 
			"title" => $_POST["title"]));
			
		
			return Core::view("Post", array("post" => $post));
		}
	
	}

	public function get_tag_meta($project_id){
		$auth = Core::model("Auth");
		$project = Core::model("Project");

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

	//$_POST["project_participation_request_id"] : id of the participation request to be cancelled
	public function cancel_participation_request(){
		$project = Core::model("Project");
		$auth = Core::model("Auth");

		$current_user = $auth->get_current_user();

		$req_id = (int)$_POST["project_participation_request_id"] ?: null;

		if(empty($req_id)){
			return array("ERROR" => "ERR_INVALID_PARAMETERS");
		}

		return json_encode($project->cancel_participation_request($req_id, $current_user["user_id"]));
	}

	public function search_by_tags(){
		if(empty($_POST["tags"])){
			$_POST["tags"] = array();
		}

		foreach($_POST["tags"] as &$tag_id){
			$tag_id = (integer)$tag_id;
		}

		$project = Core::model("Project");

		return json_encode($project->find_related($_POST["tags"], 9));
	}

	public function show($data){
		global $locale;
		global $CONFIG;

		$auth = Core::model("Auth");
		$project = Core::model("Project");
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
		if(!isset($project_obj["project_id"])){
			return "No project found for id ".$data[0];
			die();
		}
		
		$tags = array("tags" => array(), "tag_box_title" => false);
		array_push($tags["tags"], array("tag_id" => 12, "name" => "dafuq r u?"));
		
		$tag_box = Core::view("TagBox", $tags);
		
		$member_list = $project->get_positions((int)$data[0]);
		$member_list = array_map(function($entry) use ($auth, $project, $project_obj, $user){
			$result = array_merge($entry, array("user" => $auth->get_user($entry["user_id"])), array("project" => $project->get($entry["project_id"])));
			$flags = array();

			if($user){
				//If the currently shown user is the logged in user
				if(
					$project->exists_participation($project_obj["project_id"], $entry["user_id"]) &&
					$user["user_id"] == $result["user"]["user_id"] &&
					$project_obj["creator_id"] != $user["user_id"]
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

				if(
					$project->exists_participation_request($project_obj["project_id"], $user["user_id"]) &&
					!!DBEZ::find("project_participation_request", ["project_position_id" => $entry["project_position_id"], "user_id" => $user["user_id"]],["project_participation_request_id"]) &&
					empty($entry["user_id"])
				){
					array_push($flags, "CANCEL_REQUEST");
				}
			}

			$flags = array_unique($flags);

			return array("project_position" => $result, "flags" => $flags);
		}, $member_list);

		$user_can_edit = $user ? $project->user_has_right($project_obj["project_id"], $user["user_id"], "edit") : false;
		$user_is_participator = $user ? $project->exists_participation($project_obj["project_id"], $user["user_id"]) : false;
		
		$post = array();
		array_push($post, array("project" => array("title" => "Project Title", "id" => 1), "creator" => array("id" => 1, "name" => "Max"), "time" => "10:23", "content" => "This is a standard Post without title."));
		array_push($post, array("creator" => array("id" => 1, "name" => "Max"), "time" => "10:23", "content" => "This post is used to show up in projects. The usernamme is larger and the project title is gone.", "title" => ""));
		array_push($post, array("project" => array("title" => "Project Title", "id" => 1), "creator" => array("id" => 1, "name" => "Max"), "time" => "10:23", "content" => "
		You always wanted a title? Here it is! <br> And all that shit is in one View is that awesome or am I on drugs? :)<br>
		Also a max height of 390px is set here a demo:
		<br><br>
		Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.   
		<br><br>
		Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.   
		<br><br>
		Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer
		Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. <br>
		Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer", "title" => "Wow a title appears!"));
		
		$news_feed =Core::view("Post", array( "post" => $post));

		return Core::view("HtmlBase", array(	
			"title" => "Projectie - Driving Development", 
			"body" => Core::view("ContentWrapper", array(	
				"content" => Core::view("Project", array(
					"news_feed" => $news_feed, 
					"user_can_edit" => $user_can_edit,
					"project" => array(
						"project_id" => $project_obj["project_id"],
						"participator_count" => $project_obj["participator_count"],
						"title_picture" => $project_obj["title_picture"],
						"participators" => $project->get_participators($data[0]), 
						"description" => $project_obj["description"], 
						"subtitle" =>  $project_obj["subtitle"], 
						"title" => $project_obj["title"], 
						"header" => abspath("/public/images/default-banner.png"), 
						"time" => "14. 08. 2013 10:23", 
						"fav_count" => $project->get_fav_count($data[0]),
						"panels" => array(
							"public_conversation_panel" => array(
								"content" => Core::view("Conversation", array(
									"chat_title" => $locale["public_conversation"]." - ".$project_obj["title"],
									"chat_id" => $project_obj["public_chat_id"],
									"user_id" => $user["user_id"],
									"username" => $user["username"]
								)),
								"viewable" => true
							),
							"private_conversation_panel" => array(
								"content" => Core::view("Conversation", array(
									"chat_title" => $locale["private_conversation"]." - ".$project_obj["title"],
									"chat_id" => $project_obj["private_chat_id"],
									"user_id" => $user["user_id"],
									"username" => $user["username"]
								)),
								"viewable" => $user_is_participator
							),
							"members_panel" => array(
								"content" => Core::view("MemberPanel", array(
									"member_list" => Core::view_batch("ParticipationListTest", $member_list),
									"can_add_position" => $user_can_edit,
									"project_id" => $project_obj["project_id"]
								)),
								"viewable" => true
							),
							"requests_panel" => array(
								"content" => Core::view_batch("RequestEntry", $project->get_participation_requests($auth, $project_obj["project_id"])),
								"viewable" => $user_is_participator
							)
						)
					),
					"tag_box" => $tag_box
				)), 
				"user" => $user
				)
			), 
			"body_padding" => true,
			"current_user" => $user,
			"dark" => true
		));
	}

	public function createnew(){
		global $locale;
		global $CONFIG;

		$auth = Core::model("Auth");
		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
		
		$upload_picture_modal = Core::view("UploadPictureModal", "");
		
		
		$content = Core::view("CreateProject", array("upload_picture_modal" => $upload_picture_modal));

		$contentwrap = Core::view("ContentWrapper", array(	
			"content" => $content, 
			"user" => $user
		));

		$html = Core::view("HtmlBase", array(	
			"title" => "Projectie - Driving Development", 
			"body" => $contentwrap, 
			"body_padding" => true,
			"current_user" => $user,
			"dark" => true
		));

		return $html;
	}
}

?>