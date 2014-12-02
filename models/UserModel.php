<?php

class UserModel{
	private $id;
	private $createTime;
	private $name;
	private $email;
	private $is_admin;

	function __construct($id, $create_time, $name, $email, $lang, $is_admin){
		$this->set_id($id);
		$this->set_create_time($create_time);
		$this->set_name($name);
		$this->set_email($email);
		$this->set_lang($lang);
		$this->set_is_admin($is_admin);
	}

	public function set_id($val){
		$this->id = $val;
	}

	public function get_id(){
		return $this->id;
	}

	public function set_create_time($val){
		$this->createTime = $val;
	}

	public function get_create_time(){
		return $this->createTime;
	}

	public function set_name($val){
		$this->name = $val;
	}

	public function get_name(){
		return $this->name;
	}

	public function set_email($val){
		$this->email = $val;
	}

	public function get_email(){
		return $this->email;
	}

	public function set_lang($val){
		$this->lang = $val;
	}

	public function get_lang(){
		return $this->lang;
	}

	public function set_is_admin($val){
		$this->is_admin = $val;
	}

	public function get_is_admin(){
		return $this->is_admin;
	}
}

?>