<?php
  require 'backend/pull-profile.php';
  session_start();

  if ($_SESSION['logged_in'] != 1) {
    header("location: login");
    exit();
  }
 ?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/internal-styles.css">
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="scripts/profile-scripts.js"></script>
  <title>warmglue: profile</title>
  <meta charset="utf-8">
  <meta name="description" content="Complete your warmglue profile to get valuable professional intros.">
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
  </nav>
  <div class="main-body">
    <h1>profile</h1>
    <form name="set-profile" action="backend/update-profile.php" method="post">
      <br><br><br>
      <?php
        $info = getInfo($_SESSION['token']);
        $names = $info[0];

        if (isset($_SESSION['first_name_attempt'])) {
          echo sprintf("<p class=\"form-text\">*first name</p><input type=\"text\" name=\"first_name\" placeholder=\"Jane\" value=\"%s\"><img class=\"empty-x\"><br>", $_SESSION['first_name_attempt']);
        } else {
          echo sprintf("<p class=\"form-text\">*first name</p><input type=\"text\" name=\"first_name\" placeholder=\"Jane\" value=\"%s\"><img class=\"empty-x\"><br>", $names[0]);
        }

        if (isset($_SESSION['last_name_attempt'])) {
          echo sprintf("<p class=\"form-text\">*last name</p><input type=\"text\" name=\"last_name\" placeholder=\"Doe\" value=\"%s\"><img class=\"empty-x\"><br><br>", $_SESSION['last_name_attempt']);
        } else {
          echo sprintf("<p class=\"form-text\">*last name</p><input type=\"text\" name=\"last_name\" placeholder=\"Doe\" value=\"%s\"><img class=\"empty-x\"><br><br>", $names[1]);
        }

        echo sprintf("<h2 class=\"form-header\">emails (work, personal, school, etc)</h2><br>");

        if (isset($_SESSION['email_attempts'])) {
          $emails = $_SESSION['email_attempts'];
        } else {
          // the user has not input any emails
          $emails = $info[1];
        }

        echo sprintf("<p class=\"form-text e00 e_count\">*email</p><input type=\"text\" class=\"e00\" name=\"email00\" placeholder=\"\" value=\"%s\"><img class=\"empty-x\"><br>", $emails[0]);
        echo sprintf("<p class=\"form-text e01 e_count\">email</p><input type=\"text\" class=\"e01\" name=\"email01\" placeholder=\"janedoe67@outlook.com\" value=\"%s\"><img class=\"x e01\" id=\"xe01\" onClick=\"delete_this(this.id)\"><br class=\"e01\">", $emails[1]);
        echo sprintf("<p class=\"form-text e02 e_count\">email</p><input type=\"text\" class=\"e02\" name=\"email02\" placeholder=\"doe@wustl.edu\" value=\"%s\"><img class=\"x e02\" id=\"xe02\" onClick=\"delete_this(this.id)\"><br class=\"e02\">", $emails[2]);
        echo sprintf("<p class=\"form-text e03 e_count\">email</p><input type=\"text\" class=\"e03\" name=\"email03\" placeholder=\"jane@amazon.com\" value=\"%s\"><img class=\"x e03\" id=\"xe03\" onClick=\"delete_this(this.id)\"><br class=\"e03\">", $emails[3]);

        // create form elements for each of the emails the user has input
        for ($i = 4; $i < count($emails); $i++) {
          echo sprintf("<p class=\"form-text e%d%d e_count\">email</p><input type=\"text\" class=\"e%d%d\" name=\"email%d%d\" value=\"%s\"><img class=\"x e%d%d\" id=\"xe%d%d\" onClick=\"delete_this(this.id)\"><br class=\"e%d%d\">", intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, $emails[$i], intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10);
        }

        // create form elements for extra emails (30 total emails are created as a buffer)
        for ($i = count($emails); $i < 30; $i++) {
          echo sprintf("<p class=\"form-text e%d%d delete\">email</p><input type=\"text\" class=\"e%d%d delete\" name=\"email%d%d\"><img class=\"x e%d%d delete\" id=\"xe%d%d\" onClick=\"delete_this(this.id)\"><br class=\"e%d%d delete\">", intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10);
        }

        echo sprintf("<br><img class=\"plus\" id=\"plus-email\" onClick=\"add_email()\"><br><br>");

        echo sprintf("<h2 class=\"form-header\">work and education</h2><br>");

        if (isset($_SESSION['occupation_attempts'])) {
          $occupations = $_SESSION['occupation_attempts'];
        } else {
          // the user has not input any emails
          $occupations = $info[2];
        }

        // occupation00
        echo sprintf("<p class=\"form-text o_count\">*position</p><input type=\"text\" name=\"position00\" placeholder=\"B.A. Major in Math, Minor in Spanish\" value=\"%s\"><img class=\"empty-x\"><br>", $occupations[0][0]);

        echo sprintf("<p class=\"form-text\">*organization</p><input type=\"text\" name=\"organization00\" placeholder=\"Washington University in St. Louis\" value=\"%s\"><img class=\"empty-x\"><br>", $occupations[0][1]);

        echo sprintf("<p class=\"form-text\">*start</p><input type=\"text\" name=\"startdate00\" placeholder=\"mm/dd/yyyy\" value=\"%s\"><img class=\"empty-x\"><br>", $occupations[0][2]);

        echo sprintf("<p class=\"form-text\">**end</p><input type=\"text\" name=\"enddate00\" placeholder=\"mm/dd/yyyy\" value=\"%s\"><img class=\"empty-x\"><br>", $occupations[0][3]);

        echo sprintf("<p class=\"form-text\">projects</p><textarea name=\"projects00\" placeholder=\"Delta Sigma Pi business fraternity, Design-Build-Fly, Partners in East St. Louis\">%s</textarea><img class=\"empty-x\"><br><br><br><br><br><br><br><br><br>", $occupations[0][4]);

        // occupation01
        echo sprintf("<p class=\"form-text o01 o_count\">position</p><input type=\"text\" class=\"o01\" name=\"position01\" placeholder=\"Software Engineering Intern\" value=\"%s\"><img class=\"x o01\" id=\"xo01\" onClick=\"delete_this(this.id)\"><br class=\"o01\">", $occupations[1][0]);

        echo sprintf("<p class=\"form-text o01\">organization</p><input type=\"text\" class=\"o01\" name=\"organization01\" placeholder=\"Amazon\" value=\"%s\"><img class=\"empty-x o01\"><br class=\"o01\">", $occupations[1][1]);

        echo sprintf("<p class=\"form-text o01\">start</p><input type=\"text\" class=\"o01\" name=\"startdate01\" placeholder=\"06/15/2015\" value=\"%s\"><img class=\"empty-x o01\"><br class=\"o01\">", $occupations[1][2]);

        echo sprintf("<p class=\"form-text o01\">**end</p><input type=\"text\" class=\"o01\" name=\"enddate01\" placeholder=\"09/15/2015\" value=\"%s\"><img class=\"empty-x o01\"><br class=\"o01\">", $occupations[1][3]);

        echo sprintf("<p class=\"form-text o01\">projects</p><textarea class=\"o01\" name=\"projects01\" placeholder=\"CAPTCHA Machine Learning\">%s</textarea><img class=\"empty-x o01\"><br class=\"o01\"><br class=\"o01\"><br class=\"o01\"><br class=\"o01\"><br class=\"o01\"><br class=\"o01\"><br class=\"o01\"><br class=\"o01\"><br class=\"o01\">", $occupations[1][4]);

        // occupation02
        echo sprintf("<p class=\"form-text o02 o_count\">position</p><input type=\"text\" class=\"o02\" name=\"position02\" placeholder=\"Data Scientist\" value=\"%s\"><img class=\"x o02\" id=\"xo02\" onClick=\"delete_this(this.id)\"><br class=\"o02\">", $occupations[2][0]);

        echo sprintf("<p class=\"form-text o02\">organization</p><input type=\"text\" class=\"o02\" name=\"organization02\" placeholder=\"Amazon\" value=\"%s\"><img class=\"empty-x o02\"><br class=\"o02\">", $occupations[2][1]);

        echo sprintf("<p class=\"form-text o02\">start</p><input type=\"text\" class=\"o02\" name=\"startdate02\" placeholder=\"07/20/2016\" value=\"%s\"><img class=\"empty-x o02\"><br class=\"o02\">", $occupations[2][2]);

        echo sprintf("<p class=\"form-text o02\">**end</p><input type=\"text\" class=\"o02\" name=\"enddate02\" placeholder=\"\" value=\"%s\"><img class=\"empty-x o02\"><br class=\"o02\">", $occupations[2][3]);

        echo sprintf("<p class=\"form-text o02\">projects</p><textarea class=\"o02\" name=\"projects02\" placeholder=\"AWS Lambda, AWS Cognito\">%s</textarea><img class=\"empty-x o02\"><br class=\"o02\"><br class=\"o02\"><br class=\"o02\"><br class=\"o02\"><br class=\"o02\"><br class=\"o02\"><br class=\"o02\"><br class=\"o02\"><br class=\"o02\">", $occupations[2][4]);

        // create form elements for each of the occupations the user has input
        for ($i = 3; $i < count($occupations); $i++) {
          echo sprintf("<p class=\"form-text o%d%d o_count\">position</p><input type=\"text\" class=\"o%d%d\" name=\"position%d%d\" value=\"%s\"><img class=\"x o%d%d\" id=\"xo%d%d\" onClick=\"delete_this(this.id)\"><br class=\"o%d%d\">", intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, $occupations[$i][0], intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10);

          echo sprintf("<p class=\"form-text o%d%d\">organization</p><input type=\"text\" class=\"o%d%d\" name=\"organization%d%d\" value=\"%s\"><img class=\"empty-x o%d%d\"><br class=\"o%d%d\">", intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, $occupations[$i][1], intdiv($i, 10), $i%10, intdiv($i, 10), $i%10);

          echo sprintf("<p class=\"form-text o%d%d\">start</p><input type=\"text\" class=\"o%d%d\" name=\"startdate%d%d\" value=\"%s\"><img class=\"empty-x o%d%d\"><br class=\"o%d%d\">", intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, $occupations[$i][2], intdiv($i, 10), $i%10, intdiv($i, 10), $i%10);

          echo sprintf("<p class=\"form-text o%d%d\">**end</p><input type=\"text\" class=\"o%d%d\" name=\"enddate%d%d\" value=\"%s\"><img class=\"empty-x o%d%d\"><br class=\"o%d%d\">", intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, $occupations[$i][3], intdiv($i, 10), $i%10, intdiv($i, 10), $i%10);

          echo sprintf("<p class=\"form-text o%d%d\">projects</p><textarea class=\"o%d%d\" name=\"projects%d%d\">%s</textarea><img class=\"empty-x o%d%d\"><br class=\"o%d%d\"><br class=\"o%d%d\"><br class=\"o%d%d\"><br class=\"o%d%d\"><br class=\"o%d%d\"><br class=\"o%d%d\"><br class=\"o%d%d\"><br class=\"o%d%d\"><br class=\"o%d%d\">", intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, $occupations[$i][4], intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10);
        }

        // create form elements for the extra occupations (100 total occupations are created as a buffer)
        for ($i = count($occupations); $i < 100; $i++) {
          echo sprintf("<p class=\"form-text o%d%d delete\">position</p><input type=\"text\" class=\"o%d%d delete\" name=\"position%d%d\"><img class=\"x o%d%d delete\" id=\"xo%d%d\" onClick=\"delete_this(this.id)\"><br class=\"o%d%d delete\">", intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10);

          echo sprintf("<p class=\"form-text o%d%d delete\">organization</p><input type=\"text\" class=\"o%d%d delete\" name=\"organization%d%d\"><img class=\"empty-x o%d%d delete\"><br class=\"o%d%d delete\">", intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10);

          echo sprintf("<p class=\"form-text o%d%d delete\">start</p><input type=\"text\" class=\"o%d%d delete\" name=\"startdate%d%d\"><img class=\"empty-x o%d%d delete\"><br class=\"o%d%d delete\">", intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10);

          echo sprintf("<p class=\"form-text o%d%d delete\">**end</p><input type=\"text\" class=\"o%d%d delete\" name=\"enddate%d%d\"><img class=\"empty-x o%d%d delete\"><br class=\"o%d%d delete\">", intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10);

          echo sprintf("<p class=\"form-text o%d%d delete\">projects</p><textarea class=\"o%d%d delete\" name=\"projects%d%d\"></textarea><img class=\"empty-x o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\">", intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10, intdiv($i, 10), $i%10);
        }

        echo sprintf("<img class=\"plus\" id=\"plus-occupation\" onClick=\"add_occupation()\"><br><br><br>");

        echo sprintf("<div class=\"error-div\">");

        if (isset($_SESSION['message']) && $_SESSION['backend_redirect']) {
          echo sprintf("<p class=\"error-text\">* This entry is required.<br>** Leave \"end\" empty if you still work or study there.<br><br>%s<br></p>", $_SESSION['message']);
        } else {
          echo sprintf("<p class=\"error-text\">* This entry is required.<br>** Leave \"end\" empty if you still work or study there.<br></p>");
        }

        echo sprintf("</div>");

        $_SESSION['backend_redirect'] = 0;

        echo sprintf("<input type=\"hidden\" name=\"token\" value=\"%s\">", $_SESSION['token']);
      ?>
      <input type="submit" value="save profile"><img class="empty-x">
    </form>
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
