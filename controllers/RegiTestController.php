<?php
require_once("../core/Controller.php");

class RegiTestController extends Controller{
	function index(){
		$user_session = Core::model("UserSession");
		$user_session->register("admin@projectie.com", "admin", "admin");

		$user_session->login("admin@projectie.com", "admin");

		$user_session->logout();

		$user_seesion->register();
	}
}
?>