<?php
require("util.php");

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
	if(is_dir(cleanPath($file_path . "/" . $name))) {
		$files[$i]->flag = "dir";
	}
	else {
		$files[$i]->flag = "static";
	}
}

foreach($pages as $page) {
	$match = false;
	foreach($files as $file) {
		if(cleanPath($page->out_path) == cleanPath($file_path . "/" . $file->name)) {
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
		$real_path = cleanPath($file_path . "/" . $_GET["name"]);
		rmdir($real_path);
	}
	else if($_GET["type"] == "static") {
		$real_path = cleanPath($file_path . "/" . $_GET["name"]);
		unlink($real_path);
	}
	else if($_GET["type"] == "dynamic") {
		$real_path = cleanPath($file_path . "/" . $_GET["name"]);
		if(file_exists($real_path)) {
			unlink($real_path);
		}
		$pages_table->deleteRow($_GET["index"]);
	}
	header( "Location: files.php?path=" . $path);
}
else if($action == "newdir") {
	$real_path = cleanPath($file_path . "/" . $_POST["name"]);
	mkdir($real_path);
	chmod($real_path,0775);
	header( "Location: files.php?path=" . $path);
}
else if($action == "newstatic") {
	$real_path = cleanPath($file_path . "/" . $_POST["name"]);
	touch($real_path);
	chmod($real_path,0664);
	header( "Location: files.php?path=" . $path);
}
else if($action == "newdynamic") {
	$page = $pages_table->createRow();
	$page->out_path = cleanPath($path . "/" . $_POST["name"]);
	$page->write();
	header( "Location: files.php?path=" . $path);
}
else if($action == "rename") {
	$in = $_POST["in"];
	$out = $_POST["out"];
	foreach($files as $file) {
		if($file->name != ".." && $file->name != "." && $file->name == basename($in)) {
			if($file->flag == "static" || $file->flag == "dir") {
				rename(cleanPath(ROOT_PATH . $in),cleanPath(ROOT_PATH . $out));
			}
			else if($file->flag == "dynamic") {
				rename(cleanPath(ROOT_PATH . $in),cleanPath(ROOT_PATH . $out));
				$page = $pages_table->getRow($file->index);
				$page->out_path = cleanPath($out);
				$page->write();
			}
			break;
		}
	}
	header( "Location: files.php?path=" . $path);
}

?>
<html>
	<head>
		<title>File Explorer</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<div class="body-container">
			<h1>Index of <?php echo $path ?></h1>
			<div class="column col-75">
				<table>
					<thead>
						<tr>
							<th></th>
							<th>File Name</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($files as $file) {
							$new_path = cleanPath($path . "/" . $file->name);
							if($file->flag == "dir") {
						?>
							<tr style="background-color: #F7F7F7">
								<td class="delete">
									<a href="files.php?action=delete&type=dir&path=<?php echo $path ?>&name=<?php echo $file->name ?>" style="color:#0F0F0F">×</a>
								</td>
								<td>
									<a href="files.php?path=<?php echo $new_path ?>" style="color:#0000FF"><?php echo $file->name ?></a><br>
								</td>
							</tr>
						<?php
						}
						else if($file->flag == "static") {
						?>
							<tr>
								<td class="delete">
									<a href="files.php?action=delete&type=static&path=<?php echo $path ?>&name=<?php echo $file->name ?>" style="color:#0F0F0F">×</a>
								</td>
								<td>
									<a href="static.php?path=<?php echo $new_path ?>" style="color:#FF0000"><?php echo $file->name ?></a><br>
								</td>
							</tr>
						<?php
						}
						else if($file->flag == "dynamic") {
						?>
							<tr>
								<td class="delete">
									<a href="files.php?action=delete&type=dynamic&path=<?php echo $path ?>&name=<?php echo $file->name ?>&index=<?php echo $file->index ?>" style="color:#0F0F0F">×</a>
								</td>
								<td>
									<a href="dynamic.php?path=<?php echo $new_path ?>&index=<?php echo $file->index ?>" style="color:#FF00FF"><?php echo $file->name ?></a><br>
								</td>
							</tr>
						<?php
						}
						}
						?>
						</tbody>
				</table>
				<a href="upload.php?path=<?php echo $path ?>">Upload Files</a>
			</div>
			<div class="column col-25">
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
				<form action="files.php?action=rename&path=<?php echo $path ?>" method="POST">
					IN: <input type="text" name="in" value="<?php echo $path ?>"><br/>
					OUT: <input type="text" name="out" value="<?php echo $path ?>"><br/>
					<input type="submit" value="Rename File">
				</form>
			</div>
				<a href="index.php">Back</a><br>
		</div>
	</body>
</html>
