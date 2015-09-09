<?php
define("LOGIN","");
require("util.php");

$users = Table::open("cms2-users");
if(!$users) {
	error_log("cms2-users table is missing!");
	fatal_error();
}

//Login from the login page
if($action == "login") {
	$name = $_POST["username"];
	//$hash = hash("md5", $_POST["password"]);
	
	$rows = $users->getRows();
	foreach($rows as $row) {
		$hash = $row->salt . $_POST["password"];
		for($i = 0;$i < 100000;$i++) {
			$hash = hash("sha512",$hash);
		}
		if($name == $row->name &&
			 $hash == $row->hash) {
			$_SESSION["username"] = $name;
			$_SESSION["permissions"] = $row->permissions;
		}
	}
}

//Reject if no matches found
if(!isset($_SESSION["username"])) {
	$dest = isset($_POST["fdest"]) ? $_POST["fdest"] : "login.php?action=fail";
	redirect($dest);
}
else {
	$dest = isset($_POST["dest"]) ? $_POST["dest"] : "index.php";
	redirect($dest);
}
?>
