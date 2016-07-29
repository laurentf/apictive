<?php
namespace Model;

class Image {

    function get() {
    	$f3 = \Base::instance();
    	echo 'get image'.$f3->get('PARAMS.id');
    }

    function post() {
    	echo 'post image';
    }

    function put() {
    	echo 'put image';
    }
    
	function delete() {
	    echo 'delete image';
    }
}
?>