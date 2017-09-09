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

function fetch_single($table, $id='') {
	$result = find($table, $id);
	$data = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	return $data;
}

function find($table, $id='') {
	global $db;
	$sql = "SELECT * FROM {$table} ";
	$sql .= $id === '' ? "" : "WHERE id='{$id}' ";
	$sql .= "ORDER BY position ASC";
	$result = mysqli_query($db, $sql);
	confirm_result_set($result);
	return $result;
}

function insert_subject($subject) {
	return insert($subject, "subjects");
}

function insert_page($page) {
	return insert($page, "pages");
}

function update_subject($subject) {
  return update($subject, "subjects");
}

function update_page($page) {
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
	$values = $columns = [];
	foreach ($entries as $key => $value) {
		$values[] = "'" . $value . "'";
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
	$values = [];
	$id = "";
	foreach ($entries as $key => $value) {
		if ($key == 'id') {
			$id = $value;
			continue;
		}
		$values[] = " {$key} = '" . $value . "'";
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
	$sql = "DELETE FROM {$table} ";
  $sql .= "WHERE id='" . $id . "' ";
  $sql .= "LIMIT 1";

  return $sql;
}