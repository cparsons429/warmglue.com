<?php
  session_start();
 ?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/external-styles.css">
  <link rel="stylesheet" href="styles/changepassword-styles.css">
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <title>warmglue: change password</title>
  <meta charset="utf-8">
  <meta name="description" content="Change your password.">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- favicon stuff -->
  <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
  <link rel="manifest" href="assets/favicon/site.webmanifest">
  <link rel="mask-icon" href="assets/favicon/safari-pinned-tab.svg" color="#b30000">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#ffffff">
  <meta name="robots" content="noindex">
</head>
<body>
  <nav class="navbar">
    <div class="brand-image">
      <a href="landing">
        <button class="button brand-image-button">
          <img class="logo" alt ="">
        </button>
      </a>
    </div>
  </nav>
  <div class="main-body">
    <div class="basic-info">
      <h1>change password</h1>
      <form name="change-password" action="backend/update-password.php" method="post">
        <br><p class="form-text">create password</p><input type="password" name="password0" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;"><br><p class="form-text">re-type password</p><input type="password" name="password1" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
        <input type="submit" value="change password">
        <?php
          if (isset($_SESSION['message'])) {
            echo sprintf("<div class=\"pre-link\"></div>");
            echo sprintf("<br><p class=\"form-text\">%s</p><img class=\"empty-x\"><br>", $_SESSION['message']);
            echo sprintf("<div class=\"post-link\"></div>");
          }
        ?>
      </form>
    </div>
  </div>
  <div class="empty-footer-pad"></div>
  <div class="footer">
    <?php
      echo sprintf("<p>&copy; warmglue %s</p>", date("Y"));
    ?>
    <a href="https://twitter.com/realwarmglue" class="medium-link" target="_blank">
      <img class="twitter" alt="">
    </a>
    <a href="https://www.facebook.com/realwarmglue" class="facebook-link" target="_blank">
      <img class="facebook" alt="">
    </a>
    <a href="https://medium.com/@warmglue" class="medium-link" target="_blank">
      <img class="medium" alt="">
    </a>
  </div>
</body>
</html>
