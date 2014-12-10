<?php

require_once(abspath_lcl("/core/Controller.php"));

class ChatController extends Controller{

	//$_POST["title"] : the title of the chat
	public function create_private(){
		$auth = $this->model("Auth");
		$user = $auth->get_current_user();

		if(json_decode($_POST["title"]) != null){
			return array("ERROR" => "ERR_TITLE_MUST_NOT_BE_JSON");
		}

		if(!$user){
			write_log(Logger::DEBUG, "Tried to create chat without login!");
			return array("ERROR" => "ERR_NOT_LOGGED_IN");
		}

		//If the user passed a valid title, use that
		//Else, set to null and let the chat model decide what title to use
		$title = (isset($_POST["title"]) && !empty($_POST["title"]) && trim($_POST["title"]) != "") ? $_POST["title"] : null; 

		$chat = $this->model("Chat");

		$chat_obj = $chat->create_private($user["id"], $title);

		$chat->add_user($chat_obj["chat_id"], $user["id"]);

		return json_encode($chat_obj);
	}

	//$_POST["user_id"] : the user that is to be added
	//$_POST["chat_id"] : the chat that we add the user to
	public function add_user(){
		if(!isset($_POST["user_id"]) ||
			 !isset($_POST["chat_id"]))
		{
			return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS"));
		}

		$auth = $this->model("Auth");

		$user_to_be_added = $auth->get_user($_POST["user_id"]);

		if(isset($user_to_be_added["ERROR"])){
			return json_encode(array("ERROR" => "ERR_USER_DOESNT_EXIST"));
		}
		
		$user = $auth->get_current_user();

		if(!$user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		$user_id = $_POST["user_id"];
		$chat_id = $_POST["chat_id"];

		$chat = $this->model("Chat");

		if($chat->is_creator($chat_id, $user["id"])){
			return json_encode($chat->add_user($chat_id, $user_id));
		} else {
			write_log(Logger::DEBUG, "Tried to add user without rights!");
			return json_encode(array("ERROR" => "ERR_NO_RIGHTS"));
		}
	}

	//$_POST["user_id"] : the user that is to be removed
	//$_POST["chat_id"] : the chat that we remove the user from
	public function remove_user(){
		if(!isset($_POST["user_id"]) ||
			 !isset($_POST["chat_id"]))
		{
			return json_encode(array("ERROR" => "ERR_INVALID_PARAMETERS"));
		}

		$auth = $this->model("Auth");
		
		$user = $auth->get_current_user();

		if(!$user){
			return json_encode(array("ERROR" => "ERR_NOT_LOGGED_IN"));
		}

		$user_id = $_POST["user_id"];
		$chat_id = $_POST["chat_id"];

		$chat = $this->model("Chat");

		if($chat->is_creator($chat_id, $user["id"])){
			return json_encode($chat->remove_user($chat_id, $user_id));
		} else {
			write_log(Logger::DEBUG, "Tried to remove user without rights!");
			return json_encode(array("ERROR" => "ERR_NO_RIGHTS"));
		}
	}

	public function get($data){
		// data[0] : chat id
		// data[1] : number of messages to be preloaded
		if(!isset($data[0]) || !isset($data[1])){
			return json_encode(array());
		}

		$chat = $this->model("Chat");

		$auth = $this->model("Auth");
		$logged_in_user = $auth->get_current_user();

		$requester_id = $logged_in_user ? $logged_in_user["id"] : null;

		return json_encode($chat->get($requester_id, $data[0], $data[1]));
	}

	public function get_new($data){
		// data[0] : chatsession id

		$auth = $this->model("Auth");
		$chat = $this->model("Chat");

		$logged_in_user = $auth->get_current_user();
		$requester_id = $logged_in_user ? $logged_in_user["id"] : null;

		return json_encode($chat->get_new($requester_id, $data[0]));
	}

	public function send($data){
		// data[0] : chatsession_id
		if(!isset($data[0]) || !isset($_POST["message"])){
			return;
		}

		$auth = $this->model("Auth");
		$chat = $this->model("Chat");

		$logged_in_user = $auth->get_current_user();

		$sender_id = $logged_in_user ? $logged_in_user["id"] : null;
		$chat->send($sender_id, $data[0], $_POST["message"]);
	}
	
	function index(){
		
		global $locale;
		global $CONFIG;

		$chat = $this->model("Chat");
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
		
		$footer_array = array("username" => "");
		$footer = $this->view("Footer", $footer_array);

		$chat_list = array();
		array_push($chat_list, $chat->get_chat(1));
		if($user){
			foreach($user["chat_participations"] as $chat_row){
				array_push($chat_list, $chat->get_chat($chat_row["chat_id"]));
			}
		}
		
		$content = $this->view("Chat", array("chat_list" => $chat_list, "user_id" => $user["id"], "username" => $user["username"], "footer" => $footer));

		$login_modal = $this->view("LoginModal", "");

		$contentwrap = $this->view("ContentWrapper", array(	"content" => $content, 
															"user" => ($user == null ? null : $user["username"]),
															"login_modal" => $login_modal));

		$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
												"body" => $contentwrap, 
												"body_padding" => true));
		return $html;
	}

}

?>