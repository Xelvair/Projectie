<?php
require_once("../core/Controller.php");

class ExampleController extends Controller{
	function index(){
		echo "This is the index page of the ExampleController class.";
	}

	function ExampleFunction(){
		echo "This is the example page of the ExampleController class.";

		$model = $this->model("ExampleModel");
		$model->test();
	}
}
?>