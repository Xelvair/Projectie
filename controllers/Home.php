<?php
require_once("../core/Controller.php");

class Home extends Controller{
	function index(){
		echo "hi";
		$this->model("penis", 213, "shit");
	}
}
?>