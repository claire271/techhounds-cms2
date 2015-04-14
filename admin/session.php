<?php
define("ADMIN_ROOT",$_SERVER['DOCUMENT_ROOT'] . "admin/");
session_save_path(ADMIN_ROOT . "session/");
session_start();
?>
