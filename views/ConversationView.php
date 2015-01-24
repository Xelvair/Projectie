<?php 
global $locale;

require_once(abspath_lcl("/templates/chat_message.html"));

#PARAMETERS

#chat_title : title of the chat
#chat_id : id of the chat
#username : name of the chatting user
#user_id : id of the chatting user

$chat_dom_id = "chat_".rand();

$show_chat_title = isset($_DATA["chat_title"]);
?>

<div id="<?=$chat_dom_id?>" class="col-md-12 chat-col pull-left chat-wrapper">
  <?php if($show_chat_title){ ?>
  	<div class="chat-head text-center">
    	<div class="row">
        <div class="col-xs-12">
          <h2 class="pull-left"><span class="glyphicon glyphicon-comment"></span></h2>
          <h2 class="chat-title">
              <?=$_DATA["chat_title"]?>
          </h2>
        </div>
      </div>
    </div>
  <?php } ?>
	<div class="chat-box">
  	<ul class="chat">
      </ul>
  	</div>
  <div class="chat-footer">
  	<form>
 	    <div class="input-group">
        <textarea class="chat-input form-control custom-control no_right_border" placeholder="<?=$locale['write_something']?>..."rows="3" style="resize:none"></textarea>     
        <span class="chat-send input-group-addon btn btn-default"><?=$locale['send']?></span>
      </div>
    </form>
	</div>	
</div>

<script>
function chat_dispatch(msg_obj, chat_type){
  var msg_html = $("#chat-msg-prefab").clone().removeAttr("hidden").removeAttr("id");
  msg_html.find(".chat-msg-time").text("null");
  msg_html.find(".chat-msg-sender").text(msg_obj.username);
  msg_html.find(".chat-msg-content").text(msg_obj.message);

  if(msg_obj.user_id == <?=$_DATA["user_id"]?>){
    msg_html.find(".chat-img").removeClass("pull-right").addClass("pull-left");
    msg_html.find(".chat-msg-sender").removeClass("pull-right");
    msg_html.find(".chat-msg-time-container").addClass("pull-right");
  }

  $("#<?=$chat_dom_id?> .chat").append(msg_html);
  
  //if scroll height != current scroll
  $("#<?=$chat_dom_id?> .chat-box").animate({ scrollTop: $("#<?=$chat_dom_id?> .chat-box").scrollHeight}, 500);
}

function chat_load(chat_obj){
  //void
}

var chatbox_obj = null;

$(document).ready(function(){
  chatbox_obj = new Projectie.Messaging.Chatbox("<?=abspath('/chat/')?>", <?=$_DATA["user_id"]?>, '<?=$_DATA["username"]?>', <?=$_DATA["chat_id"]?>, chat_load, chat_dispatch);

  var send_msg = function(){
    console.log($("#<?=$chat_dom_id?> .chat-input").val());
    chatbox_obj.send($("#<?=$chat_dom_id?> .chat-input").val());
    $("#<?=$chat_dom_id?> .chat-input").val("");
  }

  $("#<?=$chat_dom_id?> .chat-input").on("keypress", function(e){
      if(e.which == 13){
        send_msg();
      }
    });
  $("#<?=$chat_dom_id?> .chat-send").on("click", send_msg);
});
</script>