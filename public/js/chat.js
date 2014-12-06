// function dispatch(msg_obj, silent){}
// msg_obj : object with information about a message
// msg_obj.message : actual message
// msg_obj.user_id : user_id of the sender
// msg_obj.send_time : timestamp of the message

function Chatbox(url, reader_id, reader_username, chat_id, dispatch_func){
	this.ChatType = {
		PRELOAD: 1,
		SELF: 2,
		OTHER: 3
	}

	this.dispatch_func = dispatch_func;
	this.reader_username = reader_username;
	this.reader_id = reader_id;
	this.chat_id = chat_id;
	this.chatsession_id = null;
	this.is_listen = false;
	this.url = url;

	$.ajax({
		url : this.url + "/get/" + this.chat_id + "/" + 10,
		success : function(result){
			result_obj = JSON.parse(result);
			this.chatsession_id = result_obj.chat_session_id;
			for(var i = 0; i < result_obj.messages.length; i++){
				dispatch_func(result_obj.messages[i], this.ChatType.PRELOAD);
			}
			this.toggle_listen();
		}.bind(this)
	});

	this.load_new_messages = function(){
		var that = this;
		console.log("loading new msgs.");
		$.ajax({
			url : this.url + "/get_new/" + this.chatsession_id,
			success : function(result){
				var result_obj = JSON.parse(result);
				for(var i = 0; i < result_obj.length; i++){
					that.dispatch_func(result_obj[i], this.ChatType.OTHER);
				}
				if(that.is_listen){
					setTimeout(function(){that.load_new_messages();}, 1000);
				}
			}.bind(this)
		});
	}

	this.toggle_listen = function(){
		this.is_listen = !this.is_listen;
		if(this.is_listen == true){
			this.load_new_messages();
		}
	}

	this.send = function(msg){
		if(msg.trim() == ""){
			return;
		}

		$.ajax({
			url : this.url + "/send/" + this.chatsession_id,
			method : "POST",
			data : "message=" + msg,
			success : function(){}
		});
		var msg_obj = {user_id: this.reader_id, send_time: Math.floor(Date.now() / 1000), message: msg, username: this.reader_username};
		this.dispatch_func(msg_obj, this.ChatType.OWN);
	}

}