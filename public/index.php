<?php require_once('../private/initialize.php'); ?>

<?php include(SHARED_PATH . '/public_header.php'); ?>

<?php 

	$preview = false;
	if (isset($_GET['preview'])) {
		$preview = $_GET['preview'] === 'true' && is_logged_in() ? true : false;
	}

	$visible = !$preview;

	if (isset($_GET['id'])) {
		$page_id = $_GET['id'];
		$page = find_page_by_id(['id' => $page_id, "visible" => $visible]);
		if (!$page) {
			redirect_to('/index.php');
		}
		$subject_id = $page['subject_id'];
		$subject = find_subject_by_id(["id" => $subject_id, "visible" => $visible]);
		if (!$subject) {
			redirect_to('/index.php');
		}
	} elseif (isset($_GET['subject_id'])) {
		$subject_id = $_GET['subject_id'];
		$subject = find_subject_by_id(["id" => $subject_id, "visible" => $visible]);
		if (!$subject) {
			redirect_to('/index.php');
		}
		$page_set = find_pages_by_subject_id(["visible" => $visible, "subject_id" => $subject_id]);
		$page = mysqli_fetch_assoc($page_set);
		if (!$page) {
			redirect_to('/index.php');
		}
		$page_id = $page['id'];
	} else {

	}

 ?>

<div id="main">

	<?php include(SHARED_PATH . '/public_navigation.php'); ?>

  <div id="page">

  	<?php 
	  	if (isset($page)) {
	  		// show the page from the database
	  		// TODO: add html escaping back in
	  		$allowed_tags = '<div><img><h1><h2><h3><h4><h5><h6><p><br><strong><em><ul><li>';
	  		echo strip_tags($page['content'], $allowed_tags);

	  	} else {
	  		// Show the homepage
		  	// The homepage content could:
		  	// * be static content (here or in a shared file)
		  	// * show the first page from the nav
		  	// * be in the database but add code to hide in the nav
	  		include(SHARED_PATH . '/static_homepage.php');
	  	}
	  	
  	 ?>

  </div>

</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
