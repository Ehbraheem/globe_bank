<?php 

$PRIVATE_PATH='../../../private/';

require_once $PRIVATE_PATH . 'initialize.php';
require_login();

redirect_to('/staff/index.php');

?>