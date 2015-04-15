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
?>
