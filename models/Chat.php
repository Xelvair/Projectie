<?php

require_once(abspath_lcl("/core/Model.php"));

class Chat implements Model{
	public function create_public(){
		global $mysqli;

		$mysqli->query("INSERT INTO chat (access) VALUES ('PUBLIC')");
		return $mysqli->insert_id;
	}

	public function create_project_specific($project_id){
		global $mysqli;

		$stmt = $mysqli->prepare("INSERT INTO chat (access, access_id) VALUES ('PUBLIC', ?)");
		$stmt->bind_param("i", $project_id);
		$stmt->execute();
		return $mysqli->insert_id;
	}

	public function send($chat_id, $user_id, $message){
		global $mysqli;

		//Check to see whether the chat exists and it can be accessed by the user
		$stmt_check_access = $mysqli->prepare("SELECT access, access_id FROM chat WHERE chat_id = ?");
		$stmt_check_access->bind_param("i", $chat_id);
		$stmt_check_access->execute();
		$stmt_check_access->bind_result($res_access, $res_access_id);
		
		//if no chat was retrieved, fail and return
		if(!$stmt_check_access->fetch()){
			write_log(Logger::WARNING, "Failed to send message to chat: Chat #".$chat_id." doesn't exist!");
			return false;
		}

		switch ($res_access){
			case "PUBLIC":
				$stmt_send_message = $mysqli->prepare("INSERT INTO chatmessage (chat_id, user_id, send_time, message) VALUES (?, ?, ?, ?)");
				$stmt_send_message->bind_param("iiis", $chat_id, $user_id, time(), $message);
				return true;
				break;
			case "PROJECT_SPECIFIC":
				//TODO: Check more access
				write_log(Logger::WARNING, "Tried to send message to project-specific chat. Feature not implemented yet.");
				return false;
				break;
		}
	}

	public function get_messages($requester, $chat_id, $count){
		global $mysqli;

		//TODO: Check access

		$stmt_get_msg = $mysqli->prepare("SELECT user_id, send_time, message FROM (SELECT user_id, send_time, message FROM chatmessage WHERE chat_id = ? ORDER BY send_time DESC LIMIT ?) sub ORDER BY send_time ASC");
		$stmt_get_msg->bind_param("ii", $chat_id, $count);
		$stmt_get_msg->execute();
		$stmt_get_msg->bind_result($res_user_id, $res_send_time, $res_message);

		$messages = array();
		while($stmt_get_msg->fetch()){
			array_push($messages, array("user_id" => $res_user_id, "send_time" => $res_send_time, "message" => $res_message));
		}
		return $messages;
	}

	public function get_messages_since($requester, $chat_id, $time){

		//TODO: Check access

		$stmt_get_msg = $mysqli->prepare("SELECT user_id, send_time, message FROM chatmessage WHERE chat_id = ? AND send_time >= ? ORDER BY send_time ASC");
		$stmt_get_msg->bind_param("ii", $chat_id, $time);
		$stmt_get_msg->execute();
		$stmt_get_msg->bind_result($res_user_id, $res_send_time, $res_message);

		$messages = array();
		while($stmt_get_msg->fetch()){
			array_push($messages, array("user_id" => $res_user_id, "send_time" => $res_send_time, "message" => $res_message));
		}
		return $messages;
	}
}

?>