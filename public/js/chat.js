function chat_listen(controller_url, chat_id, callback){
	var last_load = Date.now();
	$.ajax({
		url : controller_url + "/get/" + chat_id + "/" + 10,
		async: false,
		success: function(result){
			result_obj = JSON.parse(result);
			for(var i = 0; i < result_obj.length; i++){
				callback(result_obj[i]);
			}
		}
	});

	setInterval(function(){
		console.log("Loading new chat msgs..");
		$.ajax({
			url : controller_url + "/get_since/" + chat_id + "/" + last_load,
			success: function(result){
				result_obj = JSON.parse(result);
				for(var i = 0; i < result_obj.length; i++){
					callback(result_obj[i]);
				}
			}
		});
		last_load = Date.now();
	},
	1000);
}

function chat_send(controller_url, chat_id, message){
	$.ajax({
		url : controller_url + "/send/" + chat_id,
		type: "POST",
		data: "message=" + message,
		success: function(result){console.log(result);}
	});
}