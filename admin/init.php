<html>
	<head>
		<title>Techhounds CMS2 Init</title>
	</head>
	<body>
		<?php
		require($_SERVER['DOCUMENT_ROOT'] . "/db/db.php");
		
		if(!Table::exists("cms2-users")) {
			echo("cms2-users table is missing! Recreating now.<br/>");
			$users = Table::create("cms2-users",array("name","hash"));
			$admin = $users->createRow();
			$admin->name = "admin";
			$admin->hash = md5("password");
			$admin->write();
		}
		if(!Table::exists("cms2-pages")) {
			echo("cms2-pages table is missing! Recreating now.<br/>");
			$pages = Table::create("cms2-pages",array("out_path","template_path","params","body","date"));
		}
		?>
	</body>
</html>
