<?php
include('includes/init.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />

  <title>View Image</title>
</head>

<body>
  <?php
  include('includes/header.php');

  if(isset($_GET["image_id"])) {
    // retrieve image metadata
    $img_id = filter_input(INPUT_GET, "image_id", FILTER_SANITIZE_NUMBER_INT);

    $sql = "SELECT * FROM images INNER JOIN users ON images.creator_id = users.user_id WHERE image_id = :img_id;";
    $params = array(":img_id" => $img_id);
    $cur_img = exec_sql_query($db, $sql, $params)->fetchAll();

    $title = $cur_img[0]['image_title'];
    $description = $cur_img[0]['image_description'];
    $ext = $cur_img[0]['ext'];

    $img_dir = "/uploads";
    $cur_img_file = $img_dir . "/" . $img_id . "." . $ext;

    $creator = $cur_img[0]['username'];

    // delete a tag if user specified
    if(isset($_POST["delete_tag"]) and $current_user == $creator) {
      $tag_to_delete = filter_input(INPUT_POST, 'tag_to_delete', FILTER_SANITIZE_STRING);
      $sql = "DELETE FROM images_tags_mapping
              WHERE image_id = :img_id
              AND tag_id IN (SELECT tag_id FROM tags WHERE tag = :tag);";
      $params_ = array(":img_id" => $img_id, ":tag" => $tag_to_delete);
      if(!exec_sql_query($db, $sql, $params_)){
        array_push($err, "Failed to delete tag $tag_to_delete.");
      }
    }

    // delete the image if user specified
    if(isset($_POST["delete"]) and $current_user == $creator) {
      $sql = "DELETE FROM images WHERE image_id = :img_id;";
      if(exec_sql_query($db, $sql, $params)){
        $sql = "DELETE FROM images_tags_mapping WHERE image_id = :img_id;";
        if(exec_sql_query($db, $sql, $params)) {
          unlink("." . $cur_img_file);
          go_to_home_page();
        }
      }
      else {
        array_push($err, "Failed to delete this image.");
      }
    }

    // add a tag specified by user
    if(isset($_POST["add_tag"]) and isset($_POST['tag'])) {
      $tag = trim(filter_input(INPUT_POST, 'tag', FILTER_SANITIZE_STRING));
      if(!empty($tag)) {
        if($tag[0] == '#') {
          $tag = substr($tag, 1);
        }

        if(!empty($tag)) {
          add_tag_to_image($tag, $img_id);
        }
      }
    }

    // render out image data ---- title, description, tags etc.
    echo "
      <div id='cur_img_view'>
        <img src=$cur_img_file alt=\"$title\" id='cur_img'>
        <div id='cur_img_title'>
          <p>".
            htmlspecialchars($title)."
          </p>
        </div>
        <div id='cur_img_creator'>
          <p>Uploaded by ".
            htmlspecialchars($creator)."
          </p>
        </div>
        <div id='cur_img_description'>
          <p>Description: ". htmlspecialchars($description).
          "</p>
        </div>
        <div id='cur_img_tags'>
          <label>Tags:</label>
          <div>";
    display_tags($img_id, FALSE);

    echo "  <form method='post' id='add_tag_form' autocomplete='off'>
              <label>#</label>
              <input list='tags' name='tag' id='tag_input' placeholder=\"Choose or create tags\" maxlength=\"16\">
              <datalist id='tags'>";
    echo_all_tags();
    echo "    </datalist>
              <input type='submit' name='add_tag' id='add_tag_btn' value='Add A Tag'>
            </form>
          </div>
        </div>";

    // if the viewer is the uploader, show the viewer delete options
    if($creator == $current_user) {
      echo "<div>
              <br><label id='creator_area'>Creator Only:</label><br>
              <form action='/view_image.php?image_id=$img_id' method='post' id='delete_tag'>
                <select name='tag_to_delete'>";
      echo_all_tags_of_img($img_id);
      echo "    </select>
                <input type='submit' name='delete_tag' value='Delete Tag'>
              </form>
              <span id='or'>OR</span>
              <form action='/view_image.php?image_id=$img_id' method='post' id='delete_btn'>
                <input type='submit' name='delete' value='Delete This Image'>
              </form>
            </div>";
    }

    echo "</div>";

    if(sizeof($err) > 0) {
      show_err();
    }
  }
  ?>

</body>
</html>
