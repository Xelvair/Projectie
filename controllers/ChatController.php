<?php

require_once(abspath_lcl("/core/Controller.php"));

class ChatController extends Controller{

	public function get($data){
		if(!isset($data[0]) || !isset($data[1])){
			return json_encode(array());
		}

		$auth = $this->model("Auth");
		$chat = $this->model("Chat");

		$logged_in_user = $auth->get_current_user();
		$requester_id = $logged_in_user ? $logged_in_user->get_id() : null;
		
		return json_encode($chat->get_messages($requester_id, $data[0], $data[1]));
	}

	public function get_since($data){
		if(!isset($data[0]) || !isset($data[1])){
			return json_encode(array());
		}

		$auth = $this->model("Auth");
		$chat = $this->model("Chat");

		$logged_in_user = $auth->get_current_user();
		$requester_id = $logged_in_user ? $logged_in_user->get_id() : null;

		return json_encode($chat->get_messages_since($requester_id, $data[0], $data[1]));
	}

	public function send($data){
		if(!isset($data[0]) || !isset($_POST["message"])){
			return;
		}

		$auth = $this->model("Auth");
		$chat = $this->model("Chat");

		$logged_in_user = $auth->get_current_user();

		$sender_id = $logged_in_user ? $logged_in_user->get_id() : null;
		$chat->send($data[0], $sender_id, $_POST["message"]);

	}

}

?>