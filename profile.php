<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/profile-styles.css">
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
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
    <h1>complete profile</h1>
    <form name="profile" action="complete-profile.php" method="get">
      <br><br><br><p class="form-text">first name</p>
      <input type="text" name="first-name" placeholder="Jane"><br>
      <p class="form-text">last name</p>
      <input type="text" name="last-name" placeholder="Doe"><br><br>
      <h2 class="form-header">emails (work, personal, school, etc)</h2><br>
      <p class="form-text">email</p><input type="text" name="email0" placeholder=""><br>
      <p class="form-text">email</p><input type="text" name="email1" placeholder="janedoe67@outlook.com"><br>
      <p class="form-text">email</p><input type="text" name="email2" placeholder="doe@wustl.edu"><br>
      <p class="form-text">email</p><input type="text" name="email3" placeholder="jane@change.org"><br>
      <!--create emails 4 through 9-->
      <?php
        for($i=4; $i<10; $i++) {
          echo sprintf("<p class=\"form-text\">email</p><input type=\"text\" name=\"email%d\" placeholder=\"\"><br>", $i);
        }
      ?>
      <br>
      <h2 class="form-header">work and education</h2><br>
      <!-- position00 -->
      <p class="form-text">position</p><input type="text" name="position00" placeholder="Math Major"><br>
      <p class="form-text">organization</p><input type="text" name="organization00" placeholder="Washington University in St. Louis"><br>
      <p class="form-text">start</p><input type="text" name="startdate00" placeholder="mm/dd/yyyy"><br>
      <p class="form-text">end</p><input type="text" name="enddate00" placeholder="mm/dd/yyyy"><br>
      <p class="form-text">projects</p><textarea name="projects00" placeholder="Delta Sigma Pi business fraternity, Design-Build-Fly, Partners in East St. Louis"></textarea><br><br><br><br><br><br><br><br><br>
      <!-- position01 -->
      <p class="form-text">position</p><input type="text" name="position01" placeholder="Software Engineering Intern"><br>
      <p class="form-text">organization</p><input type="text" name="organization01" placeholder="Amazon"><br>
      <p class="form-text">start</p><input type="text" name="startdate01" placeholder="mm/dd/yyyy"><br>
      <p class="form-text">end</p><input type="text" name="enddate01" placeholder="mm/dd/yyyy"><br>
      <p class="form-text">projects</p><textarea name="projects01" placeholder="CAPTCHA Machine Learning"></textarea><br><br><br><br><br><br><br><br><br>
      <!-- position02 -->
      <p class="form-text">position</p><input type="text" name="position02" placeholder="Data Scientist"><br>
      <p class="form-text">organization</p><input type="text" name="organization02" placeholder="Amazon"><br>
      <p class="form-text">start</p><input type="text" name="startdate02" placeholder="mm/dd/yyyy"><br>
      <p class="form-text">end</p><input type="text" name="enddate02" placeholder="mm/dd/yyyy"><br>
      <p class="form-text">projects</p><textarea name="projects02" placeholder="AWS Lambda, AWS Cognito"></textarea><br><br><br><br><br><br><br><br><br>
      <!--create positions 3 through 49-->
      <?php
        for($i=3; $i<50; $i++) {
          echo sprintf("<p class=\"form-text\">position</p><input type=\"text\" name=\"position%d%d\" placeholder=\"\"><br>", $i/10, $i%10);
          echo sprintf("<p class=\"form-text\">organization</p><input type=\"text\" name=\"organization%d%d\" placeholder=\"\"><br>", $i/10, $i%10);
          echo sprintf("<p class=\"form-text\">start</p><input type=\"text\" name=\"startdate%d%d\" placeholder=\"\"><br>", $i/10, $i%10);
          echo sprintf("<p class=\"form-text\">end</p><input type=\"text\" name=\"enddate%d%d\" placeholder=\"\"><br>", $i/10, $i%10);
          echo sprintf("<p class=\"form-text\">projects</p><textarea name=\"projects%d%d\" placeholder=\"\"></textarea><br><br><br><br><br><br><br><br><br>", $i/10, $i%10);
        }
      ?>
      <input type="submit" value="save profile">
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
