<?php $PRIVATE_PATH='../../private/' ?>

<?php $page_title = "Staff Area"; ?>

<?php require_once $PRIVATE_PATH . 'initialize.php'; ?>

<?php include SHARED_PATH . '/staff_header.php'; ?>

<div id="content">
<div id="main-menu">
	<h2>Main Menu</h2>
	<ul>
		<li><a href="<?php echo url_for('/staff/subjects/index.php'); ?>">Subjects</a></li>
	</ul>
</div>
</div>

<?php echo '<pre>' . print_r($_SERVER) . '</pre>' ?>

<?php include SHARED_PATH . '/staff_footer.php'; ?>