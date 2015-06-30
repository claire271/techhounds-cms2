<?php
define("ERROR","");
require("util.php");

$count = isset( $_GET['count'] ) ? $_GET['count'] : 20;

?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Errors</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1>Errors</h1>
		<textarea style="height:40em; width:90%;"><?php echo htmlspecialchars(tailCustom(cleanPath(ADMIN_DIR . "/error.log"),$count)) ?></textarea>
		<br>
		<a href="index.php">Home</a>
	</body>
</html>
