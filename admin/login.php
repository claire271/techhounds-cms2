<?php
$action = isset( $_GET['action'] ) ? $_GET['action'] : "";
?>
<html>
	<head>
		<title>Admin Login</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<form id="login" action="index.php" method="POST">
			<h1>Admin Login</h1>
			Username:<br>
			<input type="text" name="username"><br>
			Password:<br>
			<input type="password" name="password"><br><br>
			<input type="submit" class="pull-right" value="Login">
		</form>
	</body>
</html>
