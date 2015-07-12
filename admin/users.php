<?php
require("util.php");

$users = Table::open("cms2-users");
if(!$users) {
	error_log("cms2-users table is missing!");
	fatal_error();
}

?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Users</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<div class="body-container">
			<h1>Users</h1>
			<?php
			if($action == "userpass" || $action == "adduser") {
				$row = $users->getRow($_GET["index"]);
				if($action == "userpass") {
					echo "User " . $row->index . "<br>";
				} ?>
				<form action="users.php?action=<?php if ($action == "userpass"){ echo('s_userpass&index='); echo $row->index; } else if ($action == "adduser"){ echo('s_adduser');} ?>" method="POST">
					Username: <input type="text" name="username" value="<?php if ($action == "userpass") echo $row->name ?>"><br>
					Password: <input type="password" name="password"><br>
					<input type="submit" value="Submit">
				<a class="button" href="users.php">Cancel</a>
				</form>
			<?php
			}
			else if($action == "s_userpass" || $action == "s_adduser") {
				if($action == "s_userpass"){
					$row = $users->getRow($_GET["index"]);
				}
				else if ($action == "s_adduser"){
					$row = $users->createRow();
				}
				$row->name = $_POST["username"];
				
    			//$row->hash = hash("md5", $_POST["password"]);

			    $row->salt = hash("sha512",mt_rand());
			    $row->hash = $row->salt . $_POST["password"];
			    for($i = 0;$i < 100000;$i++) {
				    $row->hash = hash("sha512",$row->hash);
			    }

				if($action == "s_userpass") {
	    			$_SESSION["username"] = $row->name;
				}
				$row->write();
		
			    redirect("users.php");
			}
			else if($action == "perms") {
				$row = $users->getRow($_GET["index"]);
				echo "User " . $row->index . ": " . $row->name . "<br>"; ?>
				<form action="users.php?action=s_perms&index=<?php echo $row->index ?>" method="POST">
					Permissions:<br/>
					<textarea name="permissions" placeholder="The permissions of this user" style="height: 30em; width: 80%;"><?php echo htmlspecialchars($row->permissions) ?></textarea><br>
					<input type="submit" value="Submit">
					<a class="button" href="users.php">Cancel</a>
				</form>
			<?php
			}
			else if($action == "s_perms") {
				$row = $users->getRow($_GET["index"]);
				$row->permissions = $_POST["permissions"];
				$row->write();
				redirect("users.php");
			}
			else if($action == "delete") {
				$users->deleteRow($_GET["index"]);
				redirect("users.php");
			}
			else {
				$rows = $users->getRows();
			?>
				<table>
					<thead>
						<tr>
							<th>#</th>
							<th>Username</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($rows as $row) {
							?>
							<tr>
								<th scope="row"><?php echo $row->index ?></td>
								<td><a href="users.php?action=userpass&index=<?php echo $row->index ?>"><?php echo $row->name ?></a></td>
								<td><a href="users.php?action=perms&index=<?php echo $row->index ?>">Permissions</a></td>
								<td><a href="users.php?action=delete&index=<?php echo $row->index ?>">Delete</a></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php
			}
			?>
			<br>
			<?php
			if(!($action == "userpass" || $action == "adduser" || $action == "perms")){ ?>
			<a class="button" href="users.php?action=adduser">Add User</a>
			<a class="button" href="index.php">Back</a>
			<?php 
			}
			?>
		</div>
	</body>
</html>
