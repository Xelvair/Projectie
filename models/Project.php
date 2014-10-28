<?php

class Project implements Model{
	public function create($creator_id, $info){
		// $info PARAMETERS
		// [title]: Title of the Project
		// [desc]: Description of the Project
	}
	public function get($id);
	public function set($creator_id, $id, $info);
	public function add_picture($id, $picture_id);
	public function remove_picture($id);

}

?>