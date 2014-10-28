<?php

class User{
	private $id;
	private $name;
	private $email;

	function __construct($id, $name, $email){
		$this->setId($id);
		$this->setName($name);
		$this->setEmail($email);
	}

	public function setId($val){
		$this->id = $val;
	}

	public function getId(){
		return $this->id;
	}

	public function setName($val){
		$this->name = $val;
	}

	public function getName(){
		return $this->name;
	}

	public function setEmail($val){
		$this->email = $val;
	}

	public function getEmail(){
		return $this->email;
	}
}

?>