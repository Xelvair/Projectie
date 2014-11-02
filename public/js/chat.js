// function dispatch(msg_obj, silent){}
// msg_obj : object with information about a message
// msg_obj.message : actual message
// msg_obj.user_id : user_id of the sender
// msg_obj.send_time : timestamp of the message

function Chatbox(url, reader_id, chat_id, dispatch_func){
	this.dispatch_func = dispatch_func;
	this.reader_id = reader_id;
	this.chat_id = chat_id;
	this.chatsession_id = null;
	this.is_listen = false;
	this.url = url;

	$.ajax({
		url : this.url + "/get/" + this.chat_id + "/" + 10,
		success : function(result){
			result_obj = JSON.parse(result);
			this.chatsession_id = result_obj.chatsession;
			for(var i = 0; i < result_obj.messages.length; i++){
				dispatch_func(result_obj.messages[i], true);
			}
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
					that.dispatch_func(result_obj[i], false);
				}
				if(that.is_listen){
					setTimeout(function(){that.load_new_messages();}, 1000);
				}
			}
		});
	}

	this.toggle_listen = function(){
		this.is_listen = !this.is_listen;
		if(this.is_listen == true){
			this.load_new_messages();
		}
	}

	this.send = function(msg){
		$.ajax({
			url : this.url + "/send/" + this.chat_id,
			method : "POST",
			data : "message=" + msg,
			success : function(){}
		});
	}

}