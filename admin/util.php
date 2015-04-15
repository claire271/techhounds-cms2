<?php
require($_SERVER['DOCUMENT_ROOT'] . "db/db.php");

define("ADMIN_ROOT",$_SERVER['DOCUMENT_ROOT'] . "admin/");
session_save_path(ADMIN_ROOT . "session/");
session_start();

//Only for logout page
if(defined("LOGOUT")) {
	unset( $_SESSION['username'] );
}

?>
