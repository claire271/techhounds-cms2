<?php
$action = isset( $_GET['action'] ) ? $_GET['action'] : "";
?>
<html>
	<head>
		<title>Admin Login</title>
	</head>
	<body>
		<h1>Admin Login</h1>
		<form action="index.php" method="POST">
			Username: <input type="text" name="username"><br>
			Password: <input type="password" name="password"><br>
			<input type="submit" value="Login">
		</form>
	</body>
</html>
