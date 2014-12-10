<div class="row" id="chat-title-wrapper">
    <div class="col-md-12 text-center" id="chat-title">
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
                    <h2 id="chat-partner-name">
                        Jack Sparrow
                    </h2>
                </div>
            </div>
        </div>
    	<div id="chat-box">
        	<ul id="chat">
            	<li class="left clearfix">
					<span class="chat-img pull-left">
						<img src="../public/images/default-profile-pic.png" alt="User Avatar" class="img-rounded" height="50" width="50"/>
					</span>
					<div class="chat-body clearfix">
						<div class="header">
							<strong class="primary-font">Jack Sparrow</strong>
							<small class="pull-right text-muted">
								<span class="glyphicon glyphicon-time"></span>
								12 mins ago
							</small>
						</div>
						<p>
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
							dolor, quis ullamcorper ligula sodales.
						</p>
					</div>
                </li>
             	<li class="right clearfix">
					<span class="chat-img pull-right">
						<img src="../public/images/default-profile-pic.png" alt="User Avatar" class="img-rounded" height="50" width="50"/>
					</span>
					<div class="chat-body clearfix">
						<div class="header">
							<small class=" text-muted"><span class="glyphicon glyphicon-time"></span>13 mins ago</small>
							<strong class="pull-right primary-font">Bhaumik Patel</strong>
						</div>
						<p>
							Lorem 
						</p>
					</div>
				</li>                 
            </ul>
      	</div>
        <div id="chat-footer">
        	<form>
           	  <div class="input-group">
                <textarea class="form-control custom-control" placeholder="Write something..."rows="3" style="resize:none" id="chat_input"></textarea>     
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
			<ul class="sidebar-nav stacked-list">
				<li><a onmouseover="curser_style(this);">Jack Sparrow</a></li>
				<li><a onmouseover="curser_style(this);">Max da Boss</a></li>
				<li><a onmouseover="curser_style(this);">Admin</a></li>
				<li><a onmouseover="curser_style(this);">Jack Sparrow</a></li>
				<li><a onmouseover="curser_style(this);">Max da Boss</a></li>
				<li><a onmouseover="curser_style(this);">Admin</a></li>
				<li><a onmouseover="curser_style(this);">Jack Sparrow</a></li>
				<li><a onmouseover="curser_style(this);">Max da Boss</a></li>
			</ul>
		</div>
	</div>
</div>
<script>
function dispatch(msg_obj){
 alert(msg_obj.message);
	$("#chat").append("<li>"+msg_obj.username + ": " + msg_obj.message + "</li>");
}

var chatbox_obj = null;

$(document).ready(function(){
	chatbox_obj = new Chatbox("<?=abspath('/chat/')?>", 2, 1, dispatch);
	chatbox_obj.toggle_listen();

	$("#chat_send").on("click", function(){
		alert($("#chat_input").val());
		chatbox_obj.send($("#chat_input").val());
		$("#chat_input").val("");
	});
	
	$('.stacked-list').find('a').on('click', function(){
		$('.stacked-list').find('a').removeClass('item-active');
		$(this).addClass('item-active');
		load_conversation(this);
	});
});

function load_conversation(element){

$('#chat-partner-name').text($(element).text());
	
}

function curser_style(button){
		button.style.cursor = 'pointer';
};
</script>
<?=$_DATA['footer']?>