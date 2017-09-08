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