<div class="row" id="chatwindow-title-wrapper">
    <div class="col-md-12 text-center" id="chatwindow-title">
            <h1>Conversations</h1>
            <hr />
    </div>
</div>
<div class="row" id="chat-row">
	<div id="chat-wrapper" class="col-md-10 chat-col pull-left">
    	<div id="chat-head" class="text-center">
        	<div class="row">
                <div class="col-xs-1">
                    <h2><span class="glyphicon glyphicon-comment"></span></h2>
                </div>
                <div class="col-xs-10">
                    <h2 id="chat-title">
                        &nbsp;
                    </h2>
                </div>
            </div>
        </div>
    	<div id="chat-box">
        	<ul id="chat">
          </ul>
      	</div>
        <div id="chat-footer">
        	<form>
           	  <div class="input-group">
                <textarea class="form-control custom-control no_right_border" placeholder="Write something..."rows="3" style="resize:none" id="chat_input"></textarea>     
 			   <span class="input-group-addon btn btn-default" id="chat_send">Send</span>
                </div>
          </form>
  		</div>	
    </div>
	<div class="col-md-2 chat-col">
		<div id="select-chat-box">
			<div class="select-chat-box-head text-center">
				<h3>Conversations</h3>
			</div>
			<ul id="chat-list" class="sidebar-nav stacked-list">
				<?php foreach ($_DATA["chat_list"] as $chat){ ?>
					<li><a data-chat-id="<?=$chat["chat_id"]?>" onmouseover="curser_style(this);"><?=$chat["title"]?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>

<!-- Hiding a prefab for a message here -->
<li hidden id='chat-msg-prefab' class="right clearfix">
	<span class="chat-img pull-right">
		<img src="../public/images/default-profile-pic.png" alt="User Avatar" class="img-rounded" height="50" width="50"/>
	</span>
	<div class="chat-body clearfix">
		<div class="header">
			<small class="chat-msg-time-container text-muted"><span class="glyphicon glyphicon-time"></span><span class="chat-msg-time">-Time since message-</span></small>
			<strong class="chat-msg-sender pull-right primary-font">-Username-</strong>
		</div>
		<p class='chat-msg-content'>
			-Content-
		</p>
	</div>
</li>   

<script>
function dispatch(msg_obj, chat_type){
	var msg_html = $("#chat-msg-prefab").clone().removeAttr("hidden");
	msg_html.find(".chat-msg-time").text("null");
	msg_html.find(".chat-msg-sender").text(msg_obj.username);
	msg_html.find(".chat-msg-content").text(msg_obj.message);

	if(msg_obj.user_id == <?=$_DATA["user_id"]?>){
		msg_html.find(".chat-img").removeClass("pull-right").addClass("pull-left");
		msg_html.find(".chat-msg-sender").removeClass("pull-right");
		msg_html.find(".chat-msg-time-container").addClass("pull-right");
	}

	$("#chat").append(msg_html);
	
	//if scroll height != current scroll
	$("#chat-box").animate({ scrollTop: $('#chat-box')[0].scrollHeight}, 500);
}

function chat_load(chat_obj){
	$("#chat-title").text(chat_obj.title);
}

var chatbox_obj = null;

$(document).ready(function(){
	chatbox_obj = new Projectie.Messaging.Chatbox("<?=abspath('/chat/')?>", <?=$_DATA["user_id"]?>, "<?=$_DATA['username']?>", 1, chat_load, dispatch);
	chatbox_obj.toggle_listen();

	$("#chat_send").on("click", function(){
		chatbox_obj.send($("#chat_input").val());
		$("#chat_input").val("");
	});

	$("#chat-list li a").on("click", function(e){
		var chat_id = $(e.target).data("chat-id");
		chatbox_obj.stop_listen();
		$("#chat").empty();
		chatbox_obj = new Projectie.Messaging.Chatbox("<?=abspath('/chat/')?>", <?=$_DATA["user_id"]?>, "<?=$_DATA['username']?>", chat_id, chat_load, dispatch);
	});
	
});

function curser_style(button){
		button.style.cursor = 'pointer';
};
</script>
<?=$_DATA['footer']?>