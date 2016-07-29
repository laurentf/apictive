<?php
namespace Model;

class User {

    function get() {
    	$f3 = \Base::instance();
    	echo 'get user'.$f3->get('PARAMS.id');
    }

    function post() {
    	echo 'post user';
    }

    function put() {
    	echo 'put user';
    }
    
	function delete() {
	    echo 'delete user';
    }
}
?>