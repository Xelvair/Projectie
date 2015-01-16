Projectie.Project.removeUser = function(project_id, user_id){

}

Projectie.Project.sendParticipationRequest = function(project_position_id){

}

Projectie.Project.acceptParticipationRequest = function(participation_request_id){
	$.post(
		Projectie.server_addr + "/project/accept_participation",
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
		Projectie.server_addr + "/project/cancel_participation",
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