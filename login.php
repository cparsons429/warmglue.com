<?php
  session_start();

  if ($_SESSION['logged_in'] == 1) {
    header("location: home");
  }
 ?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/external-styles.css">
  <link rel="stylesheet" href="styles/login-styles.css">
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <title>warmglue: log in</title>
  <meta charset="utf-8">
  <meta name="description" content="Log in to review intros, update your profile, update your searches, and view account settings.">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- favicon stuff -->
  <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
  <link rel="manifest" href="assets/favicon/site.webmanifest">
  <link rel="mask-icon" href="assets/favicon/safari-pinned-tab.svg" color="#b30000">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#ffffff">
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
      <h1>log in</h1>
      <form name="log-in" action="backend/login-user.php" method="post">
        <br>
        <?php
          if (isset($_SESSION['email_attempt'])) {
            echo sprintf("<p class=\"form-text\">email</p><input type=\"text\" name=\"email\" placeholder=\"jane.doe@gmail.com\" value=\"%s\"><br><p class=\"form-text\">password</p><input type=\"password\" name=\"password\" placeholder=\"&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;\">", $_SESSION['email_attempt']);
          } else {
            echo sprintf("<p class=\"form-text\">email</p><input type=\"text\" name=\"email\" placeholder=\"jane.doe@gmail.com\"><br><p class=\"form-text\">password</p><input type=\"password\" name=\"password\" placeholder=\"&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;\">");
          }
        ?>
        <div class="pre-link"></div>
        <a class="form-link" href="resetpassword">forgot your password?</a>
        <br><a class="form-link" href="signup">create an account</a>
        <div class="post-link"></div>
        <?php
          if (isset($_SESSION['message'])) {
            echo sprintf("<p class=\"error-text\">%s</p>", $_SESSION['message']);
          }
        ?>
        <input type="submit" value="log in">
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
