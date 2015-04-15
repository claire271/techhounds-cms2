<?php
require("util.php");

$users = Table::open("cms2-users");
?>
<html>
	<head>
		<title>Admin</title>
	</head>
	<body>
		<h1>Admin</h1>
		<pre>
			<?php
			if(!$users) {
				echo "ERROR";
			}
			else {
				print_r($users);
			}
			?>
		</pre>
	</body>
</html>
