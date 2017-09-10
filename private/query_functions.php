<?php

function find_all($table) {
	return find($table);
}

function find_all_subjects() {
	return find_all("subjects");
}

function find_all_pages() {
	return find_all("pages");
}

function find_subject_by_id($id) {
	return fetch_single("subjects", $id);
}

function find_page_by_id($id) {
	return fetch_single("pages", $id);
}

function find_pages_by_subject_id($value) {
	return find_by("pages", "subject_id", $value);
}

function fetch_single($table, $id='') {
	$result = find($table, $id);
	$data = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	return $data;
}

function find($table, $id='') {
	return find_by($table, "id", $id);
}

function find_by($table, $column, $id) {
	global $db;
	$sql = "SELECT * FROM {$table} ";
	$sql .= $id === '' ? "" : "WHERE {$column}='" . db_escape($db, $id) . "' ";
	$sql .= "ORDER BY position ASC";
	$result = mysqli_query($db, $sql);
	confirm_result_set($result);
	return $result;
}

function insert_subject($subject) {
	$errors = validate_subject($subject);
	if (!empty($errors)) {
		return $errors;
	}

	return insert($subject, "subjects");
}

function insert_page($page) {
	$errors = validate_page($page);
	if (!empty($errors)) {
		return $errors;
	}

	return insert($page, "pages");
}

function update_subject($subject) {
	$errors = validate_subject($subject);
	if (!empty($errors)) {
		return $errors;
	}

  return update($subject, "subjects");
}

function update_page($page) {
	$errors = validate_page($page);
	if (!empty($errors)) {
		return $errors;
	}

  return update($page, "pages");
}

function subjects_count() {
	return count_data("subjects");
}

function pages_count() {
	return count_data("pages");
}

function delete_subject($id){
	return delete("subjects", $id);
}

function delete_page($id){
	return delete("pages", $id);
}

function insert($entries, $table) {
	global $db;
	$sql = construct_create_stmt($entries, $table);
	$result = mysqli_query($db, $sql);

	if ($result) {
		return $result;
	} else {
		echo mysqli_error($db);
		db_disconnect($db);
		exit;
	}
}

function update($entries, $table) {
	global $db;
	$sql = construct_update_stmt($entries, $table);
	$result = mysqli_query($db, $sql);

	if ($result) {
		return $result;
	} else {
		echo mysqli_error($db);
		db_disconnect($db);
		exit;
	}
}

function construct_create_column($entries) {
	global $db;
	$values = $columns = [];
	foreach ($entries as $key => $value) {
		$values[] = "'" . db_escape($db,$value) . "'";
		$columns[] =  $key;
	}
	return [$values, $columns];
}

function construct_create_stmt($entries, $table) {
	list($values, $columns) = construct_create_column($entries);

	$sql = "INSERT INTO {$table} (";
	$sql .= implode(',', $columns) . ") ";
	$sql .= "VALUES (";
	$sql .= implode(',', $values);
	$sql .= ")";

	return $sql;
}

function construct_update_columns($entries) {
	global $db;
	$values = [];
	$id = "";
	foreach ($entries as $key => $value) {
		if ($key == 'id') {
			$id = $value;
			continue;
		}
		$values[] = " {$key} = '" . db_escape($db, $value) . "'";
	}
	$values = implode(',', $values);
	$values .= " WHERE id='" . $id . "' ";

	return $values;
}

function construct_update_stmt($entries, $table) {
	$sql = "UPDATE {$table} SET ";
	$sql .= construct_update_columns($entries);
  $sql .= "LIMIT 1";
	
	return $sql;
}

function count_data($table) {
	global $db;
	$sql = "SELECT COUNT(*) AS total FROM {$table}";
	$set = mysqli_query($db, $sql);
	$count = mysqli_fetch_assoc($set)['total'];
  mysqli_free_result($set);
	return $count;
}

function delete($table, $id) {
	global $db;
	$sql = delete_stmt($table, $id);
	$result = mysqli_query($db, $sql);

	if ($result) {
		return $result;
	} else {
		echo mysqli_error($db);
		db_disconnect($db);
		exit;
	}
}

function delete_stmt($table, $id) {
	global $db;
	$sql = "DELETE FROM {$table} ";
  $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
  $sql .= "LIMIT 1";

  return $sql;
}

function validate($object) {

	$errors = [];

	// menu_name
	if (is_blank($object['menu_name'])) {
		$errors[] = "Name cannot be blank.";
	} elseif (!has_length($object['menu_name'], ['min' => 2, 'max' => 255])) {
		$errors[] = "Name must be between 2 and 255 characters.";
	}

	// position
	// Makes sure we are working with an integer
	$position_int = (int) $object['position'];
	if ($position_int <= 0 || !$position_int) {
		$errors[] = "Position must be greater than zero.";
	}
	if ($position_int > 999 || !$position_int) {
		$errors[] = "Position must be less than 999.";
	}

	// visible
	// Make sure we are working with a string
	$visible_str = (string) $object['visible'];
	if (!has_inclusion_of($visible_str, ["0", "1"])) {
		$errors[] = "Visible must be true or false.";
	}

	return $errors;
}

function validate_subject($subject) {
	$errors = validate($subject);

	$current_id = $subject['id'] ?? "0";
  if (!has_unique_menu_name("subjects", $subject['menu_name'], $current_id)) {
  	$errors[] = "Menu name must be unique.";
  }

  return $errors;
}

function validate_page($page) {
	$errors = validate($page);

	// content
  if(is_blank($page['content'])) {
    $errors[] = "Content cannot be blank.";
  }

	if(is_blank($page['subject_id'])) {
    $errors[] = "Subject cannot be blank.";
  }

  $current_id = $page['id'] ?? "0";
  if (!has_unique_menu_name("pages", $page['menu_name'], $current_id)) {
  	$errors[] = "Menu name must be unique.";
  }

	return $errors;
}