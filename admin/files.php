<?php
require("util.php");
$pages_table = Table::open("cms2-pages");
if(!$pages_table) {
	error_log("cms2-pages table is missing!");
	fatal_error();
}
$pages = $pages_table->getRows();
$pages_rcd = $pages_table->getRows();

if(!isset($_SESSION["view"])) {
	$_SESSION["view"] = "advanced";
}

if($_SESSION["view"] == "simple"){
	foreach($pages as $page){
		$pagePaths = explode("/",dirname($page->out_path));
		//print_r($pagePaths);
		$pageInt = count($pagePaths);
		$page->exploded_path = $pageInt;
	}
	function sort_simple_view($a, $b) {
		$a = $a->exploded_path;
		$b = $b->exploded_path;
		if ($a == $b) {
			return 0;
		}
		return ($a < $b) ? 1 : -1;
	}
	usort($pages,"sort_simple_view");
	$pages2 = array();
	$pages2 = $pages;
	//Check if the target page has a parent
	foreach($pages as $target){
		$targetPaths = explode("/",dirname($target->out_path));
		$targetInt = count($targetPaths);
		foreach($pages as $page){
			$has_parent = true;
			$pagePaths = explode("/",dirname($page->out_path));
			$pageInt = count($pagePaths);
			if(basename($page->out_path) == "index.php"){
				if($targetInt == $pageInt + 1) {
					for($i = 0; $i < $pageInt; $i++) {
						if($targetPaths[$i] != $pagePaths[$i]) {
							$has_parent = false;
						}
					}
					if($has_parent == true){
						if(!property_exists($page,'children')){
							$page->children = array();
						}
						array_push($page->children, $target);
						unset($pages[array_search($target,$pages)]);
					}
				}
				else if($targetInt == $pageInt) {
					if(basename($target->out_path) != "index.php") {
						if($targetPaths !== $pagePaths){
							$has_parent = false;
						}
						
						if($has_parent == true){
							if(!property_exists($page,'children')){
								$page->children = array();
							}
							array_push($page->children, $target);
							unset($pages[array_search($target,$pages)]);
						}
					}
				}
			}
		}
	}
}

$path = isset( $_GET['path'] ) ? $_GET['path'] : "/";
$path = cleanPath($path);

//Permissions checking
$allowed = true;
foreach($sub_perms as $permission) {
	if(patternMatch($permission["action"],$path,true)) {
		$allowed = $permission["allowed"];
	}
}
if(!$allowed) {
	redirect("permissions.php?action=denied");
}

$file_path = ROOT_DIR . $path;
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

//Recursive rename, copy, and delete
function rcd($input,$output,$action,$pages,$pages_table) {
	$in_path = cleanPath(ROOT_DIR . "/" . $input);
	$out_path = cleanPath(ROOT_DIR . "/" . $output);
	echo $in_path . "<br>";
	if(is_dir($in_path)) {
		if($action == "copy" || $action == "rename") {
			mkdir($out_path,0775,true);
			chmod($out_path,0775);
		}

		$files = scandir($in_path);
		foreach($pages as $page) {
			if(dirname(cleanPath($page->out_path)) === (cleanPath($input))) {
				array_push($files,basename(cleanPath($page->out_path)));
			}
		}
		foreach($files as $file) {
			if($file != "." && $file != "..") {
				rcd(cleanPath($input . "/" . $file),cleanPath($output . "/" . $file),$action,$pages,$pages_table);
			}
		}

		if($action == "rename" || $action == "delete") {
			rmdir($in_path);
		}
	}
	else {
		$d_page = null;
		foreach($pages as $page) {
			if(cleanPath($page->out_path) === (cleanPath($input))) {
				$d_page = $page;
			}
		}

		if($action == "copy") {
			if($d_page === null) {
				copy($in_path,$out_path);
			}
			else {
				copy($in_path,$out_path);
				$page_out = $pages_table->createRow();
				$page_out->out_path = cleanPath($output);
				$page_out->template_path = $d_page->template_path;
				$page_out->params = $d_page->params;
				$page_out->body = $d_page->body;
				$page_out->date = $d_page->date;
				$page_out->write();
			}
		}
		else if($action == "rename") {
			if($d_page === null) {
				rename($in_path,$out_path);
			}
			else {
				rename($in_path,$out_path);
				$d_page->out_path = cleanPath($output);
				$d_page->write();
			}
		}
		else if($action == "delete") {
			if($d_page === null) {
				unlink($in_path);
			}
			else {
				if(file_exists($in_path)) {
					unlink($in_path);
				}
				$pages_table->deleteRow($d_page->index);
			}
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
	redirect("files.php?path=" . urlencode($path));
}
else if($action == "newdir") {
	$real_path = cleanPath($file_path . "/" . $_POST["name"]);
	mkdir($real_path,0775,true);
	chmod($real_path,0775);
	redirect("files.php?path=" . urlencode($path));
}
else if($action == "newstatic") {
	$real_path = cleanPath($file_path . "/" . $_POST["name"]);
	touch($real_path);
	chmod($real_path,0664);
	if($_SESSION["view"] == "simple"){
		redirect("files.php");
	}
	else {
		redirect("files.php?path=" . urlencode($path));
	}
}
else if($action == "newdynamic") {
	$page = $pages_table->createRow();
	if($_SESSION["view"] == "simple"){
		$parent = $_POST["parent"];
		$real_path = cleanPath(ROOT_DIR . $parent . $_POST["name"]);
		echo $real_path;
		if (!file_exists($real_path)) {
			mkdir($real_path, 0775, true);
			chmod($real_path,0775);
		}
		$page->out_path = cleanPath($parent . $_POST["name"] . "/index.php");
		$page->write();
		redirect("files.php");
	}
	else {
		$page->out_path = cleanPath($path . "/" . $_POST["name"]);
		$page->write();
		redirect("files.php?path=" . urlencode($path));
	}
}
else if($action == "rcd") {
	$type = $_POST["type"];
	if($type == "Recursive Rename File/Dir") $type = "rename";
	if($type == "Recursive Copy File/Dir") $type = "copy";
	if($type == "Recursive Delete File/Dir") $type = "delete";
	$in = $_POST["in"];
	$out = $_POST["out"];
	rcd($in,$out,$type,$pages_rcd,$pages_table);
	redirect("files.php?path=" . urlencode($path));
}
else if($action == "switchview") {
	$view = isset($_POST["view"]) && $_POST["view"]  ? "simple" : "advanced";
	
	if($view == "simple"){
		$_SESSION["view"] = "simple";
	}
	else {
		$_SESSION["view"] = "advanced";
	}
	redirect("files.php");
}
function sort_check_children($a, $b) {
	$a = basename(dirname($a->out_path));
	$b = basename(dirname($b->out_path));
	return strcmp($a, $b);
}
function checkForChildren($depth, $page) {
	if(property_exists($page, 'children')) {
		$children = $page->children;
		usort($children, "sort_check_children");
		foreach($children as $child){
			generateHTML($depth+1, $child);
		}
	}
}
function generateHTML($depth, $page){ ?>
	<tr>
		<td class="delete">
			<a href="files.php?action=delete&type=dynamic&path=<?php echo dirname($page->out_path) ?>&name=<?php echo basename($page->out_path) ?>&index=<?php echo $page->index ?>" style="color:#0F0F0F">×</a>
		</td>
		<td>
			<a style="padding-left:<?php echo 20 * $depth?>px;color:#<?php if(basename($page->out_path) == "index.php"){ echo "0000FF"; } else { echo "FF00FF"; }?>" href="dynamic.php?path=<?php echo $page->out_path ?>&index=<?php echo $page->index ?>"><?php if(basename($page->out_path) == "index.php"){ echo basename(dirname($page->out_path)); } else { echo basename($page->out_path); } ?></a>
		</td>
	</tr>
	<?php
	checkForChildren($depth, $page);
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
			<?php
			if($_SESSION["view"] == "simple") {
				echo "<h1>Pages</h1>";
			}
			else {
				echo "<h1>Index of " . $path . "</h1>";
			}
			?>
			<div class="column col-75">
			<?php if($_SESSION["view"] != "simple") { ?>
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
								<a href="files.php?action=delete&type=dir&path=<?php echo urlencode($path) ?>&name=<?php echo urlencode($file->name) ?>" style="color:#0F0F0F">×</a>
							</td>
							<td>
								<a href="files.php?path=<?php echo urlencode($new_path) ?>" style="color:#0000FF"><?php echo $file->name ?></a><br>
							</td>
							<td class="delete">
								<a href="javascript:setRCParams('<?php echo urlencode($new_path) ?>');" style="color:#0F0F0F">R/C</a>
							</td>
						</tr>
					<?php
					}
					else if($file->flag == "static") {
					?>
						<tr>
							<td class="delete">
								<a href="files.php?action=delete&type=static&path=<?php echo urlencode($path) ?>&name=<?php echo urlencode($file->name) ?>" style="color:#0F0F0F">×</a>
							</td>
							<td>
								<a href="static.php?path=<?php echo urlencode($new_path) ?>" style="color:#FF0000"><?php echo $file->name ?></a><br>
							</td>
							<td class="delete">
								<a href="javascript:setRCParams('<?php echo urlencode($new_path) ?>');" style="color:#0F0F0F">R/C</a>
							</td>
						</tr>
					<?php
					}
					else if($file->flag == "dynamic") {
					?>
						<tr>
							<td class="delete">
								<a href="files.php?action=delete&type=dynamic&path=<?php echo urlencode($path) ?>&name=<?php echo urlencode($file->name) ?>&index=<?php echo urlencode($file->index) ?>" style="color:#0F0F0F">×</a>
							</td>
							<td>
								<a href="dynamic.php?path=<?php echo urlencode($new_path) ?>&index=<?php echo urlencode($file->index) ?>" style="color:#FF00FF"><?php echo $file->name ?></a><br>
							</td>
							<td class="delete">
								<a href="javascript:setRCParams('<?php echo urlencode($new_path) ?>');" style="color:#0F0F0F">R/C</a>
							</td>
						</tr>
					<?php
					}
					}
					?>
					</tbody>
				</table>
			<?php 
			}
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
					function sort_display($a, $b) {
						$a = basename(dirname($a->out_path));
						$b = basename(dirname($b->out_path));
						return strcmp($a, $b);
					}
					usort($pages,"sort_display");
					foreach($pages as $page) {
						$depth = 0;
					?>
						<tr>
							<td class="delete">
								<a href="files.php?action=delete&type=dynamic&path=<?php echo dirname($page->out_path) ?>&name=<?php echo basename($page->out_path) ?>&index=<?php echo $page->index ?>" style="color:#0F0F0F">×</a>
							</td>
							<td>
								<a style="color:#<?php if(basename($page->out_path) == "index.php"){ echo "0000FF"; } else { echo "FF00FF"; }?>" href="dynamic.php?path=<?php echo $page->out_path ?>&index=<?php echo $page->index ?>"><?php if($page->out_path == "/index.php") { echo "Home"; } else if(basename($page->out_path) == "index.php"){ echo basename(dirname($page->out_path)); } else { echo basename($page->out_path); } ?></a>
							</td>
						</tr>
					<?php
					checkForChildren($depth, $page);
					}
					?>
					</tbody>
				</table>
			<?php } ?>
			</div>
			<div class="column col-25">
			<?php if($_SESSION["view"] != "simple") { ?>
				<form action="files.php?action=newdir&path=<?php echo urlencode($path) ?>" method="POST">
					<input type="text" name="name">
					<input type="submit" value="Create Directory">
				</form>
				<form action="files.php?action=newstatic&path=<?php echo urlencode($path) ?>" method="POST">
					<input type="text" name="name">
					<input type="submit" value="Create Static File">
				</form>
				<form action="files.php?action=newdynamic&path=<?php echo urlencode($path) ?>" method="POST">
					<input type="text" name="name">
					<input type="submit" value="Create Dynamic File">
				</form>
				<form action="files.php?action=rcd&path=<?php echo urlencode($path) ?>" method="POST">
					IN: <input id="rcdin" type="text" name="in" value="<?php echo $path ?>"><br/>
					OUT: <input id="rcdout" type="text" name="out" value="<?php echo $path ?>"><br/>
					<input name="type" type="submit" value="Recursive Rename File/Dir" id="rcd-r">
					<input name="type" type="submit" value="Recursive Copy File/Dir" id="rcd-c">
					<input name="type" type="submit" value="Recursive Delete File/Dir" id="rcd-d">
				</form>
			<?php
			}
			else {
			?>
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
						foreach($pages2 as $page) {
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
				<a class="button" href="upload.php?path=<?php echo urlencode($path) ?>">Upload Files</a>
				<br>
				<br>
				<a class="button" href="index.php">Back</a>
			</div>
		</div>
	</body>
	<script type="text/javascript">
	function setRCParams(name) {
		document.getElementById("rcdin").value = name;
		document.getElementById("rcdout").value = name;
	}
	window.onload = function() {
		var input = document.getElementById("viewInput");
		if("<?php echo $_SESSION["view"]?>" === "simple"){
			input.checked = true;
		}
		else {
			input.checked = false;
		}
		document.getElementById("rcd-r").onclick = function(e) {
			if(!confirm("Rename this?")) {
				e.preventDefault();
			}
		}
		document.getElementById("rcd-c").onclick = function(e) {
			if(!confirm("Copy this?")) {
				e.preventDefault();
			}
		}
		document.getElementById("rcd-d").onclick = function(e) {
			if(!confirm("Delete this?")) {
				e.preventDefault();
			}
		}
	}
	</script>
</html>
