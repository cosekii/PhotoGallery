<header>
  <nav>
    <a id="home" href="../index.php">Photo Gallery</a>
    <?php
    if($current_user and !isset($_GET["logout"])) {
      echo "<a id='logout' href='../index.php?logout=1'>Log Out</a>";
      echo "<a id='upload_image' href='../upload_image.php'>Upload Photos</a>";
      //echo "<a id='logout' href='../index.php?logout=1'>$current_user</a>";
    }
    else {
      echo "<a id='login' href='../login_page.php'>Log In</a>";
    }
    ?>
  </nav>
</header>
