<?php
require("util.php");

define("ROOT_PATH",cleanPath($_SERVER['DOCUMENT_ROOT']));

$path = cleanPath($_GET['path']);

$pages_table = Table::open("cms2-pages");
if(!$pages_table) {
	error_log("cms2-pages table is missing!");
}
$page = $pages_table->getRow($_GET["index"]);

if($action == "save") {
	$page->title = $_POST["title"];
	$page->template_path = $_POST["template_path"];
	$page->body = $_POST["body"];
	$page->write();

	//Templating stuff time
	
	
	header( "Location: dynamic.php?path=" . $path . "&index=" . $page->index);
}

?>
<html>
	<head>
		<title>Dynamic Editor</title>
	</head>
	<body>
		<form action="dynamic.php?action=save&path=<?php echo $path ?>&index=<?php echo $page->index ?>" method="POST">
			<h1><?php echo $page->out_path ?></h1>
			<input type="text" name="title" value="<?php echo $page->title ?>"><br>
			<input type="text" name="template_path" value="<?php echo $page->template_path ?>"><br>
			<textarea name="body" placeholder="The body of the file" style="height: 30em; width: 80%;"><?php echo $page->body ?></textarea><br>
			Last edited: <?php echo $page->date ?><br>
			<input type="submit" value="Save">
			<a href="dynamic.php?path=<?php echo $path ?>&index=<?php echo $page->index ?>">Cancel</a>
			<a href="files.php?path=<?php echo dirname($path) ?>">Back</a>
		</form>
	</body>
</html>
