<?php
  session_start();

  if ($_SESSION['logged_in'] != 1) {
    header("location: landing");
    exit();
  }
 ?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/external-styles.css">
  <link rel="stylesheet" href="styles/cancelaccount-styles.css">
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <title>warmglue: cancel account</title>
  <meta charset="utf-8">
  <meta name="description" content="Confirm your email to cancel your account.">
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
      <a href="home">
        <button class="button brand-image-button">
          <img class="logo" alt ="">
        </button>
      </a>
    </div>
  </nav>
  <div class="main-body">
    <div class="basic-info">
      <h1>cancel account</h1>
      <form name="confirm-delete" action="backend/delete-confirm" method="post">
        <br><p class="form-text">confirm email</p><input type="text" name="email" placeholder="jane.doe@gmail.com">
        <?php
          echo sprintf("<div class=\"pre-warning\"></div>");

          if (isset($_SESSION['message']) && $_SESSION['backend_redirect']) {
            echo sprintf("<p class=\"error-text\">This action <b>cannot</b> be undone.<br><br>%s</p>", $_SESSION['message']);
          } else {
            echo sprintf("<p class=\"error-text\">This action <b>cannot</b> be undone.</p>");
          }

          echo sprintf("<div class=\"post-warning\"></div>");

          $_SESSION['backend_redirect'] = 0;

          echo sprintf("<input type=\"hidden\" name=\"token\" value=\"%s\">", $_SESSION['token']);
        ?>
        <input type="submit" value="cancel account">
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
