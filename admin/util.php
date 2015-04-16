<?php
require($_SERVER['DOCUMENT_ROOT'] . "/db/db.php");

define("ADMIN_ROOT",$_SERVER['DOCUMENT_ROOT'] . "/admin/");
session_save_path(ADMIN_ROOT . "session/");
session_start();

//Only for login/logout page
if(defined("LOGOUT")) {
	unset( $_SESSION['username'] );
}
//See if logged on already and not on main page
else if(!defined("MAIN") && !isset($_SESSION["username"])) {
	header( "Location: login.php?action=fail" );
}

$action = isset( $_GET['action'] ) ? $_GET['action'] : "";

//Some utility functions

//Always returns with no trailing slash
function cleanPath($path) {
	$path = str_replace("\\","/",$path);
	$leading = substr($path,0,1) == "/";

  $substrings = explode("/",$path);
	
  $parts = array();
	foreach($substrings as $substring) {
    if($substring == "..") {
      array_pop($parts);
    }
    else if($substring == ".") {}
    else if($substring == "") {}
    else {
      array_push($parts,$substring);
    }
  }

  return ($leading ? "/" : "") . implode("/",$parts);
}
?>

