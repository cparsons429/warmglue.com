<?php
  session_start();

  if ($_SESSION['logged_in'] != 1) {
    // we need to see if the url has the correct token
    $_SESSION['change_password_verify_allowed'] = 1;
    require 'backend/change-password-verify.php';
    $u_id = change_pw_account(1, $_GET['token']);

    if ($u_id == 0) {
      header("location: resetpassword");
      exit();
    }
  } else {
    $u_id = $_SESSION['user_id'];
  }
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
      <form name="change-password" action="backend/update-password" method="post">
        <br><p class="form-text">create password</p><input type="password" name="password0" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;"><br><p class="form-text">re-type password</p><input type="password" name="password1" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
        <div class="pre-link"></div>
        <a class="form-link" href="landing">return to home</a>
        <div class="post-link"></div>
        <?php
          if (isset($_SESSION['message']) && $_SESSION['backend_redirect']) {
            echo sprintf("<p class=\"error-text\">%s</p>", $_SESSION['message']);
          }

          $_SESSION['backend_redirect'] = 0;

          echo sprintf("<input type=\"hidden\" name=\"reset_pw_id\" value=\"%d\">", $u_id);
          echo sprintf("<input type=\"hidden\" name=\"token\" value=\"%s\">", $_GET['token']);
        ?>
        <input type="submit" value="change password">
      </form>
    </div>
  </div>
  <div class="empty-footer-pad"></div>
  <div class="footer">
    <?php
      echo sprintf("<p>&copy; warmglue %s</p>", date("Y"));
    ?>
    <a href="https://twitter.com/realwarmglue" class="icon-link" target="_blank">
      <img class="twitter" alt="">
    </a>
    <a href="https://www.facebook.com/realwarmglue" class="icon-link" target="_blank">
      <img class="facebook" alt="">
    </a>
    <a href="https://medium.com/@warmglue" class="icon-link" target="_blank">
      <img class="medium" alt="">
    </a>
    <p class="footer-link-p">
      <a href="privacy" class="footer-link">privacy</a>&ensp;<a href="terms" class="footer-link">terms</a>
    </p>
  </div>
</body>
</html>
