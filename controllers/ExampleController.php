<?php
require_once("../core/Controller.php");

class ExampleController extends Controller{
	function index(){
		return "This is the index page of the ExampleController class.";
	}

	function ExampleFunction($data){
		$html = "";

		//It is important that every controller loads a locale before doing anything with $locale
		global $locale;
		if(isset($data[0])){
			$lang = $data[0];
		} else {
			$lang = "en-us";
		}
		$locale->load($lang);

		$html .= "This is the example page of the ExampleController class.";

		$model = $this->model("ExampleModel");

		$html .= $model->test();
		$html .= $this->view("ExampleView", ["examplestring" => "ExampleView Test String!"]);

		return $html;
	}
}
?>