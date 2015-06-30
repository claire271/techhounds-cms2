<html>
	<head>
		<title>Techhounds CMS2 Init</title>
	</head>
	<body>
		<?php
		require($_SERVER['DOCUMENT_ROOT'] . "/db/db.php");
		
		if(!Table::exists("cms2-users")) {
			echo("cms2-users table is missing! Recreating now.<br/>");
			$users = Table::create("cms2-users",array("name","hash","salt"));
			$admin = $users->createRow();
			$admin->name = "admin";

    		//$admin->hash = hash("md5", "password");

			$admin->salt = hash("sha512",mt_rand());
			$admin->hash = $admin->salt . "password";
			for($i = 0;$i < 100000;$i++) {
				$admin->hash = hash("sha512",$admin->hash);
			}
			$admin->write();
		}
		if(!Table::exists("cms2-pages")) {
			echo("cms2-pages table is missing! Recreating now.<br/>");
			$pages = Table::create("cms2-pages",array("out_path","template_path","params","body","date"));
		}
		?>
	</body>
</html>
