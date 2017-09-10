<?php

$PRIVATE_PATH='../../../private/';
require_login();

require_once $PRIVATE_PATH . 'initialize.php'; 
require_once PUBLIC_PATH . '/staff/subjects/form_processor.php';

if (is_post_request()) {

  //  Handle form values sent by new.php

  $subject = make_subject();

  $result =  insert_subject($subject);

  if ($result === true) {
    $new_id = mysqli_insert_id($db);
    $_SESSION['status'] = "The Subject was created Successfully.";
    redirect_to("/staff/subjects/show.php?id=" . $new_id);
  } else {
    $errors = $result;
    // var_dump($errors);
  }
  
  
} else {
  
}

$subject = [];
$subject_count = subjects_count() + 1;
$subject['position'] = $subject_count;

?>
<?php $page_title = 'Create Subject'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/subjects/index.php'); ?>">&laquo; Back to List</a>

  <div class="subject new">
    <h1>Create Subject</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/subjects/new.php'); ?>" method="post">
      <dl>
        <dt>Menu Name</dt>
        <dd><input type="text" name="menu_name" value="" /></dd>
      </dl>
      <dl>
        <dt>Position</dt>
        <dd>
          <select name="position">
            <?php
              for($i=1; $i <= $subject_count; $i++) {
                echo "<option value=\"{$i}\"";
                if($subject["position"] == $i) {
                  echo " selected";
                }
                echo ">{$i}</option>";
              }
            ?>
            </select>
        </dd>
      </dl>
      <dl>
        <dt>Visible</dt>
        <dd>
          <input type="hidden" name="visible" value="0" />
          <input type="checkbox" name="visible" value="1" />
        </dd>
      </dl>
      <div id="operations">
        <input type="submit" value="Create Subject" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
