<?php
require("util.php");

define("ROOT_PATH",$_SERVER['DOCUMENT_ROOT'] . "/");

$pages_table = Table::open("cms2-pages");
if(!$pages_table) {
	error_log("cms2-pages table is missing!");
}
$pages = $pages_table->getRows();

$path = isset( $_GET['path'] ) ? $_GET['path'] : "/";
$file_path = ROOT_PATH . $path;
$files = scandir($file_path);

for($i = 0;$i < count($files);$i++) {
	$name = $files[$i];
	$files[$i] = new stdClass();
	$files[$i]->name = $name;
	if(is_dir($file_path . $name)) {
		$files[$i]->flag = "dir";
	}
	else {
		$files[$i]->flag = "static";
	}
}

foreach($pages as $page) {
	print_r($page);
}




echo "<pre>";
print_r($files);
echo "</pre>";

?>
<html>
	<head>
		<title>File Explorer</title>
	</head>
	<body>
		<h1>Index of <?php echo $path ?></h1>
		<?php
		foreach($files as $file) {
			echo $file->flag;
			echo $file->name;
		}
		?>
	</body>
</html>
