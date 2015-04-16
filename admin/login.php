<?php
define("LOGOUT","");
require("util.php");
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
			<h2>TechHOUNDS</h2>
			<h3>Admin Login</h3>
			Username:<br>
			<input type="text" name="username"><br>
			Password:<br>
			<input type="password" name="password"><br><br>
			<input type="submit" value="Login">
		</form>
	</body>
</html>
