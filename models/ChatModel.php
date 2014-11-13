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

		write_log(Logger::DEBUG, $res_access);

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

	public function send($chat_id, $user_id, $message){
		global $mysqli;

		if(!self::can_participate($chat_id, $user_id)){
			write_log(Logger::WARNING, "User #".$user_id." attempted to participate in chat #".$chat_id."! Not allowed!");
			return;
		}

		$stmt_send_msg = $mysqli->prepare("INSERT INTO chatmessage(chat_id, user_id, send_time, message) VALUES (?, ?, ?, ?)");
		$stmt_send_msg->bind_param("iiis", $chat_id, $user_id, time(), htmlentities($message));
		return $stmt_send_msg->execute();
	}

	public function get_messages($requester, $chat_id, $count){
		global $mysqli;

		if(!self::can_participate($chat_id, $requester)){
			write_log(Logger::WARNING, "User #".$user_id." attempted to participate in chat #".$chat_id."! Not allowed!");
			return array("ERROR" => "ERR_NO_PARTICIPATION_RIGHTS");
		}

		$stmt_get_msg = $mysqli->prepare("
			SELECT sub.user_id, sub.send_time, sub.message, u.username 
			FROM (SELECT user_id, send_time, message FROM chatmessage WHERE chat_id = ? ORDER BY send_time DESC LIMIT ?) sub
			LEFT JOIN user u ON (sub.user_id = u.user_id) 
			ORDER BY send_time ASC
		");
		$stmt_get_msg->bind_param("ii", $chat_id, $count);
		$stmt_get_msg->execute();
		$stmt_get_msg->bind_result($res_user_id, $res_send_time, $res_message, $res_username);

		$messages = array();
		while($stmt_get_msg->fetch()){
			array_push($messages, array("user_id" => $res_user_id, "send_time" => $res_send_time, "message" => $res_message, "username" => $res_username));
		}
		return $messages;
	}

	public function get_messages_since($requester, $chat_id, $time){
		global $mysqli;

		if(!self::can_participate($chat_id, $requester)){
			write_log(Logger::WARNING, "User #".$user_id." attempted to participate in chat #".$chat_id."! Not allowed!");
			return array("ERROR" => "ERR_NO_PARTICIPATION_RIGHTS");
		}

		$stmt_get_msg = $mysqli->prepare("
			SELECT chatmessage.user_id, chatmessage.send_time, chatmessage.message, user.username 
			FROM chatmessage 
			LEFT JOIN user ON(chatmessage.user_id = user.user_id) 
			WHERE chat_id = ? AND chatmessage.send_time >= ? 
			ORDER BY chatmessage.send_time ASC
		");
		$stmt_get_msg->bind_param("ii", $chat_id, $time);
		$stmt_get_msg->execute();
		$stmt_get_msg->bind_result($res_user_id, $res_send_time, $res_message, $res_username);

		$messages = array();
		while($stmt_get_msg->fetch()){
			array_push($messages, array("user_id" => $res_user_id, "send_time" => $res_send_time, "message" => $res_message, "username" => $res_username));
		}
		return $messages;
	}
}

?>