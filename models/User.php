<?php

class User extends ActiveRecord{
	public function getPicture(){
		return $this->getRelative("Picture", "picture_id");
	}
}

?>