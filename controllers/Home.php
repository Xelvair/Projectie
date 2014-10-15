<?php
require_once("../core/Controller.php");

class Home extends Controller{
	function index(){
		global $locale;

		$locale->load("en-us");

		$content = $this->view("ContentWrapper", array("content" => "content."));
		$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
																						"body" => $content));
		return $html;
	}
}
?>