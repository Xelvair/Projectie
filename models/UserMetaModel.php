<?php

require_once("../core/Model.php");

class UserMetaModel implements Model{
	public function generate($project_model, $user_model){
		if(
			get_class($user_model) 		!= "UserModel" 			|| 
			get_class($project_model) != "ProjectModel"
		){
			return print_r(array(get_class($user_model), get_class($project_model)), true);
			write_log(Logger::DEBUG, "Invalid objects passed to UserMetaModel::generate()!".callinfo());
			return array("ERROR" => "ERR_INVALID_PARAMETERS");
		}

		$user_meta = array(
			"id" => $user_model->get_id(),
			"create_time" => $user_model->get_create_time(),
			"username" => $user_model->get_name(),
			"email" => $user_model->get_email(),
			"lang" => $user_model->get_lang(),
			"created_projects" => array(),
			"project_participations" => array()
		);

		$user_meta["created_projects"] = $project_model->get_created_projects($user_model->get_id());
		$user_meta["project_participations"] = $project_model->get_user_participations($user_model->get_id());

		return $user_meta;
	}
}

?>