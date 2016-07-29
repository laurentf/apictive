<?php 
// composer autoloader for required packages and dependencies
require_once('lib/autoload.php');

/** @var \Base $f3 */
$f3 = \Base::instance();
// config 
$f3->config('config/globals.ini');

// F3 autoloader for application business code
$f3->set('AUTOLOAD', 'app/');

/*$f3->set('ONERROR',function($f3){
  die($f3->get('error')['route']);
});*/

$options = array(
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // generic attribute
    \PDO::ATTR_PERSISTENT => TRUE,  // we want to use persistent connections
    \PDO::MYSQL_ATTR_COMPRESS => TRUE, // MySQL-specific attribute
);

//$db = new \DB\SQL('mysql:host=localhost;port=3306;dbname=snegine','root','', $options);

// Persistence 
$db = new \DB\Jig('jig/');
$f3->set('CONNECTION', $db);

$f3->route('GET /image/transform/@operations','Controller\Image\Image->transform');
$f3->route('POST /image/transform/@operations','Controller\Image\Image->transform');

$f3->route('GET /db/test','Controller\Db\Db->test');

$f3->run();