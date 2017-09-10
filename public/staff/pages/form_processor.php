<?php 

function make_page($id=''){
	$page['subject_id'] = $_POST['subject_id'] ?? '';
  $page['menu_name'] = $_POST['menu_name'] ?? '';
  $page['position'] = $_POST['position'] ?? '';
  $page['visible'] = $_POST['visible'] ?? '';
  $page['content'] = $_POST['content'] ?? '';
  if ($id) {
  	$page['id'] = $id;
  }

  return $page;
}