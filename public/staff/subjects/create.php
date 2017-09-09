<?php 

$PRIVATE_PATH='../../../private/';

require_once $PRIVATE_PATH . 'initialize.php';

if (is_post_request()) {

	//  Handle form values sent by new.php

	$menu_name = $_PORT['menu_name'] ?? '';
	$position = $_PORT['position'] ?? '';
	$visible = $_PORT['visible'] ?? '';

	echo "Form parameters<br />";
	echo "Menu name: " . $menu_name . "<br />";
	echo "Position: " . $position . "<br />";
	echo "Visible: " . $position . "<br />";
} else {
	redirect_to('/staff/subjects/new.php');
}