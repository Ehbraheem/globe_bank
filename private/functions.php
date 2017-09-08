<?php 

function url_for($script_path) {
 	// add the leading '/' if not present
 	if ($script_path[0] != '/') {
 		$script_path = "/" . $script_path;
 	}

 	return WWW_ROOT . $script_path;
}

function u($value='') {
	return urlencode($value);
}

function raw_u($value='') {
	return rawurlencode($value);
}

function h($value='') {
	return htmlspecialchars($value);
}

function error_404() {
	error("404 Not found");
}

function error_500() {
	error("500 Internal Server Error");
}

function error($value='') {
	header($_SERVER["SERVER_PROTOCOL"] . " {$value}");
	exit();
}

function redirect_to($location) {
	header("Location: " . url_for($location));
	exit;
}