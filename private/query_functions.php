<?php

function find_all($table, $options=[], $order="") {
	return find_by($table, $options);
}

function find_all_subjects($options=[]) {
	$order = "ORDER BY position ASC";
	return find_all("subjects", $options, $order);
}

function find_all_pages($options=[]) {
	$order = "ORDER BY position ASC";
	return find_all("pages", $options, $order);
}

function find_all_admins($options=[]) {
	$order = "ORDER BY last_name ASC, first_name ASC";
	return find_all("admins", $options, $order);
}

function find_subject_by_id($options=[]) {
	return fetch_single("subjects", $options);
}

function find_page_by_id($options=[]) {
	return fetch_single("pages", $options);
}

function find_admin_by_id($options){
	return fetch_single("admins", $options);
}

function find_admin_by_username($options){
	return fetch_single("admins", $options);
}

function find_pages_by_subject_id($options) {
	return find_by("pages", $options);
}

function fetch_single($table, $options) {
	$result = find($table, $options);
	$data = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	return $data;
}

function find($table, $options=[]) {
	return find_by($table, $options);
}

function find_by($table, $options=[], $order="") {
	global $db;
	$query = $options ? construct_find_stmt($options) : "";
	$sql = "SELECT * FROM {$table} ";
	$sql .= $query . " ";
	$sql .= $order ? $order : "";
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

function insert_admin($admin) {
	$errors = validate_admin($admin);
	if (!empty($errors)) {
		return $errors;
	}
	$admin['hashed_password'] = password_hash($admin['password'], PASSWORD_BCRYPT);
	unset($admin['password']);
	unset($admin['confirm_password']);
	return insert($admin, "admins");
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

function update_admin($admin) {
	$password_sent = !is_blank($admin['password']);
	$errors = validate_admin($admin, ['password_required'=> $password_sent]);
	if (!empty($errors)) {
		return $errors;
	}
	$admin['hashed_password'] = password_hash($admin['password'], PASSWORD_BCRYPT);
	unset($admin['password']);
	unset($admin['confirm_password']);
	return update("admins", $admin);
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

function delete_admin($id) {
	return delete("admins", $id);
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

function validate_admin($admin, $options=[]) {

	$errors = [];

	if (is_blank($admin['first_name'])) {
		$errors[] = "First name cannot be blank.";
	} elseif (!has_length($admin['first_name'], ['min' => 2, 'max' => 255])) {
		$errors[] = "First Name must be between 2 and 255 characters.";
	}

	if (is_blank($admin['last_name'])) {
		$errors[] = "Last name cannot be blank.";
	} elseif (!has_length($admin['last_name'], ['min' => 2, 'max' => 255])) {
		$errors[] = "Last Name must be between 2 and 255 characters.";
	}

	if (is_blank($admin['email'])) {
		$errors[] = "First name cannot be blank.";
	} elseif (!has_length($admin['email'], ['max' => 255])) {
		$errors[] = "Email must not be above 255 characters.";
	} elseif (!has_valid_email_format($admin['email'])) {
		$errors[] = "Email must be a valid format.";
	}


	if(is_blank($admin['username'])) {
    $errors[] = "Username cannot be blank.";
  } elseif (!has_length($admin['username'], array('min' => 8, 'max' => 255))) {
    $errors[] = "Username must be between 8 and 255 characters.";
  } elseif (!has_unique_username($admin['username'], $admin['id'] ?? 0)) {
    $errors[] = "Username not allowed. Try another.";
  }

  if (!isset($options['password_required']) {
  	if(is_blank($admin['password'])) {
      $errors[] = "Password cannot be blank.";
    } elseif (!has_length($admin['password'], array('min' => 12))) {
      $errors[] = "Password must contain 12 or more characters";
    } elseif (!preg_match('/[A-Z]/', $admin['password'])) {
      $errors[] = "Password must contain at least 1 uppercase letter";
    } elseif (!preg_match('/[a-z]/', $admin['password'])) {
      $errors[] = "Password must contain at least 1 lowercase letter";
    } elseif (!preg_match('/[0-9]/', $admin['password'])) {
      $errors[] = "Password must contain at least 1 number";
    } elseif (!preg_match('/[^A-Za-z0-9\s]/', $admin['password'])) {
      $errors[] = "Password must contain at least 1 symbol";
    }

    if(is_blank($admin['confirm_password'])) {
      $errors[] = "Confirm password cannot be blank.";
    } elseif ($admin['password'] !== $admin['confirm_password']) {
      $errors[] = "Password and confirm password must match.";
    }
  }
	

    return $errors;
}

function construct_find_stmt($options) {
	global $db;
	if (empty($options)) {
		return;
	}
	$stmt_array = [];
	foreach ($options as $key => $value) {
		$stmt_array[] = $key . "=" . "'" . db_escape($db, $value) . "'";
	}
	return  "WHERE " . implode(" AND ", $stmt_array);
}