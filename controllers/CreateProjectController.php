<?php
require_once("../core/Controller.php");

class CreateProjectController extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

		$dbez = $this->model("DBEZ");
		$auth = $this->model("Auth", $dbez);
		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user["lang"]);

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
	
		
		
		$footer_array = array("user" => ($user == null ? null : $user["username"]));
		$footer = $this->view("Footer", $footer_array);
		
		$upload_picture_modal = $this->view("UploadPictureModal", "");
		
		
		$content = $this->view("CreateProject", array("upload_picture_modal" => $upload_picture_modal));

		$login_modal = $this->view("LoginModal", "");

		$contentwrap = $this->view("ContentWrapper", array(	"content" => $content, 
															"user" => ($user == null ? null : $user["username"]),
															"login_modal" => $login_modal,
															"footer" => $footer));

		$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
												"body" => $contentwrap, 
												"body_padding" => true,
												"current_user" => $user));
		return $html;
	}
}
?>