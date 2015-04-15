<?php
require("util.php");

$users = Table::open("cms2-users");
if(!$users) {
	error_log("cms2-users table is missing!");
}

?>
<html>
	<head>
		<title>Users</title>
	</head>
	<body>
		<h1>Users</h1>
		<?php
		if($action == "view") {
			$row = $users->getRow($_GET["index"]);
		?>
			User <?php echo $row->index ?> <br>
			<form action="users.php?action=save&index=<?php echo $row->index ?>" method="POST">
				Username <input type="text" name="username" value="<?php echo $row->name ?>"><br>
				Password <input type="password" name="password"><br>
				<input type="submit" value="Save">
			</form>
			<a href="users.php">Cancel</a>
		<?php
		}
		else if($action == "save") {
		  $row = $users->getRow($_GET["index"]);
		  $row->name = $_POST["username"];
		  $row->hash = hash("md5", $_POST["password"]);
			$_SESSION["username"] = $row->name;
		  $row->write();
		
			header( "Location: users.php" );
		}
		else {
		  $rows = $users->getRows();
		  foreach($rows as $row) {
		?>
			<?php echo $row->index ?>. <a href="users.php?action=view&index=<?php echo $row->index ?>"><?php echo $row->name ?></a><br>
		<?php
		  }
		}
		?>
		<br>
		<a href="index.php">Back</a>
	</body>
</html>
