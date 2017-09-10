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

function is_post_request() {
	return requestCheck('POST');
}

function is_get_request() {
	return requestCheck('GET');
}

function requestCheck($method) {
	return $_SERVER['REQUEST_METHOD'] == $method;
}

function display_errors($errors) {
	$output = '';
	if (!empty($errors)) {
		$output .= "<div class=\"errors\">";
		$output .= "Please fix the following errors:";
		$output .= "<ul>";
		foreach ($errors as $error) {
			$output .= "<li>" . h($error) . "</li>";
		}
		$output .= "</ul>";
		$output .= "</div>";
	}
	return $output;
}

function get_and_clear_session_message() {
	if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
		$msg = $_SESSION['status'];
		unset($_SESSION['status']);
		return $msg;
	}
}

function display_session_message() {
	$msg = get_and_clear_session_message();
	if(!is_blank($msg)) {
		return '<div id="message">' . h($msg) . '</div>';
	}
}
// function get_and_clear_session_message() {
//   if(isset($_SESSION['message']) && $_SESSION['message'] != '') {
//     $msg = $_SESSION['message'];
//     unset($_SESSION['message']);
//     return $msg;
//   }
// }

// function display_session_message() {
//   $msg = get_and_clear_session_message();
//   if(!is_blank($msg)) {
//     return '<div id="message">' . h($msg) . '</div>';
//   }
// }