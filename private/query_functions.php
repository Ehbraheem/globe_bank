<?php

function find_all($table) {
	global $db;
	$sql = "SELECT * FROM {$table} ";
	$sql .= "ORDER BY position ASC";
	$result = mysqli_query($db, $sql);
	confirm_result_set($result);
	return $result;
}

function find_all_subjects() {
	return find_all("subjects");
}

function find_all_pages() {
	return find_all("pages");
}