<?php
include('includes/init.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />

  <title>Home</title>
</head>


<body>
  <?php
  include('includes/header.php');

  // display images by tags
  if(isset($_GET["tag"])) {
    $tag = filter_input(INPUT_GET, 'tag', FILTER_SANITIZE_STRING);
    $sql = "SELECT DISTINCT images.image_id, image_title, ext
    FROM tags INNER JOIN images_tags_mapping ON tags.tag_id = images_tags_mapping.tag_id
    INNER JOIN images ON images.image_id = images_tags_mapping.image_id
    WHERE tags.tag LIKE '%' || :tag || '%';";
    $params = array(":tag" => $tag);
  }

  //display images by tag id
  else if(isset($_GET["tag_id"])) {
    $tag_id = filter_input(INPUT_GET, 'tag_id', FILTER_SANITIZE_STRING);
    $sql = "SELECT DISTINCT images.image_id, image_title, ext
    FROM images_tags_mapping INNER JOIN images ON images.image_id = images_tags_mapping.image_id
    WHERE tag_id = :tag_id;";
    $params = array(":tag_id" => $tag_id);
  }

  // display all images
  else {
    $sql = "SELECT image_id, image_title, ext FROM images;";
    $params = array();
  }

  $imgs = exec_sql_query($db, $sql, $params)->fetchAll();
  ?>
  <div id="photo_grids">
    <?php
    // search bar that allows user to view all tags can search images with a specific tag
    echo "<div id='home_search_bar'>
          <form id='search_tag_form' autocomplete='off'>
            <label>Welcome";

    if($current_user) {
      echo " $current_user, ";
    }
    else {
      echo ", ";
    }

    echo    "browse photos with tag #</label>
            <select name='tag' id='tags'>
            <option value=\"\" selected disabled>View All Tags</option>";
    echo_all_tags();

    echo "  </select>
            <input type='submit' name='search_tag' value='Search Tag'>
          </form>
        </div>";

    // Show all images we have queried before
    if(sizeof($imgs) == 0 && isset($tag)) {
      echo "<h3>No images with tag \"$tag\"</h3>";
    }
    else {
      for($i = sizeof($imgs) - 1; $i >= 0; $i--) {
        /* Image source: I took these images */
        show_preview_grid($imgs, $i);
      }
    }
    ?>
  </div>
</body>
</html>
