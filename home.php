<?php
  session_start();

  if ($_SESSION['logged_in'] != 1) {
    header("location: login");
    exit();
  }

  $_SESSION['pull_intros_access_allowed'] = 1;
  require 'backend/pull-intros.php';
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
<body class="delete">
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
      <div class="tab-left expand-small">
        <img class="page-left-grey">
        <img class="page-left-red delete" onClick="tab_left()">
      </div>
      <div class="profile-div">
        <a href="profile" class="profile-link">
          <button class="adjustable-button body-profile">profile</button>
        </a>
      </div>
      <div class="search-div">
        <a href="search" class="search-link">
          <button class="adjustable-button body-search">search</button>
        </a>
      </div>
      <div class="account-div">
        <a href="account" class="account-link">
          <button class="adjustable-button body-account">account</button>
        </a>
      </div>
      <div class="tab-right expand-small">
        <img class="page-right-grey">
        <img class="page-right-red delete" onClick="tab_right()">
      </div>
    </div>
    <div class="intro-list">
      <?php
        $intros = getIntros($_SESSION['token']);

        if (count($intros) == 0) {
          // user must have been offered no intros yet

          echo sprintf("<div class=\"empty-intros-container\">");
          echo sprintf("<div class=\"empty-intros-left\">");
          echo sprintf("<img class=\"no-intros\">");
          echo sprintf("</div>");
          echo sprintf("<div class=\"empty-intros-right\">");

          if ($_SESSION['completed_profile'] && $_SESSION['completed_searches']) {
            // user has completed their profile
            echo sprintf("<p class=\"no-intros-text\">Intro suggestions will start arriving by tomorrow - check your inbox.</p>");
          } else {
            // user has not completed their profile
            echo sprintf("<p class=\"no-intros-text\">To start receiving intros, complete your profile and save some searches.</p>");
          }

          echo sprintf("</div>");
          echo sprintf("</div>");
        } else {
          // create entries for each of the intros offered; JS will handle showing the correct number
          for ($i = 0; $i < count($intros); $i++) {
            // format of returned intros: id, first name, last name, suggested date, rating, reason
            // we will show review info automatically if we're redirected from the backend; otherwise, it'll be hidden
            if ($i === $_SESSION['intro_number']) {
              // creating an entry on the left side of the screen
              echo sprintf("<p class=\"intro-text i%d\" id=\"intro%d\">%s&emsp;%s %s</p><img class=\"write delete i%d\" id=\"w%d\" onClick=\"show_review(this.id)\"><img class=\"x-right i%d\" id=\"x%d\" onClick=\"hide_review(this.id)\">", $i, $i, $intros[$i][3], $intros[$i][1], $intros[$i][2], $i, $i, $i, $i);

              echo sprintf("<div class=\"review\" id=\"r%d\">", $i);
            } else {
              // creating an entry on the left side of the screen
              echo sprintf("<p class=\"intro-text i%d\" id=\"intro%d\">%s&emsp;%s %s</p><img class=\"write i%d\" id=\"w%d\" onClick=\"show_review(this.id)\"><img class=\"x-right delete i%d\" id=\"x%d\" onClick=\"hide_review(this.id)\">", $i, $i, $intros[$i][3], $intros[$i][1], $intros[$i][2], $i, $i, $i, $i);

              // a div containing a form to review the intro will appear on the right side after the user clicks the rate icon
              echo sprintf("<div class=\"review delete\" id=\"r%d\">", $i);
            }

            // if the user attempted a review, these are the values we want to show
            if (isset($_SESSION['rating_attempt']) && $_SESSION['intro_number'] == $i) {
              $rate_to_show = $_SESSION['rating_attempt'];
            } else {
              $rate_to_show = $intros[$i][4];
            }

            if (isset($_SESSION['reason_attempt']) && $_SESSION['intro_number'] == $i) {
              $reas_to_show = $_SESSION['reason_attempt'];
            } else {
              $reas_to_show = $intros[$i][5];
            }

            echo sprintf("<h2 class=\"review-header\">*intro with %s %s</h2>", $intros[$i][1], $intros[$i][2]);
            echo sprintf("<form name=\"update-review%d\" action=\"backend/update-review\" method=\"post\">", $intros[$i][0]);
            echo sprintf("<br><p class=\"form-text\">**rating</p><input type=\"text\" name=\"rating\" placeholder=\"1 = poor, 5 = great\" value=\"%s\"><br>", $rate_to_show);
            echo sprintf("<p class=\"form-text\">reason</p><textarea name=\"reason\" placeholder=\"Pretty valuable! They connected me with a couple of their friends in the space.\">%s</textarea><br><br><br><br><br><br><br><br>", $reas_to_show);

            // put a message at the bottom of the form, with space for any error message
            echo sprintf("<div class=\"pre-warning\"></div>");

            if (isset($_SESSION['message']) && $_SESSION['backend_redirect'] && $_SESSION['intro_number'] == $i) {
              echo sprintf("<p class=\"error-text review-error\">* This review <b>can't</b> be seen by %s.<br>** This entry is required.<br><br>%s<br><br></p>", $intros[$i][1], $_SESSION['message']);
            } else {
              echo sprintf("<p class=\"error-text review-error\">* Your review <b>won't</b> be seen by %s.<br>** This entry is required.<br><br></p>", $intros[$i][1]);
            }

            echo sprintf("<div class=\"post-warning\"></div>");

            // including token and intro id to secure and facilitate backend processing
            echo sprintf("<input type=\"hidden\" name=\"token\" value=\"%s\">", $_SESSION['token']);
            echo sprintf("<input type=\"hidden\" name=\"intro_id\" value=\"%s\">", $intros[$i][0]);
            echo sprintf("<input type=\"hidden\" name=\"intro_num\" value=\"%s\">", strval($i));
            echo sprintf("<input type=\"hidden\" name=\"scroll_val\" value=\"\" id=\"scroll_top%d\">", $i);
            echo sprintf("<input type=\"hidden\" name=\"first_val\" value=\"\" id=\"first%d\">", $i);
            echo sprintf("<input type=\"submit\" value=\"rate intro\" class=\"rate-intro-submit\" onClick=\"scroll_and_first_save(%d)\">", $i);

            echo sprintf("</form>");
            echo sprintf("</div>");
          }

          $_SESSION['intro_number'] = null;
          $_SESSION['backend_redirect'] = 0;
        }

        // scroll to the original position before backend redirect
        echo sprintf("<script>");
        echo sprintf("unhide();");
        echo sprintf("var f = %d;", $_SESSION['first_value']);
        echo sprintf("var m = get_max_visible_intros();");
        echo sprintf("var t = get_total_intros();");
        echo sprintf("show_intros(f, m, t);");
        echo sprintf("var body = document.body;");
        echo sprintf("var html = document.documentElement;");
        echo sprintf("body.scrollTop = %d;", $_SESSION['scroll_value']);
        echo sprintf("html.scrollTop = %d;", $_SESSION['scroll_value']);
        echo sprintf("</script>");

        $_SESSION['first_value'] = null;
        $_SESSION['scroll_value'] = null;
       ?>
    </div>
  </div>
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
