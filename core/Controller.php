<?php

class Controller{
	protected function model($model){
		//Determine filepath
		$model = $model."Model";
		$model_filepath = self::modelFilepath($model);
		if(file_exists($model_filepath)){
			include_once($model_filepath);
		} else {
			write_log(Logger::ERROR, "Failed to load model file '".$model."'! Is the file named properly?");
			return null;
		}

		//Instantiate object
		if(class_exists($model)){
			$ref = new ReflectionClass($model);
			$params = func_get_args();
			array_shift($params);
  			return $ref->newInstanceArgs($params);
		} else {
			write_log(Logger::ERROR, "Failed to instantiate model class'".$model."'! Is the class named '".$model."'?");
			return null;
		}

		return $model_obj;
	}

	protected function view($view, $data){
		$view = $view."View";
		$view_filepath = self::viewFilepath($view);

		if(!file_exists($view_filepath)){
			write_log(Logger::ERROR, "Failed to load view file '".$view."'! Is the file named properly?");
			return null;
		}

		$_DATA = $data;

		ob_start();

		include($view_filepath);

		$content = ob_get_clean();

		return $content;
	}

	private function viewFilepath($view){
		return "../views/".$view.".php";
	}

	private function modelFilepath($model){
		return "../models/".$model.".php";
	}
}
?>