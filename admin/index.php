<?php
define("MAIN","");
require("util.php");

$users = Table::open("cms2-users");
if(!$users) {
	error_log("cms2-users table is missing!");
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
			$_SESSION["view"] = "simple";
			header( "Location: index.php" );
		}
	}
}

//Reject if no matches found
if(!isset($_SESSION["username"])) {
	header( "Location: login.php?action=fail" );
}

$pages_table = Table::open("cms2-pages");
if(!$pages_table) {
	error_log("cms2-pages table is missing!");
}
$pages = $pages_table->getRows();

if($action == "purge") {
	foreach($pages as $page) {
		unlink(ROOT_PATH . $page->out_path);
	}
	header( "Location: index.php" );
}

if($action == "regenerate") {
	foreach($pages as $page) {
		$template = file_get_contents(cleanPath(ROOT_PATH . $page->template_path));
		$output = template_match($template,"template_replace",$page);
		
		file_put_contents(ROOT_PATH . $page->out_path,$output);
		chmod(ROOT_PATH . $page->out_path,0664);
	}
	header( "Location: index.php" );
}

?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Admin</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<div class="body-container">
			<h1>Admin</h1>
			<p>Logged in as <b><?php echo isset($_SESSION["username"]) ? $_SESSION["username"] : "" ?></b></p>
			<a href="login.php?action=logout">Logout</a><br>
			<a href="users.php">Users</a><br>
			<a href="files.php">File Explorer</a><br>
			<a href="index.php?action=purge">Purge All Files</a><br>
			<a href="index.php?action=regenerate">Regenerate All Files</a><br>
		</div>
	</body>
</html>
