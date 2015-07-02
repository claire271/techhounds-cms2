<?php
require("util.php");

$pages_table = Table::open("cms2-pages");
if(!$pages_table) {
	error_log("cms2-pages table is missing!");
}

$pages = $pages_table->getRows();

if($_SESSION["view"] == "simple"){
	foreach($pages as $page){
		$pagePaths = explode("/",dirname($page->out_path));
		//print_r($pagePaths);

		$pageInt = count($pagePaths);

		$page->exploded_path = $pageInt;
	}
	usort($pages, function($a, $b) {
		$a = $a->exploded_path;
		$b = $b->exploded_path;
		if ($a == $b) {
			return 0;
		}
		return ($a < $b) ? 1 : -1;
	});
	print_r($pages);
}

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
	if(cleanPath(dirname($page->out_path)) == cleanPath($path)) {
		foreach($files as $file) {
			if(basename($page->out_path) == $file->name) {
				$match = true;
				$file->flag = "dynamic";
				$file->index = $page->index;
			}
		}
		if(!$match) {
			$file = new stdClass();
			$file->name = basename($page->out_path);
			$file->flag = "dynamic";
			$file->index = $page->index;
			array_push($files,$file);
		}
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

	if($_SESSION["view"] == "simple"){
		header( "Location: files.php" );
	}
	else {
		header( "Location: files.php?path=" . $path);
	}
}
else if($action == "newdynamic") {
	$page = $pages_table->createRow();

	if($_SESSION["view"] == "simple"){
		$parent = $_POST["parent"];
		$real_path = cleanPath(ROOT_PATH . $parent . $_POST["name"]);

		if (!file_exists($real_path)) {
			mkdir($real_path, 0775, true);
		}

		$page->out_path = cleanPath($parent . $_POST["name"] . "/index.php");
		$page->write();

		header( "Location: files.php" );
	}
	else {
		$page->out_path = cleanPath($path . "/" . $_POST["name"]);
		$page->write();
		header( "Location: files.php?path=" . $path);
	}
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
else if($action == "switchview") {
	$view = isset($_POST["view"]) && $_POST["view"]  ? "simple" : "advanced";
	
	if($view == "simple"){
		$_SESSION["view"] = "simple";
	}
	else {
		$_SESSION["view"] = "advanced";
	}
	header( "Location: files.php?path=/" );
}

function checkForParent($target, $pages){
	$targetPaths = explode("/",dirname($target->out_path));
	print_r($pages);
	//print_r($targetPaths);

	$targetInt = count($targetPaths);

	foreach($pages as $page){

		$pagePaths = explode("/",dirname($page->out_path));
		//print_r($pagePaths);

		$pageInt = count($pagePaths);

		if($targetInt < $pageInt) {
			echo "this is stupid";
		}
		else if($targetInt > $pageInt) {
			echo "this is not stupid";
		}
	}
}

?>
<html>
	<head>
		<meta charset="utf-8">
		<title>File Explorer</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<div class="body-container">
			<?php if($_SESSION["view"] === "advanced") { ?>
			<h1>Index of <?php echo $path ?></h1>
			<?php }
			else {
			?>
			<h1>Pages</h1>
			<?php } ?>
			<div class="column col-75">
				<?php if($_SESSION["view"] === "advanced") { ?>
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
					<br>
				<?php }
				else {
				?>
					<table>
						<thead>
							<tr>
								<th></th>
								<th>Name</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($pages as $page) {
								checkForParent($page, $pages);
								//print_r(count($pages))
							?>
								<tr>
									<td class="delete">
										<a href="files.php?action=delete&type=dynamic&path=<?php echo dirname($page->out_path) ?>&name=<?php echo basename($page->out_path) ?>&index=<?php echo $page->index ?>" style="color:#0F0F0F">×</a>
									</td>
									<td>
										<a href="dynamic.php?path=<?php echo $page->out_path ?>&index=<?php echo $page->index ?>"><?php echo basename(dirname($page->out_path)) ?>
									</td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				<?php } ?>
				<a class="button" href="upload.php?path=<?php echo $path ?>">Upload Files</a>
			</div>
			<div class="column col-25">
				<?php if($_SESSION["view"] === "advanced") { ?>
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
				<?php }
				else { ?>
				<form action="files.php?action=newstatic&path=/templates" method="POST">
					<input type="text" name="name">
					<input type="submit" value="New Template">
				</form>
				<hr>
				<form action="files.php?action=newdynamic&path=/" method="POST">
					<input type="text" name="name" placeholder="Page Name">
					<select name="parent">
						<option value="/">/</option>
						<?php
						foreach($pages as $page) {
						?>
							<option value="<?php echo dirname($page->out_path) . '/'?>"><?php echo dirname($page->out_path) . '/'?></option>
						<?php
						}
						?>
					</select>
					<br>
					<input type="submit" value="New Page">
				</form>
				<?php } ?>
				<form action="files.php?action=switchview" method="POST" id="theForm">
					<input type="checkbox" name="view" id="viewInput"value="simple" onclick="form.submit()"></input>Simple View
				</form>
				<script>
				window.onload = function() {
					var input = document.getElementById("viewInput");
					if("<?php echo $_SESSION["view"]?>" === "simple"){
						input.checked = true;
					}
					else {
						input.checked = false;
					}
				}
				</script>
				<a class="button" href="index.php">Back</a>
			</div>
		</div>
	</body>
</html>
