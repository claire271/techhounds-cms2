<?php
//Utility functions and declarations first
define("ROOT_DIR",cleanPath($_SERVER['DOCUMENT_ROOT']));
//define("ADMIN_DIR",cleanPath(dirname(__FILE__)));
//define("ADMIN_RDIR",cleanPath("/" . implode("/",array_diff(explode("/",ADMIN_DIR),explode("/",ROOT_DIR)))));
define("ADMIN_RDIR",cleanPath("/admin/"));
define("ADMIN_DIR",cleanPath(ROOT_DIR . "/" . ADMIN_RDIR));

ini_set("log_errors", 1);
ini_set("error_log", cleanPath(ADMIN_DIR . "/error.log"));

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

//Template clearing function
function template_clear($input,$page,$vars) {
	return "";
}

//Template replacing function
function template_replace($input,$page,$vars) {
	if(strlen($input) > strlen("file:") && substr($input,0,strlen("file:")) == "file:") {
		$parts = explode(",,",trim(substr($input,strlen("file:"))));
		$output = "";
		for($i = 1;$i < count($parts);$i++) {
			$output .= "{{varset:" . ($i - 1) . ":" . $parts[$i] . "}}";
		}
		$output .= file_get_contents(cleanPath(ROOT_DIR . $parts[0]));
		for($i = 1;$i < count($parts);$i++) {
			$output .= "{{varclear:" . ($i - 1) . "}}";
		}
		//return file_get_contents(cleanPath(ROOT_DIR . $parts[0]));
		return $output;
	}
	else if(strlen($input) > strlen("param:") && substr($input,0,strlen("param:")) == "param:") {
		$params = explode("\n",$page->params);
		foreach($params as $param) {
			$param = trim($param,"\r\n");
			$pos = strpos($param," ");
			$name = substr($param,0,$pos);
			//$name = strstr($param," ",true);
			$repl = substr(strstr($param," "),1);
			if($name == substr($input,strlen("param:"))) {
				return $repl;
			}
		}
	}
	else if(strlen($input) > strlen("varset:") && substr($input,0,strlen("varset:")) == "varset:") {
		$input = trim(substr($input,strlen("varset:")));
		$pos = strpos($input,":");
		$name = substr($input,0,$pos);
		$value = substr(strstr($input,":"),1);

		if(!isset($vars[$name])) {
			$vars[$name] = array();
		}
		array_push($vars[$name],$value);
		return $vars;
	}
	else if(strlen($input) > strlen("varclear:") && substr($input,0,strlen("varclear:")) == "varclear:") {
		$name = trim(substr($input,strlen("varclear:")));
		array_pop($vars[$name]);
		return $vars;
	}
	else if(strlen($input) > strlen("var:") && substr($input,0,strlen("var:")) == "var:") {
		$name = trim(substr($input,strlen("var:")));
		$array = $vars[$name];
		return $array[count($array) - 1];
	}
	else if(strlen($input) > strlen("eval:") && substr($input,0,strlen("eval:")) == "eval:") {
		$input = trim(substr($input,strlen("eval:")));
		ob_start();
		eval($input);
		return ob_get_clean();
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

	$vars = array();

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
				$replacement = $callback(substr($input,$openIndex,$closeIndex - $openIndex + 1),$page,$vars);
				if(is_string($replacement)) {
					$i += strlen($replacement) - ($closeIndex - $openIndex + 4);
					$input = substr($input,0,$openIndex - 2) . $replacement . substr($input,$closeIndex + 3);
				}
				else if(is_array($replacement)) {
					$vars = $replacement;
				}
			}

			//Clean up for next match
			$openIndex = -1;
			$closeIndex = -1;
		}
	}

	return template_match($input,$callback,$page,$level - 1);
}

//Taken from https://gist.github.com/lorenzos/1711e81a9162320fde20
function tailCustom($filepath, $lines = 1, $adaptive = true) {
	// Open file
	$f = @fopen($filepath, "rb");
	if ($f === false) return false;
	
	// Sets buffer size
	if (!$adaptive) $buffer = 4096;
	else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
	
	// Jump to last character
	fseek($f, -1, SEEK_END);
	
	// Read it and adjust line number if necessary
	// (Otherwise the result would be wrong if file doesn't end with a blank line)
	if (fread($f, 1) != "\n") $lines -= 1;
	
	// Start reading
	$output = '';
	$chunk = '';
	
	// While we would like more
	while (ftell($f) > 0 && $lines >= 0) {
		// Figure out how far back we should jump
		$seek = min(ftell($f), $buffer);
		
		// Do the jump (backwards, relative to where we are)
		fseek($f, -$seek, SEEK_CUR);
		
		// Read a chunk and prepend it to our output
		$output = ($chunk = fread($f, $seek)) . $output;
		
		// Jump back to where we started reading
		fseek($f, -strlen($chunk), SEEK_CUR);
		
		// Decrease our line counter
		$lines -= substr_count($chunk, "\n");
		
	}
	
	// While we have too many lines
	// (Because of buffer size we might have read too many)
	while ($lines++ < 0) {
		// Find first newline and remove all text before that
		$output = substr($output, strpos($output, "\n") + 1);
	}
	
	// Close file and return
	fclose($f);
	return trim($output);
}

function patternMatch($pattern,$test,$file_match = false) {
	if($file_match) {
		$pattern = cleanPath($pattern);
		$test = cleanPath($test);
		if(substr($pattern,0,1) == "/") {
			return patternMatch($pattern,$test);
		}
		else {
			$count = count(explode("/",$pattern));
			$parts = explode("/",$test);
			$test = implode("/",array_splice($parts,-$count));
			if(substr($test,0,1) == "/") {
				return false;
			}
			else {
				return patternMatch($pattern,$test);
			}
		}
	}
	else {
		return preg_match("#^" . strtr(preg_quote($pattern, '#'),
									   array('\*' => '[^\/]*', '\?' => '.')) . "$#", $test);
	}
}

function redirect($location) {
	header("Location: " . $location);
	exit();
}

function fatal_error() {
	redirect("error.php");
}

require(cleanPath(ROOT_DIR . "/db/db.php"));
require(cleanPath(ROOT_DIR . "/parsedown/Parsedown.php"));

if(!defined("NON-SECURED")) {
	session_save_path(cleanPath(ADMIN_DIR . "/session"));
	session_start();

	$action = isset( $_GET['action'] ) ? $_GET['action'] : "";

	//Only for login/logout page
	if(defined("LOGOUT")) {
		unset( $_SESSION['username'] );
	}
	//See if logged on already and not login authentication page
	else if(!defined("LOGIN") && !defined("ERROR") && !isset($_SESSION["username"])) {
		redirect("login.php?action=fail");
	}
	//Everything else. Do general permissions checking now
	else {
		$page = $_SERVER['PHP_SELF'];
		$perms = strtr($_SESSION["permissions"],array("\r\n" => "\n"));
		$permissions = explode("\n\n",$perms);
		for($i = 0;$i < count($permissions);$i++) {
			$permissions[$i] = explode("\n",$permissions[$i]);
			for($j = 0;$j < count($permissions[$i]);$j++) {
				$first = substr($permissions[$i][$j],0,1);
				$rest = substr($permissions[$i][$j],1);
				if($first == "+") {
					$permissions[$i][$j] = array();
					$permissions[$i][$j]["allowed"] = true;
					$permissions[$i][$j]["action"] = $rest;
				}
				else if($first == "-") { 
					$permissions[$i][$j] = array();
					$permissions[$i][$j]["allowed"] = false;
					$permissions[$i][$j]["action"] = $rest;
				}
				else {
					//unset($permissions[$i][$j]);
					$permissions[$i][$j] = "";
				}
			}
			$permissions[$i] = array_values(array_filter($permissions[$i]));
		}

		//Actually checking the permissions
		$allowed = true;
		$sub_perms = array();
		foreach($permissions as $permission) {
			if(count($permission) > 0) {
				$parts = explode("->",$permission[0]["action"]);
				$firstch = substr($parts[0],0,1);
				if($firstch != "/" && !($firstch == "*" || $firstch == "?")) {
					$parts[0] = ADMIN_RDIR . "/" . $parts[0];
				}
				cleanPath($parts[0]);
				echo $parts[0];
				if(patternMatch($parts[0],$page,true) &&
				   patternMatch($parts[1],$action)) {
					$allowed = $permission[0]["allowed"];

					for($i = 1;$i < count($permission);$i++) {
						array_push($sub_perms,$permission[$i]);
					}
				}
			}
		}
		if(!$allowed) {
			redirect(cleanPath(ADMIN_RDIR . "/permissions.php?action=denied"));
		}
	}
}

?>
