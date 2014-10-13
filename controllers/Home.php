<?php
require_once("../core/Controller.php");

class Home extends Controller{
	function index(){
		return $this->view("HtmlBase", array("head" => "<title>this is the title!</title>", "body" => "this is the body"));
	}
}
?>