<?php

class User{
	private $id;
	private $createTime;
	private $name;
	private $email;

	function __construct($id, $create_time, $name, $email){
		$this->setId($id);
		$this->setCreateTime($create_time);
		$this->setName($name);
		$this->setEmail($email);
	}

	public function setId($val){
		$this->id = $val;
	}

	public function getId(){
		return $this->id;
	}

	public function setCreateTime($val){
		$this->createTime = $val;
	}

	public function getCreateTime(){
		return $this->createTime;
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