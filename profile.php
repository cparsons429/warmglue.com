<?php
  require 'backend/db-users.php';
  require 'backend/update-profile.php';
  session_start();

  if ($_SESSION['logged_in'] != 1) {
    header("location: login");
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
  <meta name="description" content="Complete your warmglue profile to get valuable professional intros from your friends.">
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
    <form name="set-profile" action="update-profile.php" method="post">
      <br><br><br><p class="form-text">first name</p>
      <input type="text" name="first_name" placeholder="Jane"><img class="empty-x"><br>
      <p class="form-text">last name</p>
      <input type="text" name="last_name" placeholder="Doe"><img class="empty-x"><br><br>
      <h2 class="form-header">emails (work, personal, school, etc)</h2><br>
      <p class="form-text e00">email</p><input type="text" class="e00" name="email00" placeholder=""><img class="empty-x"><br>
      <p class="form-text e01">email</p><input type="text" class="e01" name="email01" placeholder="janedoe67@outlook.com"><img class="x e01" id="xe01" onClick="delete_this(this.id)"><br class="e01">
      <p class="form-text e02">email</p><input type="text" class="e02" name="email02" placeholder="doe@wustl.edu"><img class="x e02" id="xe02" onClick="delete_this(this.id)"><br class="e02">
      <p class="form-text e03">email</p><input type="text" class="e03" name="email03" placeholder="jane@change.org"><img class="x e03" id="xe03" onClick="delete_this(this.id)"><br class="e03">
      <!--create emails 4 through 29-->
      <?php
        for($i=4; $i<30; $i++) {
          echo sprintf("<p class=\"form-text e%d%d delete\">email</p><input type=\"text\" class=\"e%d%d delete\"name=\"email%d%d\" placeholder=\"\"><img class=\"x e%d%d delete\" id=\"xe%d%d\" onClick=\"delete_this(this.id)\"><br class=\"e%d%d delete\">", $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10);
        }
      ?>
      <br><img class="plus" id="plus-email" onClick="add_email()"><br><br>
      <h2 class="form-header">work and education</h2><br>
      <!-- position00 -->
      <p class="form-text">position</p><input type="text" name="position00" placeholder="Math Major"><img class="empty-x"><br>
      <p class="form-text">organization</p><input type="text" name="organization00" placeholder="Washington University in St. Louis"><img class="empty-x"><br>
      <p class="form-text">start</p><input type="text" name="startdate00" placeholder="mm/dd/yyyy"><img class="empty-x"><br>
      <p class="form-text">end</p><input type="text" name="enddate00" placeholder="mm/dd/yyyy"><img class="empty-x"><br>
      <p class="form-text">projects</p><textarea name="projects00" placeholder="Delta Sigma Pi business fraternity, Design-Build-Fly, Partners in East St. Louis"></textarea><img class="empty-x"><br><br><br><br><br><br><br><br><br>
      <!-- position01 -->
      <p class="form-text o01">position</p><input type="text" class="o01" name="position01" placeholder="Software Engineering Intern"><img class="x o01" id="xo01" onClick="delete_this(this.id)"><br class="o01">
      <p class="form-text o01">organization</p><input type="text" class="o01" name="organization01" placeholder="Amazon"><img class="empty-x o01"><br class="o01">
      <p class="form-text o01">start</p><input type="text" class="o01" name="startdate01" placeholder="mm/dd/yyyy"><img class="empty-x o01"><br class="o01">
      <p class="form-text o01">end</p><input type="text" class="o01" name="enddate01" placeholder="mm/dd/yyyy"><img class="empty-x o01"><br class="o01">
      <p class="form-text o01">projects</p><textarea class="o01" name="projects01" placeholder="CAPTCHA Machine Learning"></textarea><img class="empty-x o01"><br class="o01"><br class="o01"><br class="o01"><br class="o01"><br class="o01"><br class="o01"><br class="o01"><br class="o01"><br class="o01">
      <!-- position02 -->
      <p class="form-text o02">position</p><input type="text" class="o02" name="position02" placeholder="Data Scientist"><img class="x o02" id="xo02" onClick="delete_this(this.id)"><br class="o02">
      <p class="form-text o02">organization</p><input type="text" class="o02" name="organization02" placeholder="Amazon"><img class="empty-x o02"><br class="o02">
      <p class="form-text o02">start</p><input type="text" class="o02" name="startdate02" placeholder="mm/dd/yyyy"><img class="empty-x o02"><br class="o02">
      <p class="form-text o02">end</p><input type="text" class="o02" name="enddate02" placeholder="mm/dd/yyyy"><img class="empty-x o02"><br class="o02">
      <p class="form-text o02">projects</p><textarea class="o02" name="projects02" placeholder="AWS Lambda, AWS Cognito"></textarea><img class="empty-x o02"><br class="o02"><br class="o02"><br class="o02"><br class="o02"><br class="o02"><br class="o02"><br class="o02"><br class="o02"><br class="o02">
      <!--create positions 3 through 69-->
      <?php
        for($i=3; $i<70; $i++) {
          echo sprintf("<p class=\"form-text o%d%d delete\">position</p><input type=\"text\" class=\"o%d%d delete\" name=\"position%d%d\" placeholder=\"\"><img class=\"x o%d%d delete\" id=\"xo%d%d\" onClick=\"delete_this(this.id)\"><br class=\"o%d%d delete\">", $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10);
          echo sprintf("<p class=\"form-text o%d%d delete\">organization</p><input type=\"text\" class=\"o%d%d delete\" name=\"organization%d%d\" placeholder=\"\"><img class=\"empty-x o%d%d delete\"><br class=\"o%d%d delete\">", $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10);
          echo sprintf("<p class=\"form-text o%d%d delete\">start</p><input type=\"text\" class=\"o%d%d delete\" name=\"startdate%d%d\" placeholder=\"\"><img class=\"empty-x o%d%d delete\"><br class=\"o%d%d delete\">", $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10);
          echo sprintf("<p class=\"form-text o%d%d delete\">end</p><input type=\"text\" class=\"o%d%d delete\" name=\"enddate%d%d\" placeholder=\"\"><img class=\"empty-x o%d%d delete\"><br class=\"o%d%d delete\">", $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10);
          echo sprintf("<p class=\"form-text o%d%d delete\">projects</p><textarea class=\"o%d%d delete\" name=\"projects%d%d\" placeholder=\"\"></textarea><img class=\"empty-x o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\"><br class=\"o%d%d delete\">", $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10, $i/10, $i%10);
        }
      ?>
      <img class="plus" id="plus-occupation" onClick="add_occupation()"><br><br><br>
      <input type="submit" value="save profile"><img class="empty-x">
    </form>
  </div>
  <div class="footer">
    <p>&copy; warmglue 2018</p>
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
