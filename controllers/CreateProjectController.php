<?php
require_once("../core/Controller.php");

class CreateProjectController extends Controller{
	function index(){
		global $locale;
		global $CONFIG;

		$auth = $this->model("Auth");
		$user = $auth->get_current_user();
		if($user != null){
			$locale_load_result = $locale->load($user->get_lang());

			if($locale_load_result == false){
				$locale->load("en-us");
			}
		} else {
			$locale->load("en-us");
		}
	
		
		
		$footer_array = array("username" => "");
		$footer = $this->view("Footer", $footer_array);
		
		$upload_picture_modal = $this->view("UploadPictureModal", "");
		
		
		$content = $this->view("CreateProject", array("footer" => $footer, "upload_picture_modal" => $upload_picture_modal));

		$login_modal = $this->view("LoginModal", "");

		$contentwrap = $this->view("ContentWrapper", array(	"content" => $content, 
															"user" => ($user == null ? null : $user->get_name()),
															"login_modal" => $login_modal));

		$html = $this->view("HtmlBase", array(	"title" => "Projectie - Driving Development", 
												"body" => $contentwrap, 
												"body_padding" => true));
		return $html;
	}
}
?>