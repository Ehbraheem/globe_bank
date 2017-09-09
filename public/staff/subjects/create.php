<?php 

$PRIVATE_PATH='../../../private/';

require_once $PRIVATE_PATH . 'initialize.php';
require_once PUBLIC_PATH . '/staff/subjects/form_processor.php';

if (is_post_request()) {

	//  Handle form values sent by new.php

	$subject = make_subject();

	$result =  insert_subject($subject);
	$new_id = mysqli_insert_id($db);
	redirect_to("/staff/subjects/show.php?id=" . $new_id);
	
} else {
	redirect_to('/staff/subjects/new.php');
}