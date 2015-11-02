<?php
require("util.php");

$path = cleanPath($_GET['path']);

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
$contents = file_get_contents($file_path);

if($action == "save") {
	file_put_contents($file_path,$_POST["contents"]);
	redirect("static.php?path=" . urlencode($path));
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
			<form action="static.php?action=save&path=<?php echo urlencode($path) ?>" method="POST">
				<div id="editorSelector">
					<div style="cursor: pointer; display: inline-block; background-color: #DFDFDF" onclick="enableAce();">Enable Ace Editor</div>
					<div style="cursor: pointer; display: inline-block; background-color: #DFDFDF" onclick="enableCSV();">Enable CSV Editor</div>
				</div>
				<textarea class="editorArea" name="contents" data-editor="<?php echo pathinfo($path,PATHINFO_EXTENSION) ?>" placeholder="The contents of the file" style="height: 30em; width: 100%;"><?php echo htmlspecialchars($contents) ?></textarea><br>
				<input type="submit" value="Save">
				<a class="button" href="static.php?path=<?php echo urlencode($path) ?>">Cancel</a>
				<a class="button" href="files.php?path=<?php echo urlencode(dirname($path)) ?>">Back</a>
			</form>
		</div>
	</body>
	<script src="/ace-builds/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
	<script src="/spreadsheet/spreadsheet.js" type="text/javascript" charset="utf-8"></script>
	<script src="/jquery-1.11.3.js" type="text/javascript" charset="utf-8"></script>
	<script>
	//Extended editor stuff
	function removeSelector() {
		document.getElementById("editorSelector").remove();
	}
	//CSV Stuff
	function enableCSV() {
		removeSelector();
		var textareas = document.getElementsByClassName("editorArea");
		for(var i = 0;i < textareas.length;i++) {
			(function() {
				var spreadsheet = new Spreadsheet(textareas[i]);
				var textarea = textareas[i];
				textareas[i].parentNode.onsubmit = function() {
					textarea.value = spreadsheet.export();
				};
			})();
		}
	}
	//Ace stuff
	function enableAce() {
		removeSelector();
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
			editor.getSession().setUseWrapMode(true);

			if(mode == "js") mode = "javascript";
			if(mode == "md") mode = "markdown";
			if(mode == "csv") {
				editor.getSession().setUseSoftTabs(false);
				editor.getSession().setUseWrapMode(false);
			}

			editor.getSession().setMode("ace/mode/" + mode);
			//editor.setKeyboardHandler("ace/keyboard/emacs");
			editor.setTheme("ace/theme/chrome");
			
			test = editor;
			// copy back to textarea on form submit
			textarea.closest('form').submit(function () {
				textarea.val(editor.getSession().getValue());
			})
		});
	}
	</script>
</html>
