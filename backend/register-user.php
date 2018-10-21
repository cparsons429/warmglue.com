<?php
  require 'db-users.php';
  require 'helper-functions.php';

  // clear our error message so that we can display the correct errors to the user
  $_SESSION['message'] = "";

  // save the email the user attempted for use on multiple pages
  $_SESSION['email_attempt'] = $_POST['email'];

  // make sure email matches regex to prevent sql injections
  if (isEmail($_POST['email'])) {
    $e = $_POST['email'];
  } else {
    // let user know that this isn't an email address and exit
    addToMessage("\"" + htmlentities($_POST['email']) + "\" doesn't look like an email address.");
    exit();
  }

  // escape passwords to protect against SQL injections
  $pwd0 = $mysqli->escape_string($_POST['password0']);
  $pwd1 = $mysqli->escape_string($_POST['password1']);

  // we need to make sure the email isn't already registered with an account
  $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE primary_email=? OR secondary_email0=? OR secondary_email1=? OR secondary_email2=? OR secondary_email3=? OR secondary_email4=? OR secondary_email5=? OR secondary_email6=? OR secondary_email7=? OR secondary_email8=?");

  // bind parameter
  $stmt->bind_param('ssssssssss', $e, $e, $e, $e, $e, $e, $e, $e, $e, $e);
  $stmt->execute();

  // bind results
  $stmt->bind_result($cnt);
  $stmt->fetch();

  if ($cnt > 0) {
    // email must already be associated with an account
    // let the user know and exit
    addToMessage("\"" + htmlentities($e) + "\" is already associated with an account.");
    exit();
  } else if (!password_verify($pwd0, $pwd1)) {
    // passwords don't match one another
    // let the user know and exit
    addToMessage("Those passwords don't match.");
    exit();
  }
  else {
    // we're good to create a user
    $stmt = $mysqli->prepare("INSERT INTO users (primary_email, salted_hash) VALUES (?, ?)");
    $stmt->bind_param('ss', $_POST['email'], password_hash($_POST['password0'], PASSWORD_DEFAULT));
    $stmt->execute();

    // automatically log in, and have the user complete their profile
    $_SESSION['user_id'] = "SELECT MAX(id) from users";
    $_SESSION['logged_in'] = 1;
    $_POST['registering'] = 1;
    header("location: profile");
  }

?>
