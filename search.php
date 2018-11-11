<?php
  require 'backend/pull-searches.php';
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
  <script src="scripts/search-scripts.js"></script>
  <title>warmglue: search</title>
  <meta charset="utf-8">
  <meta name="description" content="Search for the intros your friends can make.">
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
    <h1>searches</h1>
    <form name="search" action="backend/update-searches.php" method="post">
      <br><br><br>
      <?php
        $searches = getSearches($_SESSION['token']);

        echo sprintf("<p class=\"form-text s00\">search</p><textarea class=\"s00\" name=\"search00\" placeholder=\"Professor who's connected with the graduate math program at Stanford. I'm thinking about applying there for a master's.\">%s</textarea><img class=\"empty-x s00\"><br class=\"s00\"><br class=\"s00\"><br class=\"s00\"><br class=\"s00\"><br class=\"s00\"><br class=\"s00\"><br class=\"s00\"><br class=\"s00\"><br class=\"s00\">", $searches[0]);
        echo sprintf("<p class=\"form-text s01\">search</p><textarea class=\"s01\" name=\"search01\" placeholder=\"Developer at a meal delivery company - we're trying to get more users for our API!\">%s</textarea><img class=\"empty-x s01\"><br class=\"s01\"><br class=\"s01\"><br class=\"s01\"><br class=\"s01\"><br class=\"s01\"><br class=\"s01\"><br class=\"s01\"><br class=\"s01\"><br class=\"s01\">", $searches[1]);
        echo sprintf("<p class=\"form-text s02\">search</p><textarea class=\"s02\" name=\"search02\" placeholder=\"\">%s</textarea><img class=\"empty-x s02\"><br class=\"s02\"><br class=\"s02\"><br class=\"s02\"><br class=\"s02\"><br class=\"s02\"><br class=\"s02\"><br class=\"s02\"><br class=\"s02\"><br class=\"s02\">", $searches[2]);
        echo sprintf("<p class=\"form-text s03\">search</p><textarea class=\"s03\" name=\"search03\" placeholder=\"\">%s</textarea><img class=\"empty-x s03\"><br class=\"s03\"><br class=\"s03\"><br class=\"s03\"><br class=\"s03\"><br class=\"s03\"><br class=\"s03\"><br class=\"s03\"><br class=\"s03\"><br class=\"s03\">", $searches[3]);
        echo sprintf("<p class=\"form-text s04\">search</p><textarea class=\"s04\" name=\"search04\" placeholder=\"\">%s</textarea><img class=\"empty-x s04\"><br class=\"s04\"><br class=\"s04\"><br class=\"s04\"><br class=\"s04\"><br class=\"s04\"><br class=\"s04\"><br class=\"s04\"><br class=\"s04\"><br class=\"s04\">", $searches[4]);

        echo sprintf("<input type=\"hidden\" name=\"token\" value=\"%s\">", $_SESSION['token']);
      ?>
      <input type="submit" value="save searches"><img class="empty-x">
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
