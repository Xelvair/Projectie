<?php

require_once(abspath_lcl("/core/Model.php"));

class ChatModel implements Model{

	private $dbez;

	public function __construct($dbez){
		$this->dbez = $dbez;
	}

	public function create_public($creator_id, $title = null){
		$title = ($title ? $title : "New Public Chat");

		return $this->dbez->insert("chat", ["creator_id" => $creator_id, "title" => htmlentities($title), "access" => "PUBLIC"], DBEZ_INSRT_RETURN_ROW);
	}

	public function create_private($creator_id, $title = null){
		return $this->dbez->insert("chat", ["creator_id" => $creator_id, "title" => htmlentities($title), "access" => "PRIVATE"], DBEZ_INSRT_RETURN_ROW);
	}

	public function get_chat($chat_id){
		global $mysqli;

		$stmt_get_chat = $mysqli->prepare("
			SELECT 
				chat.chat_id AS chat_id,
				chat.title AS title,
				chat.creator_id AS creator_id,
				chat.access AS access,
				pp.project_participation_request_id,
				pp.project_Position_id AS participation_project_position_id,
				pp.user_id AS participation_user_id
			FROM chat 
			LEFT JOIN project_participation_request pp 
				ON (pp.chat_id = chat.chat_id)
			WHERE chat.chat_id = ? 
		");
		$stmt_get_chat->bind_param("i", $chat_id);
		$stmt_get_chat->execute();

		$result = $stmt_get_chat->get_result();

		if($result->num_rows <= 0){
			return array("ERROR" => "ERR_CHAT_DOESNT_EXIST");
		} else {
			return $result->fetch_assoc();
		}
	}

	public function can_participate($chat_id, $user_id){
		$chat = $this->dbez->find("chat", $chat_id, ["access"]);

		if(!$chat)
			return false;

		//If public, return true right away
		if($chat["access"] == "PUBLIC")
			return true;

		//Else, check participation table
		return !!$this->dbez->find("chat_participation", ["chat_id" => $chat_id, "participant_id" => $user_id], ["chat_participation_id"]);
	}

	public function is_creator($chat_id, $user_id){
		$chat = $this->dbez->find("chat", $chat_id, ["creator_id"]);

		return $chat["creator_id"] == $user_id;
	}

	public function add_user($chat_id, $user_id){
		if(self::can_participate($chat_id, $user_id)){
			return array("ERROR" => "ERR_IS_ALREADY_PARTICIPATOR");
		}

		return !!$this->dbez->insert("chat_participation", ["chat_id" => $chat_id, "participant_id" => $user_id]);
	}

	public function remove_user($chat_id, $user_id){
		return !!$this->dbez->delete("chat_participation", ["chat_id" => $chat_id, "user_id" => $user_id]);
	}

	public function send($sender_id, $chat_session_id, $message){
		if(!isset($_SESSION["chatsessions"][$chat_session_id])){
			write_log(Logger::ERROR, "User #".$sender_id." sent message with invalid chatsession #".$chat_session_id."!");
			return array("ERROR" => "ERR_NO_CHATSESSION");
		}

		$chat_id = $_SESSION["chatsessions"][$chat_session_id]["chat_id"];

		if(!self::can_participate($chat_id, $sender_id)){
			write_log(Logger::WARNING, "User #".$sender_id." attempted to participate in chat #".$chat_id."! Not allowed!");
			return;
		}

		return $this->dbez->insert("chat_message", [
			"chat_id" => $chat_id, 
			"user_id" => $sender_id, 
			"chat_session_id" => $chat_session_id, 
			"send_time" => time(), 
			"message" => htmlentities($message)
		]);
	}

	public function get($requester, $chat_id, $count){
		global $mysqli;

		if(!self::can_participate($chat_id, $requester)){
			write_log(Logger::WARNING, "User #".$user_id." attempted to participate in chat #".$chat_id."! Not allowed!");
			return array("ERROR" => "ERR_NO_PARTICIPATION_RIGHTS");
		}

		$stmt_get_msg = $mysqli->prepare("
			SELECT sub.chat_message_id, sub.user_id, sub.send_time, sub.message, u.username 
			FROM (SELECT chat_message_id, user_id, send_time, message FROM chat_message WHERE chat_id = ? ORDER BY chat_message_id DESC LIMIT ?) sub
			LEFT JOIN user u ON (sub.user_id = u.user_id) 
			ORDER BY sub.chat_message_id ASC
		");
		$stmt_get_msg->bind_param("ii", $chat_id, $count);
		$stmt_get_msg->execute();
		$stmt_get_msg->bind_result($res_chat_message_id, $res_user_id, $res_send_time, $res_message, $res_username);

		$last_msg_id = 0;
		$messages = array();
		while($stmt_get_msg->fetch()){
			array_push($messages, array("user_id" => $res_user_id, "send_time" => $res_send_time, "message" => $res_message, "username" => $res_username));
			$last_msg_id = $res_chat_message_id;
		}

		$chat_session_id = rand();

		$_SESSION["chatsessions"][$chat_session_id]["chat_id"] = $chat_id;
		$_SESSION["chatsessions"][$chat_session_id]["last_msg_id"] = $last_msg_id;

		$result = array("chat_session_id" => $chat_session_id, "messages" => $messages);
		return array_merge($result, self::get_chat($chat_id));
	}

	public function get_new($requester, $chat_session_id){
		global $mysqli;

		write_log(Logger::DEBUG, $requester);

		if(!isset($_SESSION["chatsessions"][$chat_session_id])){
			write_log(Logger::ERROR, "User #".$sender_id." requested messages from invalid chatsession #".$chat_session_id."!");
			return array("ERROR" => "ERR_NO_CHATSESSION");
		}

		$chat_id = $_SESSION["chatsessions"][$chat_session_id]["chat_id"];
		$last_msg_id = $_SESSION["chatsessions"][$chat_session_id]["last_msg_id"];

		if(!self::can_participate($chat_id, $requester)){
			write_log(Logger::WARNING, "User #".$user_id." attempted to participate in chat #".$chat_id."! Not allowed!");
			return array("ERROR" => "ERR_NO_PARTICIPATION_RIGHTS");
		}

		$stmt_get_msg = $mysqli->prepare("
			SELECT chat_message.chat_message_id, chat_message.user_id, chat_message.send_time, chat_message.message, user.username 
			FROM chat_message 
			LEFT JOIN user ON(chat_message.user_id = user.user_id) 
			WHERE chat_id = ? AND chat_message.chat_message_id > ? AND NOT (chat_message.user_id = ? AND chat_message.chat_session_id = ?)
			ORDER BY chat_message.chat_message_id ASC
		");
		$stmt_get_msg->bind_param("iiii", $chat_id, $last_msg_id, $requester, $chat_session_id);
		$stmt_get_msg->execute();
		$stmt_get_msg->bind_result($res_chat_message_id, $res_user_id, $res_send_time, $res_message, $res_username);

		$last_msg_id = 0;
		$messages = array();
		while($stmt_get_msg->fetch()){
			array_push($messages, array("user_id" => $res_user_id, "send_time" => $res_send_time, "message" => $res_message, "username" => $res_username));
			$last_msg_id = $res_chat_message_id;
		}

		if($last_msg_id != 0){
			$_SESSION["chatsessions"][$chat_session_id]["last_msg_id"] = $last_msg_id;
		}

		return $messages;
	}
}

?>