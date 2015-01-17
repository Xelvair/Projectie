Projectie.Project.removeUser = function(project_id, user_id){

}

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