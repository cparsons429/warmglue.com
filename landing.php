<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/landing-styles.css">
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="scripts/landing-scripts.js"></script>
  <title>warmglue: log in or sign up</title>
  <meta charset="utf-8">
  <meta name="description" content="As you expand your professional network, warmglue finds the valuable intros your friends can make for you. Log in or sign up.">
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
      <a href="landing.php">
        <button class="button brand-image-button">
          <img class="logo" alt ="">
        </button>
      </a>
    </div>
    <div class="nav-sign-up-div disappear">
      <a href="signup.php" class="nav-sign-up-link disable-link">
        <button class="button nav-sign-up">sign up</button>
      </a>
    </div>
    <div class="nav-log-in-div disappear">
      <a href="login.php" class="nav-log-in-link disable-link">
        <button class="button nav-log-in">log in</button>
      </a>
    </div>
  </nav>
  <div class="main-body">
    <div class="explanation appear">
      <h1>Warm intros are the <b>best</b>.</h1>
      <p>As you expand your professional network, warmglue finds the valuable intros your friends can make for you.</p>
      <div class="body-log-in-div">
        <a href="login.php" class="body-log-in-link enable-link">
          <button class="button body-log-in">log in</button>
        </a>
      </div>
      <div class="body-sign-up-div">
        <a href="signup.php" class="body-sign-up-link enable-link">
          <button class="button body-sign-up">sign up</button>
        </a>
      </div>
      <div class="down-arrow-div expand">
        <img class="down-arrow" alt="">
      </div>
    </div>
    <div class="mobile-top-divider">
    </div>
    <div class="in-depth appear">
      <p>When you're networking, selling a product, or looking for a job, warmglue <b>finds</b> your friends' closest connections.<br><br>Then, warmglue finds where those connections have worked and studied, <b>recommending</b> valuable people with whom your friends can intro you.<br><br>Intros are suggested when they're professionally <b>valuable</b> and you have a good mutual friend.</p>
    </div>
    <div class="mobile-bottom-divider">
    </div>
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
