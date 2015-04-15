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
  $hash = hash("md5", $_POST["password"]);
	
	$rows = $users->getRows();
	foreach($rows as $row) {
		if($name == $row->name &&
			 $hash == $row->hash) {
			$_SESSION["username"] = $name;
		}
	}
}

//Reject if no matches found
if(!isset($_SESSION["username"])) {
	header( "Location: login.php?action=fail" );
}

?>
<html>
	<head>
		<title>Admin</title>
	</head>
	<body>
		<h1>Admin</h1>
		<pre>
			<?php
			?>
		</pre>
	</body>
</html>
