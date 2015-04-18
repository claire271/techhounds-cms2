<?php
require("util.php");

define("ROOT_PATH",cleanPath($_SERVER['DOCUMENT_ROOT']));

$path = cleanPath($_GET['path']);

$pages_table = Table::open("cms2-pages");
if(!$pages_table) {
	error_log("cms2-pages table is missing!");
}
$page = $pages_table->getRow($_GET["index"]);

function template_replace($input,$page) {
	if(strlen($input) > strlen("file:") && substr($input,0,strlen("file:")) == "file:") {
		return file_get_contents(cleanPath(ROOT_PATH . trim(substr($input,strlen("file:")))));
	}
	else {
		return $page->$input;
	}
}

function template_match($input,$callback,$page,$level = 8) {
	if($level <= 0) {
		return $input;
	}

	$openIndex = -1;
	$closeIndex = -1;
	for($i = 1;$i < strlen($input) - 1;$i++) {
		if($input[$i - 1] == "{" && $input[$i] == "{") {
			$openIndex = $i + 1;
		}
		if($input[$i] == "}" && $input[$i + 1] == "}") {
			$closeIndex = $i - 1;

			//Process this match
			if($openIndex >= 0 && $closeIndex >= 0) {
				$replacement = $callback(substr($input,$openIndex,$closeIndex - $openIndex + 1),$page);
				$input = substr($input,0,$openIndex - 2) . $replacement . substr($input,$closeIndex + 3);
			}

			//Clean up for next match
			$openIndex = -1;
			$closeIndex = -1;
		}
	}

	return template_match($input,$callback,$page,$level - 1);
}

if($action == "save") {
	$page->title = $_POST["title"];
	$page->template_path = $_POST["template_path"];
	$page->body = $_POST["body"];
	date_default_timezone_set("America/Indiana/Indianapolis");
	$page->date = date("D d/n/Y G:i:s");
	$page->write();

	//Templating stuff time
	$template = file_get_contents(cleanPath(ROOT_PATH . $page->template_path));
	$output = template_match($template,"template_replace",$page);

	file_put_contents(ROOT_PATH . $path,$output);
	
	//header( "Location: dynamic.php?path=" . $path . "&index=" . $page->index);
}

?>
<html>
	<head>
		<title>Dynamic Editor</title>
	</head>
	<body>
		<form action="dynamic.php?action=save&path=<?php echo $path ?>&index=<?php echo $page->index ?>" method="POST">
			<h1><?php echo $path ?></h1>
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
