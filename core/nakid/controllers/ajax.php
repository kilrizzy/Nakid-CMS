<?php

class Ajax extends CI_Controller {
	public function __construct(){
		parent::__construct();
		global $data, $system, $user;
		$data = array();
	}
	function index(){
		global $data, $user;
	}
}

/* End of file ajax.php */