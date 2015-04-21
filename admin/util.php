<?php
//Utility functions and declarations first
define("ROOT_PATH",cleanPath($_SERVER['DOCUMENT_ROOT']));

//Always returns with no trailing slash
function cleanPath($path) {
	$path = str_replace("\\","/",$path);
	$leading = substr($path,0,1) == "/";

  $substrings = explode("/",$path);
	
  $parts = array();
	foreach($substrings as $substring) {
    if($substring == "..") {
      array_pop($parts);
    }
    else if($substring == ".") {}
    else if($substring == "") {}
    else {
      array_push($parts,$substring);
    }
  }

  return ($leading ? "/" : "") . implode("/",$parts);
}

//Template replacing function
function template_replace($input,$page) {
	if(strlen($input) > strlen("file:") && substr($input,0,strlen("file:")) == "file:") {
		return file_get_contents(cleanPath(ROOT_PATH . trim(substr($input,strlen("file:")))));
	}
	else {
		return $page->$input;
	}
}

//Template matching function
function template_match($input,$callback,$page,$level = 8) {
	if($level <= 0) {
		return $input;
	}

	$openIndex = -1;
	$closeIndex = -1;
	for($i = 1;$i < strlen($input) - 1;$i++) {
		if($input[$i - 1] == "{" && $input[$i] == "{") {
			$openIndex = $i + 1;
		}
		if($input[$i] == "}" && $input[$i + 1] == "}") {
			$closeIndex = $i - 1;

			//Process this match
			if($openIndex >= 0 && $closeIndex >= 0) {
				$replacement = $callback(substr($input,$openIndex,$closeIndex - $openIndex + 1),$page);
				$input = substr($input,0,$openIndex - 2) . $replacement . substr($input,$closeIndex + 3);
			}

			//Clean up for next match
			$openIndex = -1;
			$closeIndex = -1;
		}
	}

	return template_match($input,$callback,$page,$level - 1);
}

require(cleanPath($_SERVER['DOCUMENT_ROOT'] . "/db/db.php"));

session_save_path(cleanPath($_SERVER['DOCUMENT_ROOT'] . "/admin/session"));
session_start();

//Only for login/logout page
if(defined("LOGOUT")) {
	unset( $_SESSION['username'] );
}
//See if logged on already and not on main page
else if(!defined("MAIN") && !isset($_SESSION["username"])) {
	header( "Location: login.php?action=fail" );
}

$action = isset( $_GET['action'] ) ? $_GET['action'] : "";

?>

