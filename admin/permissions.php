<?php
require("util.php");

$action = isset( $_GET['action'] ) ? $_GET['action'] : "";

?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Permissions</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<h1>Permissions</h1>
		<?php
		if($action == "denied"){ ?>
			<div class="banner fail">Permission Denied!</div>
		<?php
		} ?>
		<textarea style="height:40em; width:90%;" placeholder="No Permissions to Show"><?php echo $_SESSION["permissions"]; ?></textarea>
		<br>
		<a href="index.php">Home</a>
	</body>
</html>
