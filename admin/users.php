<?php
require("util.php");

$users = Table::open("cms2-users");
if(!$users) {
	error_log("cms2-users table is missing!");
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
			if($action == "view" || $action == "add") {
				$row = $users->getRow($_GET["index"]);
			?>
				<?php
				if($action == "view") { ?>
					User <?php echo $row->index ?><br>
				<?php
				}
				?>
				<form action="users.php?action=<?php if ($action == "view"){ echo('save&index='); echo $row->index; } else if ($action == "add"){ echo('create');} ?>" method="POST">
					Username: <input type="text" name="username" value="<?php if ($action == "view") echo $row->name ?>"><br>
					Password: <input type="password" name="password"><br>
					<input type="submit" value="Submit">
				<?php if($action == "view"){ ?> <a class="button" href="users.php?action=delete&index=<?php echo $row->index ?>">Delete</a><?php } ?>
				<a class="button" href="users.php">Cancel</a>
				</form>
			<?php
			}
			else if($action == "save" || $action == "create") {
			  if($action == "save"){
		        $row = $users->getRow($_GET["index"]);
			  }
		      else if ($action == "create"){
		        $row = $users->createRow();
		      }
		      $row->name = $_POST["username"];
    		  $row->hash = hash("md5", $_POST["password"]);
	    	  $_SESSION["username"] = $row->name;
		      $row->write();
		
			    header( "Location: users.php" );
			}
			else if($action == "delete") {
			  $users->deleteRow($_GET["index"]);
		      header( "Location: users.php" );
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
								<td><a href="users.php?action=view&index=<?php echo $row->index ?>"><?php echo $row->name ?></a></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php
			}
			?>
			<br>
			<?php
			if(!($action == "view" || $action == "add")){ ?>
			<a class="button" href="users.php?action=add">Add User</a>
			<a class="button" href="index.php">Back</a>
			<?php 
			}
			?>
		</div>
	</body>
</html>
