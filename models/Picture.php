<?php

require_once(abspath_lcl("/core/Model.php"));

class Picture extends ActiveRecord{
	/* PUBLIC STATIC */
	public static function storeFromPost($file, $uploader_id){
		if($file["error"] != UPLOAD_ERR_OK){
			switch($file["error"]){
				case UPLOAD_ERR_INI_SIZE:
					throw new RuntimeException("ERR_FILE_TOO_BIG");
					break;
				case UPLOAD_ERR_FORM_SIZE:
					throw new RuntimeException("ERR_FILE_TOO_BIG");
					break;
				case UPLOAD_ERR_NO_FILE:
					throw new RuntimeException("ERR_NO_FILE");
					break;
				default:
					throw new RuntimeException("ERR_UNKNOWN");
					break;
			}
		}

		$file_ext = pathinfo($file["name"])["extension"];
		$file_hash = substr(md5_file($file["tmp_name"]), 0, 8);

		$file_path = "public/picture/".$file_hash.".".$file_ext;

		while(file_exists(abspath_lcl($file_path))){
			$file_hash = dechex(hexdec($file_hash) + 1);
			$file_path = "public/picture/".$file_hash.".".$file_ext;
		}

		move_uploaded_file($file["tmp_name"], abspath_lcl($file_path));

		$picture_id = DBEZ::insert("picture", ["file_path" => $file_path, "upload_date" => time(), "uploader_id" => $uploader_id]);

		return self::get($picture_id);
	}

}

?>