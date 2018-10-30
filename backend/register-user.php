<?php
  require 'db.php';
  require 'helper-functions.php';

  // clear our error message so that we can display the correct errors to the user
  $_SESSION['message'] = "";

  // save the email the user attempted for use on multiple pages / in case submission fails
  // make sure to escape string to prevent sql injections
  $_SESSION['email_attempt'] = htmlentities($_POST['email']);

  // make sure email matches regex to prevent sql injections
  if (!isEmail($_POST['email'])) {
    // let user know that this isn't an email address and exit
    addToMessage("\"" + $_SESSION['email_attempt'] + "\" doesn't look like an email address.");
    exit();
  }

  // we search to make sure this isn't a taken email
  $stmt = $mysqli->prepare("SELECT COUNT(*) FROM user_emails WHERE email=?");
  $stmt->bind_param('s', $_POST['email']);
  $stmt->execute();
  $stmt->bind_result($count);
  $stmt->fetch();

  if ($count == 1) {
    // email must already be associated with an account
    // let the user know and exit
    addToMessage("\"" + $_SESSION['email_attempt'] + "\" is already associated with an account.");
    exit();
  } else {
    // we need to make sure the passwords match one another
    if ($_POST['password0'] === $_POST['password1']) {
      // we're good to create a user
      $stmt = $mysqli->prepare("INSERT INTO users (salted_hash) VALUES (?)");
      $stmt->bind_param('s', password_hash($_POST['password0'], PASSWORD_DEFAULT));
      $stmt->execute();

      // get user id
      $stmt = $mysqli->prepare("SELECT LAST_INSERT_ID()");
      $stmt->execute();
      $stmt->bind_result($u_id);
      $stmt->fetch();

      // add their email to the db
      $stmt = $mysqli->prepare("INSERT INTO user_emails (user_id, email, is_primary) VALUES (?, ?, ?)");
      $stmt->bind_param("isi", $u_id, $_POST['email'], 1);
      $stmt->execute();

      // automatically log in, and have the user complete their profile
      // note that we don't want to delete the session email - we want to keep it available to make sign in easy in case
      // the user signs out
      $_SESSION['user_id'] = $u_id;
      $_SESSION['logged_in'] = 1;
      $_POST['registering'] = 1;
      header("location: profile");
    } else {
      // passwords don't match one another
      // let the user know and exit
      addToMessage("Those passwords don't match.");
      exit();
    }
  }

?>
