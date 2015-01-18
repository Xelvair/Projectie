Projectie.Project.sendParticipationRequestToProject = function(project_position_id){
		$.post(
		Projectie.server_addr + "/project/request_participation",
		{
			requester_id : Projectie.current_user.user_id,
			requestee_id : project_position_id,
			request_type : "USER_TO_PROJECT"
		},
		function(result){
			if(result.ERROR){
				console.log("sendParticipationRequestToProject: "+result.ERROR);
			}
		}
	);
}

Projectie.Project.cancelParticipation = function(project_position_id){
	$.post(
		Projectie.server_addr + "/project/cancel_participation",
		{
			project_position_id : project_position_id
		},
		function(result){
			if(result.ERROR){
				console.log("cancelParticipation: "+result.ERROR);
			}
		}
	);
}

Projectie.Project.acceptParticipationRequest = function(participation_request_id){
	$.post(
		Projectie.server_addr + "/project/accept_participation_request",
		{
			project_participation_request_id : participation_request_id
		},
		function(result){
			if(result.ERROR){
				console.log("acceptParticipationRequest: "+result.ERROR);
			}
		}
	);
}

Projectie.Project.cancelParticipationRequest = function(participation_request_id){
	$.post(
		Projectie.server_addr + "/project/cancel_participation_request",
		{
			project_participation_request_id : participation_request_id
		},
		function(result){
			if(result.ERROR){
				console.log("cancelParticipationRequest: "+result.ERROR);
			}
		}
	);
}

Projectie.Project.removePosition = function(project_position_id){
	$.post(
		Projectie.server_addr + "/project/remove_position",
		{
			project_position_id : project_position_id
		},
		function(result){
			if(result.ERROR){
				console.log("removePosition: "+result.ERROR);
			}
		}
	);
}

Projectie.Project.addPosition = function(project_id, new_position_title){
	$.post(
		Projectie.server_addr + "/project/add_position",
		{
			project_id : project_id,
			position_title : new_position_title
		},
		function(result){
			if(result.ERROR){
				console.log("addPosition: "+result.ERROR);
			}
		}
	);
}