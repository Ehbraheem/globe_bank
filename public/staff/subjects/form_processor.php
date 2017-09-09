<?php 

function make_subject() {
	$subject = [];
  $subject['menu_name'] = $_POST['menu_name'] ?? '';
  $subject['position'] = $_POST['position'] ?? '';
  $subject['visible'] = $_POST['visible'] ?? '';
  if (isset($id)) {
  	$subject['id'] = $id;
  }

  return $subject;
}