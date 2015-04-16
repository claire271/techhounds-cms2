<?php
require("util.php");

define("ROOT_PATH",cleanPath($_SERVER['DOCUMENT_ROOT']));

$pages_table = Table::open("cms2-pages");
if(!$pages_table) {
	error_log("cms2-pages table is missing!");
}
$pages = $pages_table->getRows();

$path = isset( $_GET['path'] ) ? $_GET['path'] : "/";
$path = cleanPath($path);
$file_path = ROOT_PATH . $path;
$files = scandir($file_path);

for($i = 0;$i < count($files);$i++) {
	$name = $files[$i];
	$files[$i] = new stdClass();
	$files[$i]->name = $name;
	if(is_dir($file_path . "/" . $name)) {
		$files[$i]->flag = "dir";
	}
	else {
		$files[$i]->flag = "static";
	}
}

foreach($pages as $page) {
	$match = false;
	foreach($files as $file) {
		if(basename($page->out_path) == $file->name) {
			$match = true;
			$file->flag = "dynamic";
			$file->index = $page->index;
		}
	}
	if(!$match && cleanPath(dirname($page->out_path)) == cleanPath($path)) {
		$file = new stdClass();
		$file->name = basename($page->out_path);
		$file->flag = "dynamic";
		$file->index = $page->index;
		array_push($files,$file);
	}
}

if($action == "delete") {
	if($_GET["type"] == "dir") {
		$real_path = $file_path . "/" . $_GET["name"];
		rmdir($real_path);
	}
	else if($_GET["type"] == "static") {
		$real_path = $file_path . "/" . $_GET["name"];
		unlink($real_path);
	}
	else if($_GET["type"] == "dynamic") {
		$real_path = $file_path . "/" . $_GET["name"];
		if(file_exists($real_path)) {
			unlink($real_path);
		}
		$pages_table->deleteRow($_GET["index"]);
	}
	header( "Location: files.php?path=" . $path);
}
else if($action == "newdir") {
	$real_path = $file_path . "/" . $_POST["name"];
	mkdir($real_path);
	chmod($real_path,0775);
	header( "Location: files.php?path=" . $path);
}
else if($action == "newstatic") {
	$real_path = $file_path . "/" . $_POST["name"];
	touch($real_path);
	chmod($real_path,0664);
	header( "Location: files.php?path=" . $path);
}
else if($action == "newdynamic") {
	$page = $pages_table->createRow();
	$page->out_path = $path . "/" . $_POST["name"];
	$page->write();
	header( "Location: files.php?path=" . $path);
}

?>
<html>
	<head>
		<title>File Explorer</title>
	</head>
	<body>
		<h1>Index of <?php echo $path ?></h1>
		<a href="index.php">Back</a><br>
		<form action="files.php?action=newdir&path=<?php echo $path ?>" method="POST">
			<input type="text" name="name">
			<input type="submit" value="Create Directory">
		</form>
		<form action="files.php?action=newstatic&path=<?php echo $path ?>" method="POST">
			<input type="text" name="name">
			<input type="submit" value="Create Static File">
		</form>
		<form action="files.php?action=newdynamic&path=<?php echo $path ?>" method="POST">
			<input type="text" name="name">
			<input type="submit" value="Create Dynamic File">
		</form>

		<br>
		<?php
		foreach($files as $file) {
			$new_path = cleanPath($path . "/" . $file->name);
			if($file->flag == "dir") {
		?>
			<a href="files.php?action=delete&type=dir&path=<?php echo $path ?>&name=<?php echo $file->name ?>" style="color:#0F0F0F">x</a>
			<a href="files.php?path=<?php echo $new_path ?>" style="color:#0000FF"><?php echo $file->name ?></a><br>
		<?php
		  }
		  else if($file->flag == "static") {
		?>
			<a href="files.php?action=delete&type=static&path=<?php echo $path ?>&name=<?php echo $file->name ?>" style="color:#0F0F0F">x</a>
			<a href="static.php?path=<?php echo $new_path ?>" style="color:#FF0000"><?php echo $file->name ?></a><br>
		<?php
		  }
		  else if($file->flag == "dynamic") {
		?>
			<a href="files.php?action=delete&type=dynamic&path=<?php echo $path ?>&name=<?php echo $file->name ?>&index=<?php echo $file->index ?>" style="color:#0F0F0F">x</a>
			<a href="dynamic.php?path=<?php echo $new_path ?>&index=<?php echo $file->index ?>" style="color:#FF00FF"><?php echo $file->name ?></a><br>
		<?php
		  }
		}
		?>
	</body>
</html>
