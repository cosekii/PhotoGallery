<?php

$msg = array();
$err = array();

// display normal messages to user
function show_msg() {
  global $msg;
  foreach ($msg as $m) {
    echo "<p class='message'>" . htmlspecialchars($m) . "</p>";
  }
  $msg = array();
}

// display error messages to user
function show_err() {
  global $err;
  foreach ($err as $e) {
    echo "<p class='error'>" . htmlspecialchars($e) . "</p>";
  }
  $err = array();
}

// execute query with database, source: lab code
function exec_sql_query($db, $sql, $param) {
  $query = $db->prepare($sql);
  if ($query and $query->execute($param)) {
    return $query;
  }
  return NULL;
}

// YOU MAY COPY & PASTE THIS FUNCTION WITHOUT ATTRIBUTION.
// open connection to database
function open_or_init_sqlite_db($db_filename, $init_sql_filename) {
  if (!file_exists($db_filename)) {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db_init_sql = file_get_contents($init_sql_filename);
    if ($db_init_sql) {
      try {
        $result = $db->exec($db_init_sql);
        if ($result) {
          return $db;
        }
      } catch (PDOException $exception) {
        // If we had an error, then the DB did not initialize properly,
        // so let's delete it!
        unlink($db_filename);
        throw $exception;
      }
    }
  } else {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }
  return NULL;
}

$db = open_or_init_sqlite_db("gallery.sqlite", "init/init.sql");
$img_dir = '../uploads';

// display all tags to our user
function echo_all_tags() {
  global $db;
  $sql = "SELECT tag FROM tags";
  $params = array();
  $res = exec_sql_query($db, $sql, $params)->fetchAll();

  foreach ($res as $tag => $tag_name) {
    echo "<option value=\"".htmlspecialchars($tag_name[0])."\">".htmlspecialchars($tag_name[0])."</option>";
  }
}

// display all tags for a specific image
function echo_all_tags_of_img($image_id) {
  global $db;
  $sql = "SELECT tag FROM tags WHERE tag_id IN
          (SELECT tag_id FROM images_tags_mapping WHERE image_id = :img_id)";
  $params = array(":img_id" => $image_id);
  $tags = exec_sql_query($db, $sql, $params)->fetchAll();

  foreach ($tags as $tag => $tag_name) {
    echo "<option value=\"".htmlspecialchars($tag_name[0])."\">".htmlspecialchars($tag_name[0])."</option>";
  }
}

// render out the image grids in homepage
function show_preview_grid($imgs, $i) {
  $img_id = $imgs[$i]['image_id'];
  $title = $imgs[$i]['image_title'];
  $ext = $imgs[$i]['ext'];

  global $img_dir;
  $img_file = $img_dir . "/" . $img_id . "." . $ext;
  ?>
  <div class="preview">
    <?php
    echo "<a href='view_image.php?image_id=".rawurlencode($img_id)."'>";
    echo "<img src='$img_file' alt='$title'>
            <div class='prev_info'>
              <p>".
                htmlspecialchars($title)."
              </p>";
    echo "</div></a>";
    echo "<div class='tag_area'>";
    display_tags($img_id, TRUE);
    echo "</div>";
    ?>
  </div>
  <?php
}

// show tags bubble in homepage and view_image page
function display_tags($img_id, $is_preview) {
  global $db;
  $sql = "SELECT tag FROM tags WHERE tag_id IN
          (SELECT tag_id FROM images_tags_mapping WHERE image_id = :img_id)";
  $params = array(":img_id" => $img_id);
  $tags = exec_sql_query($db, $sql, $params)->fetchAll();

  for($i = 0; $i < sizeof($tags); $i++) {
    if($is_preview && $i >= 2) {
      break;
    }
    $tag = htmlspecialchars($tags[$i]['tag']);
    if($is_preview) {
      echo "<div class='tag_prev'>
              <p>
                <span class='hashtag'>#</span>
                <a href='/index.php?tag=".rawurlencode($tag)."'>" . htmlspecialchars($tag)."</a>
              </p>
            </div>";
    }
    else {
      echo "<div class='tag_curview'>
              <p>
                <span class='hashtag'>#</span>
                <a href='/index.php?tag=".rawurlencode($tag)."'>".htmlspecialchars($tag)."</a>
              </p>
            </div>";
    }
  }
}

// add tags and their corresponding images to database
function add_tag_to_image ($tag, $image_id) {
  global $db;
  global $err;
  $sql = "SELECT * FROM tags WHERE tags.tag = :tag";
  $params = array(":tag" => $tag);
  $res = exec_sql_query($db, $sql, $params)->fetchAll();
  // check if we have duplicate tags
  if($res and sizeof($res) > 0) {
    $tag_id = $res[0]["tag_id"];
  }
  else {
    $sql = "INSERT INTO tags (tag) VALUES (:tag);";
    $params = array(":tag" => $tag);
    if(!exec_sql_query($db, $sql, $params)) {
      array_push($err, "Failed to add a tag to your image.");
    }
    $tag_id = $db->lastInsertId("tag_id");
  }

  // add the mapping of tag and image to database
  $sql = "SELECT * FROM images_tags_mapping WHERE image_id = :image_id AND tag_id = :tag_id";
  $params = array(":image_id" => $image_id, ":tag_id" => $tag_id);
  if(empty(exec_sql_query($db, $sql, $params)->fetchAll())) {
    $sql = "INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (:image_id, :tag_id);";
    if(!exec_sql_query($db, $sql, $params)) {
      array_push($err, "Failed to add a tag to your image.");
    }
  }
}

// write a login function based on the log_in function from lab 8
function login($username, $password) {
    global $db;
    global $err;
    global $msg;

    $sql = "SELECT password, user_id FROM users WHERE username = :username";
    $params = array(":username" => $username);
    $user = exec_sql_query($db, $sql, $params)->fetchAll();

    if($user) {
      $pw_hash = $user[0]["password"];

      if(password_verify($password, $pw_hash) == TRUE) {
        $sql = "UPDATE users SET session = :session WHERE user_id = :user_id";
        $session = uniqid();
        $params = array(":session" => $session, ":user_id" => $user[0]["user_id"]);
        $res = exec_sql_query($db, $sql, $params);

        if($res) {
          setcookie("session", $session, time() + 24 * 3600);

          array_push($msg, "successfully logged in as $username.");
          return TRUE;
        }
        else {
          array_push($err, "Login failed!");
        }
      }
      else {
        array_push($err, "Invalid username or password!");
      }
    }
    else {
      array_push($err, "Username does not exist!");
    }

    if(sizeof($err) > 0) {
      show_err();
    }

    return FALSE;
}

// get current user, this function is based on check_login function in lab 8
function fetch_current_user() {
  if(isset($_COOKIE["session"])) {
    global $db;
    $cookie_session = $_COOKIE["session"];
    $sql = "SELECT username FROM users WHERE session = :session";
    $params = array(':session' => $cookie_session);
    $cur_user = exec_sql_query($db, $sql, $params)->fetchAll();
    if($cur_user) {
      return $cur_user[0]["username"];
    }
  }

  return NULL;
}

//log out function, this function is based on log_out function in lab 8
function logout() {
  global $current_user;

  if ($current_user) {
    global $db;
    $sql = "UPDATE users SET session = NULL WHERE username = :username;";
    $params = array(":username" => $current_user);
    $res = exec_sql_query($db, $sql, $params);
    if (is_null($res)) {
      global $err;
      array_push($err, "Log out failed.");
    }
    setcookie("session", "", time() - 24 * 3600);
    unset($current_user);
  }
}

// go to homepage from any where in the website
function go_to_home_page() {
  header('Location:../index.php');
}

$current_user = fetch_current_user();

if(isset($_GET["logout"])) {
  logout();
  go_to_home_page();
}

?>
