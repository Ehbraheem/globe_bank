<?php 
$PRIVATE_PATH='../../../private/';
require_once $PRIVATE_PATH . 'initialize.php';
require_login();
require_once PUBLIC_PATH . '/staff/pages/form_processor.php';

if(is_post_request()) {

  // Handle form values sent by new.php
  $page = make_page();
  $result = insert_page($page);
  if ($result === true) {
    $new_id = mysqli_insert_id($db);
    $_SESSION['status'] = "The Page was created Successfully.";
    redirect_to("/staff/pages/show.php?id=" . $new_id);
  } else {
    $errors = $result;
    // var_dump($errors);
  }
  
} else {
  
}

$page = [];
$page['subject_id'] = $_GET['subject_id'] ?? '1';
$page['menu_name'] = '';
$page['position'] = '';
$page['visible'] = '';
$page['content'] = '';

$page['position'] = $page_count = count_pages_by_subject_id(['subject_id'=>$page['subject_id']]) + 1;
   
$visible = '';

$page_title = 'Create Page';
include(SHARED_PATH . '/staff_header.php');
?>

<div id="content">

  <a class="back-link" href="<?php echo url_for('/staff/subjects/show.php?id=' . h(u($page['subject_id']))); ?>">&laquo; Back to Subject</a>

  <div class="page new">
    <h1>Create Page</h1>

    <?php echo display_errors($errors); ?>

    <form action="<?php echo url_for('/staff/pages/new.php'); ?>" method="post">
      <dl>
        <dt>Subject</dt>
        <dd>
          <select name="subject_id">
          <?php
            $subject_set = find_all_subjects();
            while($subject = mysqli_fetch_assoc($subject_set)) {
              echo "<option value=\"" . h($subject['id']) . "\"";
              if($page["subject_id"] == $subject['id']) {
                echo " selected";
              }
              echo ">" . h($subject['menu_name']) . "</option>";
            }
            mysqli_free_result($subject_set);
          ?>
          </select>
        </dd>
      </dl>
      <dl>
        <dt>Menu Name</dt>
        <dd><input type="text" name="menu_name" value="<?php echo h($page['menu_name']); ?>" /></dd>
      </dl>
      <dl>
        <dt>Position</dt>
        <dd>
          <select name="position">
            <?php
              for($i=1; $i <= $page_count; $i++) {
                echo "<option value=\"{$i}\"";
                if($page["position"] == $i) {
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
          <input type="checkbox" name="visible" value="1"<?php if($page['subject_id'] == "1") { echo " checked"; } ?> />
        </dd>
      </dl>
      <dl>
        <dt>Content</dt>
        <dd>
          <textarea name="content" cols="60" rows="10"><?php echo h($page['content']); ?></textarea>
        </dd>
      </dl>
      <div id="operations">
        <input type="submit" value="Create Page" />
      </div>
    </form>

  </div>

</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>