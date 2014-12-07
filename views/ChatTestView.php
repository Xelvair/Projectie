<?php
//PARAMETERS
//user_id : the id of the chat user
//user_name : the name of the chat user
?>
<input type="text" id="chat_input">
<button type="button" id="chat_send">Senden</button>
<ul id="chatbox">
</ul>
<p><?=nl2br(print_r($_DATA["chat_list"], true))?></p>

<script>
function dispatch(msg_obj){
	$("#chatbox").append("<li>" + msg_obj.username + ": " + msg_obj.message + "</li>");
}

var chatbox_obj = null;

$(document).ready(function(){
	chatbox_obj = new Chatbox("<?=abspath('/chat/')?>", <?=$_DATA["user_id"]?>, '<?=$_DATA["user_name"]?>', 1, dispatch);

	var send_msg = function(){
		chatbox_obj.send($("#chat_input").val());
		$("#chat_input").val("");
	}

	$("#chat_input").on("keypress", function(e){
			if(e.which == 13){
				send_msg();
			}
		});
	$("#chat_send").on("click", send_msg);
});
</script>