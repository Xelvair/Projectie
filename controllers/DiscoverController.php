<?php

require_once(abspath_lcl("/core/Controller.php"));

class DiscoverController extends Controller{
	public function index(){
		global $locale;
		global $CONFIG;

		$auth = Core::model("Auth");
		$project = Core::model("Project");

		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}

		return Core::view("HtmlBase", [
			"title" => $locale["discover_projectie"],
			"body" => Core::view("ContentWrapper", [
				"user" => $user,
				"content" => Core::view("Discover")
			]),
			"body_padding" => true
		]);
	}
}

?>