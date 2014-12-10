<?php
//PARAMETERS
//user_id : the id of the chat user
//user_name : the name of the chat user
?>
<!--Chat list-->
<div id='chat_list' style='margin-bottom: 30px;'>
<?php foreach($_DATA["chat_list"] as $chat){ ?>
<a data-chat-id='<?=$chat["chat_id"]?>' href='#'><?=$chat["title"]?></a><br>
<?php } ?>
<input id="create_new_chat" type="button" value="Create New Chat">
</div>

<script>
$("#chat_list a").on("click", function(e){
	var chat_id = $(e.target).data("chat-id");
	chatbox_obj.stop_listen();
	chatbox_obj = new Projectie.Messaging.Chatbox("<?=abspath('/chat/')?>", <?=$_DATA["user_id"]?>, '<?=$_DATA["username"]?>', chat_id, dispatch);
	$("#chatbox").empty();
});
$("#create_new_chat").on("click", function(){
	Projectie.Messaging.create_private_chat(prompt("Please enter the title of the chat you want to create!"));
})
</script>

<!--Chatbox-->
<input type="text" id="chat_input">
<button type="button" id="chat_send">Senden</button>
<button type="button" id="chat_add_user">Benutzer hinzufügen...</button>
<ul id="chatbox">
</ul>

<script>
function chat_dispatch(msg_obj, chat_type){
	$("#chatbox").append("<li>" + msg_obj.username + ": " + msg_obj.message + "</li>");
}

function chat_load(chat_obj){
	//void
}

var chatbox_obj = null;

$(document).ready(function(){
	chatbox_obj = new Projectie.Messaging.Chatbox("<?=abspath('/chat/')?>", <?=$_DATA["user_id"]?>, '<?=$_DATA["username"]?>', 1, chat_load, chat_dispatch);

	var send_msg = function(){
		chatbox_obj.send($("#chat_input").val());
		$("#chat_input").val("");
	}

	var add_user = function(){
		chatbox_obj.add_user(prompt("Please enter the id of the user you want to add!"));
	}

	$("#chat_input").on("keypress", function(e){
			if(e.which == 13){
				send_msg();
			}
		});
	$("#chat_send").on("click", send_msg);
	$("#chat_add_user").on("click", add_user);
});
</script>