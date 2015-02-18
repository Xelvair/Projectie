<?php
class Project extends ActiveRecord{
	public function getTitlePicture(){
		return $this->getRelative("Picture", "title_picture_id");
	}
}
?>