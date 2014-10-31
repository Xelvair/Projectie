<?php

require_once(abspath_lcl("/core/Controller.php"));

class ChatController extends Controller{

	public function get($data){
		$auth = $this->model("Auth");
		$chat = $this->model("Chat");

		$logged_in_user = $auth->getLoggedInUser();
		
		return json_encode($chat->get_messages($logged_in_user->getId(), $data[0], $data[1]));
	}

}

?>