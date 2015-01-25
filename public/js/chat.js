// function dispatch(msg_obj, silent){}
// msg_obj : object with information about a message
// msg_obj.message : actual message
// msg_obj.user_id : user_id of the sender
// msg_obj.send_time : timestamp of the message

Projectie.Messaging.ChatType = {
		PRELOAD: 1,
		SELF: 2,
		OTHER: 3
}

Projectie.Messaging.create_private_chat = function(chat_title){
		$.post(
			Projectie.server_addr + "/chat/create_private",
			{title : chat_title}
		).done(function(result){location.reload();});
}

Projectie.Messaging.Chatbox = function(url, reader_id, reader_username, chat_id, load_func, dispatch_func){
	this.load_func = load_func;
	this.dispatch_func = dispatch_func;
	this.chat_creator_id = 0;
	this.reader_username = reader_username;
	this.reader_id = reader_id;
	this.chat_id = chat_id;
	this.chatsession_id = null;
	this.is_listen = false;
	this.url = url;
	this.timeout_inst = null;

	$.ajax({
		url : this.url + "/get/" + this.chat_id + "/" + 10,
		success : function(result){
			console.log(result);
			result_obj = JSON.parse(result);
			this.chatsession_id = result_obj.chat_session_id;
			this.chat_creator_id = result_obj.creator_id;
			load_func(result_obj);
			for(var i = 0; i < result_obj.messages.length; i++){
				dispatch_func(result_obj.messages[i], Projectie.Messaging.ChatType.PRELOAD);
			}
			this.toggle_listen();
		}.bind(this)
	});

	this.is_user_creator = function(){
		return (this.reader_id == this.chat_creator_id)
	}

	this.add_user = function(user_id){
		if(!this.is_user_creator()){
			alert("You have no rights to do that!");
			return;
		}

		$.post(
			Projectie.server_addr + "/chat/add_user",
			{chat_id : this.chat_id,
			 user_id : user_id}
		).done(function(e){
			result_obj = JSON.parse(e);
			if(!!result_obj.ERROR){
				alert(result_obj.ERROR);
			} else {
				alert("User was added!");
			}
		});
	}

	this.load_new_messages = function(){
		var that = this;
		$.ajax({
			url : this.url + "/get_new/" + this.chatsession_id,
			success : function(result){
				var result_obj = JSON.parse(result);
				for(var i = 0; i < result_obj.length; i++){
					that.dispatch_func(result_obj[i], Projectie.Messaging.ChatType.OTHER);
				}
				if(that.is_listen){
					this.timeout_inst = setTimeout(function(){that.load_new_messages();}, 1000);
				}
			}.bind(this)
		});
	}

	this.stop_listen = function(){
		this.is_listen = false;
		clearTimeout(this.timeout_inst);
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
		this.dispatch_func(msg_obj, Projectie.Messaging.ChatType.OWN);
	}

}