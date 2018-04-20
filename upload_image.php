<?php
include('includes/init.php');
const IMAGE_UPLOADS_PATH = "uploads/";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />

  <title>Upload Images</title>
</head>

<body id="upload_image_body">
  <?php
  include('includes/header.php');

  if(!$current_user) {
    go_to_home_page();
  }

  // upload image to uploads folder
  if (isset($_FILES['new_image_file'])) {
    $new_image = $_FILES['new_image_file'];

    // retrieve image metadata
    if($new_image['error'] == UPLOAD_ERR_OK) {
      $image_info = pathinfo($new_image['name']);
      $ext = strtolower($image_info['extension']);

      $img_title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));

      if(isset($_POST['description_field'])) {
        $description = trim(filter_input(INPUT_POST, 'description_field', FILTER_SANITIZE_STRING));
      }
      else {
        $description = NULL;
      }

      $sql = "SELECT user_id FROM users WHERE username = :username;";
      $params = array(":username" => $current_user);
      $res = exec_sql_query($db, $sql, $params)->fetchAll();

      // upload user typed data to database
      if($res) {
        $creator_id = $res[0]['user_id'];

        $sql = "INSERT INTO images (image_title, image_description, creator_id, ext)
                VALUES (:image_title, :image_description, :creator_id, :ext);";

        $params = array(":image_title" => $img_title,
                        ":image_description" =>$description,
                        ":creator_id" => $creator_id,
                        ":ext" => $ext);

        $res = exec_sql_query($db, $sql, $params);

        // upload image
        if($res) {
          array_push($msg, "Your image has been uploaded successfully.");
          $last_id = $db->lastInsertId("image_id");
          move_uploaded_file($new_image['tmp_name'], IMAGE_UPLOADS_PATH."$last_id.$ext");

          // upload a tag defined by user their uploaded image
          if(isset($_POST["tag"])) {
            $tag = trim(filter_input(INPUT_POST, 'tag', FILTER_SANITIZE_STRING));
            if(!empty($tag)) {
              if($tag[0] == '#') {
                $tag = substr($tag, 1);
              }

              if(!empty($tag)) {
                add_tag_to_image($tag, $last_id);
              }
            }
          }

          go_to_home_page();
        }
        else {
          array_push($err, "Failed to upload your image.");
        }
      }
      else {
        array_push($err, "Failed to upload your image.");
      }
    }
    else {
      array_push($err, "Failed to upload your image.");
    }
  };

  if(sizeof($err) > 0) {
    show_err();
  }
  ?>
  <div id="form_field">
    <h1>UPLOAD AN IMAGE</h1>
    <form id="form_field_form" action="upload_image.php" method="post" enctype="multipart/form-data" autocomplete="off">
      <div class="input_field">
        <label>Choose Image:</label>
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
        <input type="file" name="new_image_file" required>
      </div>
      <br><br>
      <div class="input_field">
        <label>Title:</label>
        <input type="text" name="title" required>
      </div>
      <br><br>
      <div class="input_field">
        <label>Description (Optional):</label><br>
        <textarea name="description_field" rows="5" id="description_field"></textarea>
      </div>
      <br><br>
      <div class="input_field">
        <label>Add A Tag (Optional):</label>
        <!--input type="text" name="tag"-->
        <input list="tags" name="tag" placeholder="Double click to choose existing tags" maxlength="16">
        <datalist id="tags">
          <?php
          echo_all_tags();
          ?>
        </datalist>
      </div>
      <br><br>
      <div class="input_field">
        <input type="submit" id="form_field_btn" value="Upload Image">
      </div>
    </form>
  </div>
</body>
</html>
