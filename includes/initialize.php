<?php
/**
*
thetecsup php codes.
@author Sedem Datsa <sedemdatsa69@gmail.com>
*/

//App version
define('THETECSUP_VERSION', '0.0.5');

$dir_loc = dirname(__FILE__);

//folders are not included because all these files are in the same folder.
//Require the database connection file.
require_once $dir_loc.'/Database.php';


//refactor the site title.
$site_title = "thetecsup";

//home page redirects for admin site.
$admin_home = $_SERVER['HTTP_HOST'] == 'localhost' ? '../home' : '/';


//Assign cookie names to be stored in a variable for maintainance.
if (isset($_COOKIE['tsp_username'])) {
	$cookie_username = $_COOKIE['tsp_username'];
}

if (isset($_COOKIE['tsp_token'])) {
	$cookie_token = $_COOKIE['tsp_token'];
}


//Load basic functions so everything after can use them.
require_once $dir_loc.'/functions.php';

//auto Load classes.
spl_autoload_register(function ($class_name) {
    global $dir_loc;

    require_once $dir_loc.'/'.$class_name . '.php';
});


//require classes that use construct function.load last to use classes before it.
require_once $dir_loc.'/Session.php';
require_once $dir_loc.'/Status.php';
require_once $dir_loc.'/fb_api.php';


//home page redirects for public site.
if ($session->is_logged_in()) {
	$home = "home";
}
elseif(!$session->is_logged_in() && $_SERVER['HTTP_HOST'] == 'localhost') {
	$home = "index";
}
else {
	$home = "/";
}

?>