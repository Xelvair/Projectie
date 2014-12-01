<div class="row" id="chat-title-wrapper">
    <div class="col-md-12 text-center" id="chat-title">
            <h1>Conversations</h1>
            <hr />
    </div>
</div>

<div class="row" id="chat-row">
	<div class="col-md-4 chat-col">
    	<div id="select-chat-box">
        	<ul class="sidebar-nav stacked-list">
            	<li><a>Jack Sparrow</a></li>
                <li><a>Max da Boss</a></li>
                <li><a>Admin</a></li>
                <li><a>Jack Sparrow</a></li>
                <li><a>Max da Boss</a></li>
                <li><a>Admin</a></li>
                <li><a>Jack Sparrow</a></li>
                <li><a>Max da Boss</a></li>
                <li><a>Admin</a></li>
                <li><a>Jack Sparrow</a></li>
                <li><a>Max da Boss</a></li>
                <li><a>Admin</a></li>
            </ul>
        
        </div>
    </div>
	<div id="chat-wrapper" class="col-md-8 chat-col">
    	<div id="chat-head" class="text-center">
        	<div class="row">
                <div class="col-xs-1">
                    <h2><span class="glyphicon glyphicon-comment"></span></h2>
                </div>
                <div class="col-xs-10">
                    <h2>
                        Jack Sparrow
                    </h2>
                </div>
            </div>
        </div>
    	<div id="chat-box">
        	<ul id="chat">
            	<li class="left clearfix">
                		<span class="chat-img pull-left">
                            <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-rounded" />
                        </span>
                        <div class="chat-body clearfix">
                            <div class="header">
                                <strong class="primary-font">Jack Sparrow</strong> <small class="pull-right text-muted">
                                    <span class="glyphicon glyphicon-time"></span>12 mins ago</small>
                            </div>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
                                dolor, quis ullamcorper ligula sodales.
                            </p>
                        </div>
                </li>
                
             	 <li class="right clearfix">
                 		<span class="chat-img pull-right">
                            <img src="http://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar" class="img-rounded" />
                        </span>
                        <div class="chat-body clearfix">
                            <div class="header">
                                <small class=" text-muted"><span class="glyphicon glyphicon-time"></span>13 mins ago</small>
                                <strong class="pull-right primary-font">Bhaumik Patel</strong>
                            </div>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
                                dolor, quis ullamcorper ligula sodales.
                            </p>
                        </div>
                    </li>
                             	<li class="left clearfix">
                		<span class="chat-img pull-left">
                            <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-rounded" />
                        </span>
                        <div class="chat-body clearfix">
                            <div class="header">
                                <strong class="primary-font">Jack Sparrow</strong> <small class="pull-right text-muted">
                                    <span class="glyphicon glyphicon-time"></span>12 mins ago</small>
                            </div>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
                                dolor, quis ullamcorper ligula sodales.
                            </p>
                        </div>
                </li>
                
             	 <li class="right clearfix">
                 		<span class="chat-img pull-right">
                            <img src="http://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar" class="img-rounded" />
                        </span>
                        <div class="chat-body clearfix">
                            <div class="header">
                                <small class=" text-muted"><span class="glyphicon glyphicon-time"></span>13 mins ago</small>
                                <strong class="pull-right primary-font">Bhaumik Patel</strong>
                            </div>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
                                dolor, quis ullamcorper ligula sodales.
                            </p>
                        </div>
                    </li>
                             	<li class="left clearfix">
                		<span class="chat-img pull-left">
                            <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-rounded" />
                        </span>
                        <div class="chat-body clearfix">
                            <div class="header">
                                <strong class="primary-font">Jack Sparrow</strong> <small class="pull-right text-muted">
                                    <span class="glyphicon glyphicon-time"></span>12 mins ago</small>
                            </div>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
                                dolor, quis ullamcorper ligula sodales.
                            </p>
                        </div>
                </li>
                
             	 <li class="right clearfix">
                 		<span class="chat-img pull-right">
                            <img src="http://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar" class="img-rounded" />
                        </span>
                        <div class="chat-body clearfix">
                            <div class="header">
                                <small class=" text-muted"><span class="glyphicon glyphicon-time"></span>13 mins ago</small>
                                <strong class="pull-right primary-font">Bhaumik Patel</strong>
                            </div>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare
                                dolor, quis ullamcorper ligula sodales.
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
</div>
<script>
function dispatch(msg_obj){
	$("#chat").append("<li>"+msg_obj.username + ": " + msg_obj.message + "</li>");
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
<?=$_DATA['footer']?>