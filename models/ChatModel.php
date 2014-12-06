<?php

require_once(abspath_lcl("/core/Model.php"));

class ChatModel implements Model{

	public function create_public(){
		global $mysqli;

		if(!$mysqli->query("INSERT INTO chat (access) VALUES ('PUBLIC')")){
			wrtie_log(Logger::ERROR, "Failed to create private project chat instance!");
			return;
		}

		return $mysqli->insert_id;
	}

	public function create_private(){
		global $mysqli;

		if(!$mysqli->query("INSERT INTO chat (access) VALUES ('PRIVATE')")){
			wrtie_log(Logger::ERROR, "Failed to create private project chat instance!");
			return;
		}

		return $mysqli->insert_id;
	}

	public function can_participate($chat_id, $user_id){
		global $mysqli;

		//Check if it's a public chat
		$stmt_check_public = $mysqli->prepare("SELECT access FROM chat WHERE chat_id = ?");
		$stmt_check_public->bind_param("i", $chat_id);
		$stmt_check_public->execute();

		$stmt_check_public->bind_result($res_access);

		if(!$stmt_check_public->fetch()){
			return false;
		}

		//If public, return true right away
		if($res_access == "PUBLIC"){
			return true;
		}

		$stmt_check_public->close();

		//Else, check participation table
		$stmt_check_participation = $mysqli->prepare("SELECT chat_participation_id FROM chat_participation WHERE chat_id = ? AND participant_id = ?");
		$stmt_check_participation->bind_param("ii", $chat_id, $user_id);
		$stmt_check_participation->execute();

		$got_result = $stmt_check_participation->fetch() != null;

		$stmt_check_participation->close();

		return $got_result;

	}

	public function add_user($chat_id, $user_id){
		global $mysqli;

		$stmt = $mysqli->prepare("INSERT INTO chat_participation(chat_id, participant_id) VALUES(?, ?)");
		$stmt->bind_param("ii", $chat_id, $user_id);

		if(!$stmt->execute()){
			return array("ERR" => "ERR_DB_INSERT_FAILED");
		}

		return true;
	}

	public function remove_user($chat_id, $user_id){
		global $mysqli;

		$stmt = $mysqli->prepare("DELETE FROM chat_participation WHERE chat_id = ? AND participant_id = ?");
		$stmt->bind_param("ii", $chat_id, $user_id);
		
		if(!$stmt->execute()){
			return array("ERR" => "ERR_DB_DELETE_FAILED");
		}

		if($mysqli->affected_rows <= 0){
			return array("ERR" => "ERR_USER_DOESNT_EXIST");
		}

		return true;
	}

	public function send($sender_id, $chat_session_id, $message){
		global $mysqli;

		if(!isset($_SESSION["chatsessions"][$chat_session_id])){
			write_log(Logger::ERROR, "User #".$sender_id." sent message with invalid chatsession #".$chat_session_id."!");
			return array("ERROR" => "ERR_NO_CHATSESSION");
		}

		$chat_id = $_SESSION["chatsessions"][$chat_session_id]["chat_id"];

		if(!self::can_participate($chat_id, $sender_id)){
			write_log(Logger::WARNING, "User #".$sender_id." attempted to participate in chat #".$chat_id."! Not allowed!");
			return;
		}

		$stmt_send_msg = $mysqli->prepare("INSERT INTO chat_message(chat_id, user_id, chat_session_id, send_time, message) VALUES (?, ?, ?, ?, ?)");
		$stmt_send_msg->bind_param("iiiis", $chat_id, $sender_id, $chat_session_id, time(), htmlentities($message));
		return $stmt_send_msg->execute();
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
		return $result;
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