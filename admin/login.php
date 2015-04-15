<?php
define("LOGOUT","");
require("util.php");

//Need to eventually use action to show banners
?>
<html>
	<head>
		<title>Admin Login</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1>Admin Login</h1>
		<form id="login" action="index.php?action=login" method="POST">
			Username: <input type="text" name="username"><br>
			Password: <input type="password" name="password"><br>
			<input type="submit" value="Login">
		</form>
	</body>
</html>
