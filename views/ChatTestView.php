<form>
	<input type="text" id="chat_input">
	<button type="button" id="chat_send">Senden</button>
</form>
<ul id="chatbox">
</ul>

<script>
function dispatch(msg_obj){
	$("#chatbox").append("<li>" + msg_obj.username + ": " + msg_obj.message + "</li>");
}

var chatbox_obj = null;

$(document).ready(function(){
	chatbox_obj = new Chatbox("<?=abspath('/chat/')?>", 2, 1, dispatch);
	chatbox_obj.toggle_listen();

	$("#chat_send").on("click", function(){
		chatbox_obj.send($("#chat_input").val());
		$("#chat_input").val("");
	});
});
</script>