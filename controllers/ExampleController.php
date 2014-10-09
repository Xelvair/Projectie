<?php
require_once("../core/Controller.php");

class ExampleController extends Controller{
	function index(){
		echo "This is the index page of the ExampleController class.";
	}

	function ExampleFunction($data){
		//It is important that every controller loads a locale before doing anything with $locale
		global $locale;
		if(isset($data[0])){
			$lang = $data[0];
		} else {
			$lang = "en-us";
		}
		$locale->load($lang);

		echo "This is the example page of the ExampleController class.";

		$model = $this->model("ExampleModel");
		$model->test();
		print_r($this->view("ExampleView", ["examplestring" => "ExampleView Test String!"]));
	}
}
?>