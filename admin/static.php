<?php
require("util.php");

$path = cleanPath($_GET['path']);
$file_path = ROOT_PATH . $path;

$contents = file_get_contents($file_path);

if($action == "save") {
	file_put_contents($file_path,$_POST["contents"]);
	header( "Location: static.php?path=" . $path);
}

?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Static Editor</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<div class="body-container">
			<h1><?php echo basename($path) ?></h1>
			<form action="static.php?action=save&path=<?php echo $path ?>" method="POST">
				<textarea name="contents" placeholder="The contents of the file" style="height: 30em; width: 80%;"><?php echo htmlspecialchars($contents) ?></textarea><br>
				<input type="submit" value="Save">
				<a class="button" href="static.php?path=<?php echo $path ?>">Cancel</a>
				<a class="button" href="files.php?path=<?php echo dirname($path) ?>">Back</a>
			</form>
		</div>
	</body>
</html>
