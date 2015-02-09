<?php

require_once("../core/Debug.php");
require_once("../core/Model.php");

class ProjectModel implements Model{
	public function create($creator_id, $info){
		// $info PARAMETERS
		// [title]: Title of the Project
		// [subtitle]: Subtitle of the Project
		// [description]: Description of the Project
		// [public_chat_id]: ID of public chat
		// [private_chat_id]: ID of private chat 

		if(	$info["title"] == "" ||
			$info["subtitle"] == "" ||
			$info["description"] == "" ||
			$info["public_chat_id"] == "" ||
			$info["private_chat_id"] == "")
		{
			write_log(Logger::WARNING, "Invalid parameters to project::create()!".callinfo());
			return array("ERROR" => "ERR_INSUFFICIENT_PARAMETERS");
		}

		$project_id = DBEZ::insert("project", [
			"creator_id" => $creator_id,
			"create_time" => time(),
			"title" => $info["title"],
			"subtitle" => $info["subtitle"],
			"description" => $info["description"],
			"title_picture_id" => 1,
			"public_chat_id" => $info["public_chat_id"],
			"private_chat_id" => $info["private_chat_id"],
			"active" => 1
		]);

		$project_position_id = DBEZ::insert("project_position", [
			"project_id" => $project_id,
			"user_id" => $creator_id,
			"job_title" => "Creator",
			"can_delete" => 1,
			"can_edit" => 1,
			"can_communicate" => 1,
			"can_add_participants" => 1,
			"can_remove_participants" => 1,
			"participator_since" => time()
		]);
		
		return array();
	}

	public function get_participators($project_id){
		global $mysqli;

		$stmt_get_participators = $mysqli->prepare("
			SELECT project_position_id, user.user_id, username, job_title, can_delete, can_edit, can_communicate, can_add_participants, can_remove_participants, participator_since
			FROM project_position
			LEFT JOIN user
				ON(project_position.user_id = user.user_id)
			WHERE project_id = ?
			AND project_position.user_id IS NOT NULL
		");
		$stmt_get_participators->bind_param("i", $project_id);
		$stmt_get_participators->execute();
		$result_get_participators = $stmt_get_participators->get_result();
		$result_array = $result_get_participators->fetch_all(MYSQLI_ASSOC);

		$result_array_keyasidx = array();
		foreach($result_array as $result_entry){
			$result_array_keyasidx[$result_entry["project_position_id"]] = $result_entry;
		}

		return $result_array_keyasidx;
	}

	public function get_positions($project_id){
		return DBEZ::find("project_position", ["project_id" => $project_id], "*");
	}

	public function get($id){
		$project = DBEZ::find("project", $id, ["project_id", "creator_id", "create_time", "title", "subtitle", "description", "title_picture_id", "public_chat_id", "private_chat_id"]);

		if(!$project){
			write_log(Logger::WARNING, "Failed to retrieve project #".$id." from databaase!");
			return array("ERROR" => "ERR_PROJECT_NONEXISTENT");
		}

		$project["participators"] = self::get_participators($id);
		$project["participator_count"] = sizeof($project["participators"]);
		$project["fav_count"] = self::get_fav_count($id);
		$project["title_picture"] = DBEZ::find("picture", $project["title_picture_id"], ["*"]);

		return $project;
	}

	public function exists($project_id){
		return !!DBEZ::find("project", $project_id, ["project_id"]);
	}

	public function set($creator_id, $id, $info){
		global $mysqli;

		$update_str = "";
		$is_first_attr = true;

		//Iterate through attributes of $info object,
		//Update for each existing valid attribute
		foreach($info as $attr_name => $attr_val){
			if(
				$attr_name == "creator_id" ||
				$attr_name == "create_time" ||
				$attr_name == "title" ||
				$attr_name == "subtitle" ||
				$attr_name == "description"
			)
			{
				$stmt = $mysqli->prepare("UPDATE project SET ".$attr_name." = ? WHERE project_id = ?");
				$stmt->bind_param("si", $attr_value, $id);
				
				if($stmt->execute()){
					write_log(Logger::ERROR, "Execution of query failed!".callinfo());
					throw new Exception("Execution of query failed!");
				}
			} else {
				write_log(Logger::WARNING, "Invalid attribute in info structure: '".$attr_name."'!".callinfo());
			}
		}
	}

	//REQUEST_TYPE: ENUM["USER_TO_PROJECT", "PROJECT_TO_USER"]
	public function request_participation($chat_obj, $project_position_id, $user_id, $request_type){
		global $mysqli;

		//Check parameters
		if(
			$request_type != "USER_TO_PROJECT" &&
			$request_type != "PROJECT_TO_USER"
		){
			write_log(Logger::ERROR, "Invalid parameter for function request_participation!".callinfo());
			return array("ERROR" => "ERR_INVALID_PARAMETERS");
		}

		$project_position_obj = DBEZ::find("project_position", ["project_position_id" => $project_position_id], ["project_id"])[0];

		$project_id = $project_position_obj["project_id"];

		if(self::exists_participation_request($project_id, $user_id)){
			write_log(Logger::ERROR, "User #".$user_id."has already requested participation at project #".$project_id."!".callinfo());
			return array("ERROR" => "ERR_PARTICIPATION_REQUEST_ALREADY_EXISTS");
		}

		if(self::exists_participation($project_id, $user_id)){
			write_log(Logger::ERROR, "User #".$user_id." is already participating in project #".$project_id."!".callinfo());
			return array("ERROR" => "ERR_PARTICIPATION_ALREADY_EXISTS");
		}

		$project = self::get($project_id);

		$title_obj = array(
			"type" => "recruitment",
			"project_id" => $project_id,
			"user_id" => $user_id
		);

		$recruitment_chat_title = json_encode($title_obj);

		$recruitment_chat = $chat_obj->create_private(0, $recruitment_chat_title);

		$project_participation_request_id = DBEZ::insert("project_participation_request", [
			"project_position_id" => $project_position_id,
			"user_id" => $user_id,
			"request_type" => $request_type,
			"chat_id" => $recruitment_chat["chat_id"]
		]);

		if(!$project_participation_request_id){
			return array("ERROR" => "ERR_DB_INSERT_FAILED");
		}

		$chat_obj->add_user($recruitment_chat["chat_id"], $user_id);

		$participators = self::get_participators($project_id);
		foreach($participators as $participator){
			if($participator["can_add_participants"] || 
				 $participator["can_communicate"])
			{
				$chat_obj->add_user($recruitment_chat["chat_id"], $participator["user_id"]);
			}
		}

		return array();
	}

	public function accept_participation_request($participation_req_id, $acceptor_id){
		global $mysqli;

		$result_participation = DBEZ::find("project_participation_request", ["project_participation_request_id" => $participation_req_id], ["project_position_id", "user_id", "request_type"]);

		//Check if participation request exists
		if(!$result_participation){
			write_log(Logger::WARNING, "Tried to accept non-existent participation request!".callinfo());
			return array("ERROR" => "ERR_PARTICIPATION_REQUEST_NONEXISTENT");
		}

		extract($result_participation[0], EXTR_OVERWRITE | EXTR_PREFIX_ALL, "res");

		$result_position = DBEZ::find("project_position", ["project_position_id" => $res_project_position_id], ["project_id"]);

		if(!$result_position){
			write_log(Logger::WARNING, "Tried to accept non-existent participation request!".callinfo());
			return array("ERROR" => "ERR_PARTICIPATION_REQUEST_NONEXISTENT");
		}

		extract($result_position[0], EXTR_OVERWRITE | EXTR_PREFIX_ALL, "res");

		//Determine who originally sent requesst
		if($res_request_type == "USER_TO_PROJECT"){
			if(self::user_has_right($res_project_id, $acceptor_id, "add_participants")){
				self::create_participation($participation_req_id);
			} else {
				write_log(Logger::WARNING, "User #".$acceptor_id." has no rights to accept participation request #".$participation_req_id."2!");
				return array("ERROR" => "ERR_NO_RIGHTS");
			}
		} else if($res_request_type == "PROJECT_TO_USER"){
			//If a project sent the request, check if the asked user is the currently logged in user
			if($acceptor_id == $res_user_id){
				self::create_participation($participation_req_id);
			}
		} else {
			write_log(Logger::ERROR, "Found corrupt db entry in project participation request table!");
			return array("ERROR" => "ERR_CORRUPT_DB_ENTRY");
		}

		return array();

	}

	public function cancel_participation_request($participation_id, $canceler_id){
		global $mysqli;

		$stmt_load_info = $mysqli->prepare("
			SELECT 
				ppr.project_participation_request_id as project_participation_request_id, 
				ppr.user_id as user_id, 
				pp.project_id as project_id
			FROM project_participation_request ppr
			LEFT JOIN project_position pp ON(ppr.project_position_id = pp.project_position_id)
			WHERE ppr.project_participation_request_id = ?
		");
		$stmt_load_info->bind_param("i", $participation_id);
		$stmt_load_info->execute();

		$result_load_info = $stmt_load_info->get_result();

		if($result_load_info->num_rows <= 0){
			return array("ERROR" => "ERR_NO_SUCH_PARTICIPATION_REQUEST");
		}

		$result_load_row = $result_load_info->fetch_assoc();

		$can_remove_from_project = self::user_has_right($canceler_id, $result_load_row["project_id"], "remove_participants");
		$is_requester = ($canceler_id == $result_load_row["user_id"]);

		if($can_remove_from_project || $is_requester){
			DBEZ::delete("project_participation_request", ["project_participation_request_id" => $participation_id]);
			return true;
		}

		return array("ERROR" => "ERR_NO_RIGHTS");

	}
	public function cancel_participation($canceled_position_id, $canceler_id){
		global $mysqli;

		$result_load_row = DBEZ::find("project_position", $canceled_position_id, ["project_id", "user_id"]);

		if(empty($result_load_row)){
			return array("ERROR" => "ERR_NO_SUCH_PROJECT_POSITION");
		}

		$result_project = DBEZ::find("project", $result_load_row["project_id"], ["creator_id"]);

		$is_said_participator = ($result_load_row["user_id"] == $canceler_id);
		$can_remove_from_project = self::user_has_right($canceler_id, $result_load_row["project_id"], "remove_participants");
		$is_project_creator = $result_load_row["user_id"] == $result_project["creator_id"];

		if($is_project_creator){
			return array("ERROR" => "ERR_CREATOR_CANT_LEAVE");
		}

		if($can_remove_from_project || $is_said_participator){
			$stmt_cancel_participation = $mysqli->prepare("UPDATE project_position SET user_id = NULL, participator_since = NULL WHERE project_position_id = ?");
			$stmt_cancel_participation->bind_param("i", $canceled_position_id);
			return json_encode($stmt_cancel_participation->execute());
		}

		return array("ERROR" => "ERR_NO_RIGHTS");
	}

	public function user_has_right($project_id, $user_id, $right){
		$result = DBEZ::find("project_position", ["project_id" => $project_id, "user_id" => $user_id], "*");

		if(!$result){
			return false;
		}

		$row = $result[0];

		if(isset($row["can_".$right])){
			return (boolean)$row["can_".$right];
		} else {
			throw new Exception("No entry found for 'can_".$right."' in database!");
		}
	}

	public function create_participation($participation_req_id){
		global $mysqli;

		$result = DBEZ::find("project_participation_request", $participation_req_id, ["project_position_id", "user_id"]);

		if(!$result){
			write_log(Logger::ERROR, "Participation request #".$participation_req_id."doesn't exist!");
			return array("ERROR" => "ERR_NO_SUCH_PARTICIPATION_REQUEST");
		}

		extract($result, EXTR_OVERWRITE | EXTR_PREFIX_ALL, "res");

		if(self::exists_participation($res_project_position_id, $res_user_id)){
			write_log(Logger::ERROR, "User #".$res_user_id." is already participating in project #".$res_project_id."!".callinfo());
			return array("ERROR" => "ERR_PARTICIPATION_ALREADY_EXISTS");
		}

		//Remove entry in participation request table, since we're going to create the real deal now
		DBEZ::delete("project_participation_request", $participation_req_id);

		$curr_time = time();

		$stmt_update_position = $mysqli->prepare("UPDATE project_position SET user_id = ?, participator_since = ? WHERE project_position_id = ?");
		$stmt_update_position->bind_param("iii", $res_user_id, $curr_time, $res_project_position_id);
		$stmt_update_position->execute();
	}

	public function get_participation_requests($auth, $project_id){
		global $mysqli;

		$stmt_get_participation_requests = $mysqli->prepare("SELECT ppr.* FROM project_participation_request ppr LEFT JOIN project_position pp ON(ppr.project_position_id = pp.project_position_id) WHERE pp.project_id = ?");
		$stmt_get_participation_requests->bind_param("i", $project_id);
		$stmt_get_participation_requests->execute();

		$result = $stmt_get_participation_requests->get_result()->fetch_all(MYSQLI_ASSOC);

		foreach($result as &$result_entry){
			write_log(Logger::DEBUG, print_r($result_entry, true));
			$result_entry["user"] = $auth->get_user($result_entry["user_id"]);
			$result_entry["project_position"] = DBEZ::find("project_position", $result_entry["project_position_id"], ["project_position_id", "job_title"]);
		}

		return $result;
	}

	public function add_position($project_id, $position_title, $adder_id){
		if(!self::user_has_right($project_id, $adder_id, "edit")){
			return array("ERROR" => "ERR_NO_RIGHTS");
		} 

		return DBEZ::insert("project_position", [
			"project_id" => $project_id,
			"job_title" => $position_title,
			"can_edit" => 0,
			"can_delete" => 0,
			"can_communicate" => 0,
			"can_add_participants" => 0,
			"can_remove_participants" => 0
		]);
	}
	
	public function remove_position($project_position_id, $remover_id){
		$position_result = DBEZ::find("project_position", $project_position_id, ["project_id", "user_id"]);

		if(!$position_result){
			return array("ERROR" => "ERR_NO_SUCH_PROJECT_POSITION");
		}

		if($position_result["user_id"] != null){
			return array("ERROR" => "ERR_POSITION_IS_FILLED");
		}

		if(self::user_has_right($position_result["project_id"], $remover_id, "edit")){
			DBEZ::delete("project_position", ["project_position_id" => $project_position_id]);
		} else {
			return array("ERROR" => "ERR_NO_RIGHTS");
		}
	}

	public function get_all_projects(){
		return DBEZ::find("project", [], "*");
	}

	public function exists_participation_request($project_id, $user_id){
		global $mysqli;

		$stmt_check_participation = $mysqli->prepare("
			SELECT project_participation_request_id 
			FROM project_participation_request ppr 
			LEFT JOIN project_position pp 
				ON(pp.project_position_id = ppr.project_position_id) 
			WHERE ppr.user_id = ? 
			AND pp.project_id = ?");

		$stmt_check_participation->bind_param("ii", $user_id, $project_id);
		$stmt_check_participation->execute();
		
		$result = $stmt_check_participation->get_result();

		return ($result->num_rows > 0);
	}

	public function exists_participation($project_id, $user_id){
		return !!DBEZ::find("project_position", ["project_id" => $project_id, "user_id" => $user_id], ["project_position_id"]);
	}

	public function tag($tag_model, $project_id, $tag){
		if(!self::exists($project_id)){
			return array("ERROR" => "ERR_PROJECT_NONEXISTENT");
		}

		if(gettype($tag) != "integer" && gettype($tag) != "string"){
			throw new InvalidArgumentException("request_tag function expects integer or string. ".gettype($tag)." given");
		}

		//Get tag from database
		$tag_entry = $tag_model->request_tag($tag);

		if(sizeof($tag_entry) <= 0){
			return array("ERROR" => "ERR_TAG_NONEXISTENT");
		}

		if(self::is_tagged($tag_model, $project_id, $tag)){
			return array("ERROR" => "ERR_PROJECT_ALREADY_TAGGED");
		}

		$tag_id = $tag_entry["tag_id"];

		DBEZ::insert("project_tag", ["project_id" => $project_id, "tag_id" => $tag_id]);
	
		return array();
	}

	public function is_tagged($tag_model, $project_id, $tag){
		if(gettype($tag) != "integer" && gettype($tag) != "string"){
			throw new InvalidArgumentException("request_tag function expects integer or string. ".gettype($tag)." given");
		}

		$tag_entry = $tag_model->get_tag($tag);

		if(sizeof($tag_entry) <= 0){
			throw new InvalidArgumentException("request_tag function cannot find '".$tag."' in database!");
		}

		$tag_id = $tag_entry["tag_id"];

		return !!DBEZ::find("project_tag", ["project_id" => $project_id, "tag_id" => $tag_id], ["project_tag_id"]);
	}

	public function untag($tag_model, $project_id, $tag){
		$tag_entry = $tag_model->get_tag($tag);

		$tag_id = $tag_entry["tag_id"];

		return !!DBEZ::delete("project_tag", ["project_id" => $project_id, "tag_id" => $tag_id]);
	}

	public function get_tags($project_id){
		global $mysqli;

		$query_get_tags = $mysqli->prepare("SELECT t.tag_id AS tag_id, t.name AS name FROM project_tag pt LEFT JOIN tag t ON(pt.tag_id = t.tag_id) WHERE pt.project_id = ?");
		$query_get_tags->bind_param("i", $project_id);
		$query_get_tags->execute();
		$result = $query_get_tags->get_result();

		$query_get_tags->close();

		return $result->fetch_all(MYSQLI_ASSOC);
	}

	public function favorite($project_id, $user_id){
		$is_faved_already = !!DBEZ::find("project_fav", ["project_id" => $project_id, "user_id" => $user_id], ["project_fav_id"]);

		if($is_faved_already)
			return array("ERROR" => "ERR_PROJECT_ALREADY_FAVED");

		$project = self::get($project_id);

		if(isset($project["ERROR"]))
			return array("ERROR" => "ERR_PROJECT_NONEXISTENT");

		return !!DBEZ::insert("project_fav", ["project_id" => $project_id, "user_id" => $user_id, "fav_time" => time()]);
	}

	public function unfavorite($project_id, $user_id){
		return DBEZ::delete("project_fav", ["project_id" => $project_id, "user_id" => $user_id]);
	}

	public function get_fav_count($project_id){
		global $mysqli;

		$stmt_fav_count = $mysqli->prepare("
			SELECT COUNT(project_id) AS fav_count 
			FROM project_fav 
			WHERE project_id = ? 
			GROUP BY project_id"
		);
		$stmt_fav_count->bind_param("i", $project_id);
		$stmt_fav_count->execute();
		$stmt_fav_count->bind_result($res_fav_count);
		
		if(!$stmt_fav_count->fetch())
			return 0;

		$stmt_fav_count->close();

		return $res_fav_count;
	}

	public function get_news($project_news_id){
	  return DBEZ::find("project_news", $project_news_id, "*");
	}
 
  //$info["project_id"] : id of the project the news belong to
  //$info["author_id"] : id of the author
  //$info["content"] : content of the news
  public function post_news($info){
    if(!isset($info["project_id"]) ||
       !isset($info["author_id"]) ||
       !isset($info["title"]) ||
       !isset($info["content"]))
    {
      print_r(json_encode($info));
      throw new InvalidArgumentException();
    }

    extract($info);

    if(!self::user_has_right($project_id, $author_id, "communicate")){
      return array("ERROR" => "ERR_NO_RIGHTS");
    }

    return DBEZ::insert("project_news", [
      "project_id" => $project_id,
      "author_id" => $author_id,
      "post_time" => time(),
      "title" => htmlentities($title),
      "content" => htmlentities($content)
    ], DBEZ_INSRT_RETURN_ROW);
  }
 
        //$info["project_news_id"] : id of the news entry
        //$info["editor_id"] : id of the editor
        //$info["content"] : content of the news
  public function edit_news($info){
    global $mysqli;

    if(!isset($info["project_news_id"]) ||
       !isset($info["editor_id"]) ||
       !isset($info["content"]) ||
       !isset($info["title"]))
    {
      throw new InvalidArgumentException();
    }

    $news = DBEZ::find("project_news", (int)$info["project_news_id"], "*");

    if(!$news){
      return array("ERROR" => "ERR_NEWS_NONEXISTENT");
    }

    extract($info);

    if(!self::user_has_right($news["project_id"], $editor_id, "communicate")){
    	return array("ERROR" => "ERR_NO_RIGHTS");
    }

    $edit_time = time();
    $escaped_content = htmlentities($content);
    $escaped_title = htmlentities($title);

    $query_edit_news = $mysqli->prepare("UPDATE project_news SET content = ?, title = ?, last_editor = ?, last_edit_time = ? WHERE project_news_id = ?");
    $query_edit_news->bind_param("ssiii", $escaped_content, $escaped_title, $editor_id, $edit_time, $info["project_news_id"]);
    if(!$query_edit_news->execute()){
      return array("ERROR" => "ERR_DB_UPDATE_FAILED");
    }

    return self::get_news($info["project_news_id"]);
  }

	//$info["project_news_id"] : id of the news entry
	//$info["remover_id"] : id of the remover
	public function remove_news($info){
		global $mysqli;

		if(!isset($info["project_news_id"]) ||
			 !isset($info["remover_id"]))
		{
			throw new InvalidArgumentException();
		}

		$news = DBEZ::find("project_news", (int)$info["project_news_id"], "*");

		if(!$news){
			return array("ERROR" => "ERR_NEWS_NONEXISTENT");
		}

		extract($info);

		if(!self::user_has_right($news["project_id"], $remover_id, "communicate")){
			return array("ERROR" => "ERR_NO_RIGHTS");
		}

		$stmt_remove_news = $mysqli->prepare("UPDATE project_news SET active = 0 WHERE project_news_id = ?");
		$stmt_remove_news->bind_param("i", $info["project_news_id"]);
		if(!$stmt_remove_news->execute()){
			return array("ERROR" => "ERR_DB_DELETE_FAILED");
		}

		return array();
	}

	public function get_tag_matches($project_id, $search_tags){
		$project_tags_raw = self::get_tags($project_id);

		$project_tags = array_map(function($entry){
			return (integer)$entry["tag_id"];
		}, $project_tags_raw);

		$matches = array_intersect($project_tags, $search_tags);

		return sizeof($matches);
	}

	public function find_related($search_tags, $count){
		$projects = DBEZ::find("project", [], ["project_id"]);

		foreach ($projects as &$project){
			$project = self::get($project["project_id"]);
			$project["tag_matches"] = self::get_tag_matches($project["project_id"], $search_tags);
			$project["fav_count"] = self::get_fav_count($project["project_id"]);
			$project["relevance"] = $project["tag_matches"] * (log($project["fav_count"] + 1) * 5 + 1); //5: GAIN value, needs to be lower for more users
		}

		usort($projects, function($lhs, $rhs){
			return $lhs["relevance"]>$rhs["relevance"] ? -1 : 1;
		});

		$projects = array_slice($projects, 0, $count);

		return $projects;
	}

	public function get_new_projects($count){
		global $mysqli;

		$stmt_get_projects = $mysqli->prepare("SELECT project_id FROM project ORDER BY create_time DESC LIMIT ?");
		$stmt_get_projects->bind_param("i", $count);
		$stmt_get_projects->execute();

		$project_rows = $stmt_get_projects->get_result()->fetch_all(MYSQL_ASSOC);

		$projects = array();
		foreach($project_rows as $project_row){
			array_push($projects, $this->get($project_row["project_id"]));
		}

		return $projects;
	}

	public function add_picture($id, $picture_id){}
	public function remove_picture($id){}

}

?>