<?php

require_once "../core/Controller.php";

class Install extends Controller{
	public function index($data){
		global $CONFIG;
		global $locale;
		global $mysqli;

		if(isset($_POST["submit"]) && isset($_POST["db_name"])){
			write_log(Logger::DEBUG, "Initializing DB...");
			if(!$mysqli->select_db($_POST["db_name"])){
				header("Location: ".abspath("/Install/index/failure"));
				exit();
			}
			$mysqli->multi_query(file_get_contents(abspath("sql/install.sql")));

			$CONFIG["db_name"] = $_POST["db_name"];
			write_log(Logger::DEBUG, file_put_contents("../projectie.cfg", json_encode($CONFIG)));

			header("Location: ".abspath("/Install/index/success"));
		} else {
			$locale->load("en-us");

			if(isset($data[0]) && $data[0] != ""){
				$action = $data[0];
			} else {
				$action = "default";
			}

			$content = $this->view("Install", array("action" => $action));
			$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
													"body" => $content,
													"body_padding" => false));
			return $html;
		}
	}
}

?>