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
	<script src="/ace-builds/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
	<script>
	//Ace stuff
	$(function () {
		$('textarea[data-editor]').each(function () {
			var textarea = $(this);
			
			var mode = textarea.data('editor');
			
			var editDiv = $('<div>', {
				position: 'absolute',
				width: textarea.width(),
				height: textarea.height(),
				'class': textarea.attr('class')
			}).insertBefore(textarea);
			
			//textarea.css('visibility', 'hidden');
			textarea.css('display', 'none');
			
			var editor = ace.edit(editDiv[0]);
			//editor.renderer.setShowGutter(false);
			editor.getSession().setValue(textarea.val());
			editor.getSession().setMode("ace/mode/" + mode);
			editor.setKeyboardHandler("ace/keyboard/ace");
			editor.setTheme("ace/theme/idle_fingers");
			
			test = editor;
			// copy back to textarea on form submit
			textarea.closest('form').submit(function () {
				textarea.val(editor.getSession().getValue());
			})
		});
	});
	</script>
</html>
