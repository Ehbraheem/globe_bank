<?php  

// is_blank("absd")
// * validates data presence
// * uses trim() so empty spaces don't count
// * uses === to avoid false positives
// * better than empty() which consider "0" to be empty
function is_blank($value) {
	return !isset($value) || trim($value) === '';
}

// has_presence('adcd')
// * validates data presence
// * reverse of is_blank()
function has_presence($value) {
	return !is_blank($value);
}

// has_length_greater_than('abcd', 3)
// * validates string length
// * spaces count towards length
// * use trim() if spaces should not count
function has_length_greater_than($value, $min){
	$length = strlen($value);
	return $length > $min;
}

// has_length_less_than('abcd', 5)
// * validates string length
// * spaces count towards length
// * use trim() if spaces should not count
function has_length_less_than($value, $max){
	$length = strlen($value);
	return $length < $max;
}

// has_length_exactly('abcd', 4)
// * validates string length
// * spaces count towards length
// * use trim() if spaces should not count
function has_length_exactly($value, $exact){
	$length = strlen($value);
	return $length == $exact;
}

// has_length('abcd', ['min' => 3, 'max' => 5])
// * validates string length
// * combine functions_greater_than, _less_than, _exactly
// * spaces count towards length
// * use trim() if spaces should not count
function has_length($value, $options){
	if (isset($options['min']) && !has_length_greater_than($value, $options['min'] - 1)) {
		return false;
	} elseif (isset($options['max']) && !has_length_less_than($value, $options['max'] + 1)) {
		return false;
	} elseif (isset($options['exact']) && !has_length_exactly($value, $options['exact'])) {
		return false;
	} else {
		return true;
	}
}

// has_inclusion_of(5, [1,2,3,4,5,6])
// * validates inclusion in a set
function has_inclusion_of($value, $set){
	return in_array($value, $set);
}

// has_exclusion_of(5, [1,2,3,4,5,6])
// * validates exclusion in a set
function has_exclusion_of($value, $set){
	return !in_array($value, $set);
}

// * has_string('nobody@nowhere.com', '.com')
// * validates inclusion of character(s)
// * strpos returns string start position of false
// * uses !== to prevent position 0 from being considered or false
// * strpos is faster than preg_match()
function has_string($value, $required_string) {
	return strpos($value, $required_string) !== false;
}

// * has_valid_email_format('nobody@nowhere.com')
// * validates correct format for email addresses
// * format: [chars]@[chars].[2+ letters]
// * preg_match is helpful, uses a regular expresion
//     returns 1 for a match, 0 for no match
//     http://php.net/manual/en/function.preg-match.php
function has_valid_email_format($value) {
	$email_regex = '/\A[A-Z0-9._%+-]+@[A-Z0-9.-]+[A-Z]{2,}\Z/i';
	return preg_match($email_regex, $value) === 1;
}

// has_unique_menu_name('History')
  // * Validates uniqueness of $column
  // * For new records, provide only the $column.
  // * For existing records, provide current ID as second arugment
  function has_unique($table, $column, $menu_name, $current_id="0") {
    global $db;

    $sql = "SELECT * FROM {$table} ";
    $sql .= "WHERE {$column}='" . db_escape($db, $menu_name) . "' ";
    $sql .= "AND id != '" . db_escape($db, $current_id) . "'";

    $doc_set = mysqli_query($db, $sql);
    $doc_count = mysqli_num_rows($doc_set);
    mysqli_free_result($doc_set);

    return $doc_count === 0;
  }

  function has_unique_username($username, $current_id="0"){
  	return has_unique("admins", "username", $username, $current_id);
  }

  function has_unique_menu_name($table, $menu_name, $current_id="0") {
  	return has_unique($table, "menu_name", $menu_name, $current_id);
  }