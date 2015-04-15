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
		<?php
		if($action == "logout"){ ?>
			<div class="banner">Logged out!</div>
		<?php
		} else if ($action == "fail") { ?>
			<div class="banner fail">You fail!</div>
		<?php
		}
		?>
		<form id="login" action="index.php?action=login" method="POST">
			<h1>Admin Login</h1>
			Username:<br>
			<input type="text" name="username"><br>
			Password:<br>
			<input type="password" name="password"><br><br>
			<input type="submit" class="pull-right" value="Login">
		</form>
	</body>
</html>
