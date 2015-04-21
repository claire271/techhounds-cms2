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
		<title>Static Editor</title>
	</head>
	<body>
		<h1><?php echo basename($path) ?></h1>
		<form action="static.php?action=save&path=<?php echo $path ?>" method="POST">
			<textarea name="contents" placeholder="The contents of the file" style="height: 30em; width: 80%;"><?php echo htmlspecialchars($contents) ?></textarea><br>
			<input type="submit" value="Save">
			<a href="static.php?path=<?php echo $path ?>">Cancel</a>
			<a href="files.php?path=<?php echo dirname($path) ?>">Back</a>
		</form>
	</body>
</html>
