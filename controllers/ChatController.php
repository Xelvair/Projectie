<?php

require_once(abspath_lcl("/core/Controller.php"));

class ChatController extends Controller{

	public function get($data){
		// data[0] : chat id
		// data[1] : number of messages to be preloaded
		if(!isset($data[0]) || !isset($data[1])){
			return json_encode(array());
		}

		$auth = $this->model("Auth");
		$chat = $this->model("Chat");

		$logged_in_user = $auth->get_current_user();
		$requester_id = $logged_in_user ? $logged_in_user["id"] : null;

		do{
			$chatsession_id = substr(md5(rand()), 0, 8);
		} while(isset($_SESSION["chatsessions"][$chatsession_id]));

		$_SESSION["chatsessions"][$chatsession_id] = array(
			"chat_id" => $data[0],
			"last_load" => time()
		);

		$result_array = array("chatsession" => $chatsession_id, 
													"messages" => $chat->get_messages($requester_id, $data[0], $data[1]));

		return json_encode($result_array);
	}

	public function get_new($data){
		// data[0] : chatsession id

		if(	!isset($data[0]) ||
				!isset($_SESSION["chatsessions"]) || 
				!isset($_SESSION["chatsessions"][$data[0]]))
		{
			return json_encode(array());
		}

		$chatsession = $_SESSION["chatsessions"][$data[0]];

		$auth = $this->model("Auth");
		$chat = $this->model("Chat");

		$logged_in_user = $auth->get_current_user();
		$requester_id = $logged_in_user ? $logged_in_user["id"] : null;

		$result = json_encode($chat->get_messages_since($requester_id, $chatsession["chat_id"], $chatsession["last_load"]));
	
		$_SESSION["chatsessions"][$data[0]]["last_load"] = time();

		return $result;
	}

	public function send($data){
		if(!isset($data[0]) || !isset($_POST["message"])){
			return;
		}

		$auth = $this->model("Auth");
		$chat = $this->model("Chat");

		$logged_in_user = $auth->get_current_user();

		$sender_id = $logged_in_user ? $logged_in_user["id"] : null;
		$chat->send($data[0], $sender_id, $_POST["message"]);

	}
	
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
		
		$footer_array = array("username" => "");
		$footer = $this->view("Footer", $footer_array);
		
		$content = $this->view("Chat", array("footer" => $footer));

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