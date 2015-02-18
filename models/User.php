<?php

class User extends ActiveRecord{
	public function getPicture(){
		return $this->getRelative("Picture", "picture_id");
	}

	public function getTags(){
		$tags = DBEZ::find("user_tag", ["user_id" => $this->user_id], ["tag_id"]);

		$result = [];
		foreach($tags as $tag){
			array_push($result, Tag::get((int)$tag["tag_id"]));
		}

		return $result;
	}
}

?>