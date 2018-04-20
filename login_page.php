<?php
include('includes/init.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />

  <title>Log In</title>
</head>

<body id="login_page_body">
  <?php
  include('includes/header.php');
  ?>
  <div id="form_field">
    <h1>LOGIN</h1>
    <form id="form_field_form" method="post">
      <div class="input_field">
        <label>Username:</label>
        <input type="text" name="username" required>
      </div>
      <br><br>
      <div class="input_field">
        <label>Password:</label>
        <input type="password" name="password" required>
      </div>
      <br><br>
      <div class="input_field">
        <input type="submit" id="form_field_btn" value="Log In">
      </div>
      <?php
      // check username and password, then go back to homepage if log-in succeeds
      if(isset($_POST["username"]) && isset($_POST["password"])) {
        $username = trim(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING));
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);

        if(login($username, $password)) {
          show_msg();
          sleep(1);
          header("Location:/index.php");
        }
      }
      ?>
    </form>
  </div>
</body>
</html>
