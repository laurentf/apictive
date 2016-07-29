<?php

namespace Controller\Db;

class Db {
	function test(\Base $f3, $params) {
		// response init
		$response = new \stdClass();

		// response OK => build general info
		$response->status = '1';
		$response->message = $f3->get('success');
		$db = $f3->get('CONNECTION');
	
	    header('Content-Type: application/json');
		$response->data = $db->name();	

		die(json_encode($response));
			
	}

}