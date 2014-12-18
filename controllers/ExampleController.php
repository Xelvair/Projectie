<?php
require_once("../core/Controller.php");

class ExampleController extends Controller{
	function index(){
		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$user = $auth->get_current_user();
		return $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
										"body" => "fagit", 
										"body_padding" => true,
										"current_user" => $user));
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
		$html .= var_dump($_SERVER);

		$html .= $this->view("ExampleView", ["examplestring" => "ExampleView Test String!"]);

		return $html;
	}
}
?>