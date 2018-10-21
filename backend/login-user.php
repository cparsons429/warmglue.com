<?php
  require 'db-users.php';
  require 'helper-functions.php';

  // clear our error message so that we can display the correct errors to the user
  $_SESSION['message'] = "";

  // save the email the user attempted for use on multiple pages
  $_SESSION['email_attempt'] = $_POST['email'];

  // make sure email matches regex to prevent sql injections
  if (isEmail($_POST['email'])) {
    $email_guess = $_POST['email'];
  } else {
    // let user know that this isn't an email address and exit
    addToMessage("\"" + htmlentities($_POST['email']) + "\" doesn't look like an email address.");
    exit();
  }

  // escape password to prevent sql injections
  $pwd_guess = escape_string($_POST['password']);

  // we search in order of provided emails, because the user is more likely to provide an email they specified earlier in sign up
  $stmts = array($mysqli->prepare("SELECT COUNT(*), id, salted_hash FROM users WHERE primary_email=?"),
    $mysqli->prepare("SELECT COUNT(*), id, salted_hash FROM users WHERE secondary_email0=?"),
    $mysqli->prepare("SELECT COUNT(*), id, salted_hash FROM users WHERE secondary_email1=?"),
    $mysqli->prepare("SELECT COUNT(*), id, salted_hash FROM users WHERE secondary_email2=?"),
    $mysqli->prepare("SELECT COUNT(*), id, salted_hash FROM users WHERE secondary_email3=?"),
    $mysqli->prepare("SELECT COUNT(*), id, salted_hash FROM users WHERE secondary_email4=?"),
    $mysqli->prepare("SELECT COUNT(*), id, salted_hash FROM users WHERE secondary_email5=?"),
    $mysqli->prepare("SELECT COUNT(*), id, salted_hash FROM users WHERE secondary_email6=?"),
    $mysqli->prepare("SELECT COUNT(*), id, salted_hash FROM users WHERE secondary_email7=?"),
    $mysqli->prepare("SELECT COUNT(*), id, salted_hash FROM users WHERE secondary_email8=?"));

  // seeing if this matches one of our users
  foreach ($stmts as $key => $stmt) {
    // bind the parameter
    $stmt->bind_param('s', $email_guess);
    $stmt->execute();

    // bind the results
    $stmt->bind_result($cnt, $user_id, $pwd_hash);
    $stmt->fetch();

    // compare the submitted password to the actual password hash
    if ($cnt == 1) {
      if (password_verify($pwd_guess, $pwd_hash)) {
	       // login succeeded
	       $_SESSION['user_id'] = $user_id;
         $_SESSION['logged_in'] = 1;
	       header("location: home");
         exit();
      }
      else {
        // login must have failed because password didn't match password in records
        // let the user know the password was incorrect and exit
        addToMessage("Incorrect password.");
        exit();
      }
    }
  }

  // login must have failed because email address isn't in the records
  // let the user know the email address isn't registered, then we finish
  addToMessage("\"" + htmlentities($email_guess) + "\" isn't associated with any account.");

 ?>
