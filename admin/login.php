<?php
define("LOGOUT","");
require("util.php");
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Admin Login</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<?php
		if($action == "logout"){ ?>
			<div class="banner">Logged out!</div>
		<?php
		} else if ($action == "fail") { ?>
			<div class="banner fail">Invalid Credentials!</div>
		<?php
		}
		?>
		<form id="login" action="login-auth.php?action=login" method="POST">
			<h2>TechHOUNDS</h2>
			<h3>Admin Login</h3>
			Username:<br>
			<input type="text" name="username"><br>
			Password:<br>
			<input type="password" name="password"><br><br>
			<input type="submit" value="Login">
			<input type="hidden" name="dest" value="/index.php">
		</form>
	</body>
</html>
