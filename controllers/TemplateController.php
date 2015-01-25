<?php
require_once("../core/Controller.php");

class TemplateController extends Controller{
	function TagBox(){
		global $locale;

		$dbez = Core::model("DBEZ");
		$auth = Core::model("Auth", $dbez);

		$user = $auth->get_current_user();

		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}

		return Core::view("TagBoxTemplate");
	}
}
?>