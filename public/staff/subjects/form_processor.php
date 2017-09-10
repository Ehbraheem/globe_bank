<?php 

function make_subject($id='') {
	$subject = [];
  $subject['menu_name'] = $_POST['menu_name'] ?? '';
  $subject['position'] = $_POST['position'] ?? '';
  $subject['visible'] = $_POST['visible'] ?? '';
  if ($id) {
  	$subject['id'] = $id;
  }
  return $subject;
}