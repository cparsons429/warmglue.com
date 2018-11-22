<?php
  session_start();

  if ($_SESSION['logged_in'] != 1) {
    header("location: login");
    exit();
  }

  $_SESSION['pull_intros_access_allowed'] = 1;
  require 'backend/pull_intros';
 ?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/home-styles.css">
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="scripts/home-scripts.js"></script>
  <title>warmglue: home</title>
  <meta charset="utf-8">
  <meta name="description" content="Log in or sign up to review intros, update your profile, update your searches, and view account settings.">
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
      <a href="home">
        <button class="button brand-image-button">
          <img class="logo" alt ="">
        </button>
      </a>
    </div>
    <div class="nav-log-out-div">
      <form name="log-out" action="backend/logout-user" method="post">
        <?php
          echo sprintf("<input type=\"hidden\" name=\"token\" value=\"%s\">", $_SESSION['token']);
         ?>
        <input type="submit" value="log out" class="button nav-log-out">
      </form>
    </div>
  </nav>
  <div class="main-body">
    <div class="link-buttons">
      <div class="profile-div">
        <a href="profile" class="profile-link">
          <button class="button body-profile">profile</button>
        </a>
      </div>
      <div class="search-div">
        <a href="search" class="search-link">
          <button class="button body-search">search</button>
        </a>
      </div>
      <div class="account-div">
        <a href="account" class="account-link">
          <button class="button body-account">account</button>
        </a>
      </div>
    </div>
    <div class="intro-pages">
      <div class="tab-left">
        <img class="page-left greyed-out">
      </div>
      <div class="tab-right">
        <img class="page-right greyed-out">
      </div>
    </div>
    <div class="intro-list">
      <?php
        $intros = getIntros($_SESSION['token']);

        if (count($intros) == 0) {
          // user must have been offered no intros yet
          if ($_SESSION['completed_profile']) {
            // user has completed their profile
            echo sprintf("<img class=\"no-intros-yet\">");
          } else {
            // user has not completed their profile
            echo sprintf("<img class=\"incomplete-profile\">");
          }
        } else {
          // create entries for each of the intros offered; JS will handle showing the correct number
          for ($i = 0; $i < count($intros); $i++) {
            // format of returned intros: id, first name, last name, suggested date, rating, reason
            // creating an entry on the left side of the screen
            echo sprintf("<p class=\"intro-text i%d\">%s\t%s %s</p><img class=\"rate i%d\" id=\"ri%d\" onClick=\"rate_this(this.id)\"><br class=\"i%d\">", $i, $intros[$i][3], $intros[$i][1], $intros[$i][2], $i, $i, $i);

            // a div containing a form to review the intro will appear on the right side after the user clicks the rate icon
            echo sprintf("<div class=\"rating i%d delete\">", $i);
            echo sprintf("<h2>intro with %s %s</h2>", $intros[$i][1], $intros[$i][2]);
            echo sprintf("<form name=\"update-review%d\" action=\"backend/update-review\" method=\"post\">", $intros[$i][0]);
            echo sprintf("<br><p class=\"form-text\">*rating (1-5)</p><input type=\"text\" name=\"rating\" placeholder=\"5 - great, 1 - poor\" value=\"%d\"><br>", $intros[$i][4]);
            echo sprintf("<p class=\"form-text\">reason</p><textarea name=\"reason\" placeholder=\"Pretty valuable! They connected me with a couple of their friends in the space.\">%s</textarea><br><br><br><br><br><br><br><br><br>", $intros[$i][5]);

            // put a message at the bottom of the form, with space for any error message
            echo sprintf("<div class=\"pre-warning\"></div>");

            if (isset($_SESSION['message']) && $_SESSION['backend_redirect']) {
              echo sprintf("<p class=\"error-text\">* This entry is required.<br>Your reviews are used to improve the quality of warmglue's future recommendations for you. This <b>won't</b> be seen by %s (or by anyone not working with warmglue).<br><br>%s</p>", $intros[$i][1], $_SESSION['message']);
            } else {
              echo sprintf("<p class=\"error-text\">* This entry is required.<br>Your reviews are used to improve the quality of warmglue's future recommendations for you. This <b>won't</b> be seen by %s (or by anyone not working with warmglue).</p>", $intros[$i][1]);
            }

            echo sprintf("<div class=\"post-warning\"></div>");

            // including token and intro id to secure and facilitate backend processing
            echo sprintf("<input type=\"hidden\" name=\"token\" value=\"%s\">", $_SESSION['token']);
            echo sprintf("<input type=\"hidden\" name=\"intro_id\" value=\"%s\">", $intros[$i][0]);
            echo sprintf("<input type=\"submit\" value=\"rate intro\">");

            echo sprintf("</form>");
            echo sprintf("</div>");
          }
        }
       ?>
    </div>
  </div>
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
