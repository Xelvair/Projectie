<?php

class Controller{
	protected function model($model, $data = null){
		//Determine filepath
		$model_filepath = self::modelFilepath($model);
		if(file_exists($model_filepath)){
			include_once($model_filepath);
		} else {
			write_log(Logger::ERROR, "Failed to load model file '".$model."'! Is the file named properly?");
			return null;
		}

		//Instantiate object
		if(class_exists($model)){
			$model_obj = new $model($data);
		} else {
			write_log(Logger::ERROR, "Failed to instantiate model class'".$model."'! Is the class named '".$model."'?");
		}

		return $model_obj;
	}

	private function modelFilepath($model){
		return "../models/".$model.".php";
	}
}
?>