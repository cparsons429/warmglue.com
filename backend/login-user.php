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

  // we search to make sure this is a taken email
  $stmt = $mysqli->prepare("SELECT COUNT(*), user_id FROM user_emails WHERE email=?");
  $stmt->bind_param('s', $_POST['email']);
  $stmt->execute();
  $stmt->bind_result($count, $u_id);
  $stmt->fetch();

  // compare the submitted password to the actual password hash
  if ($count == 1) {
    // we search for the password for this email
    $stmt = $mysqli->prepare("SELECT salted_hash FROM users WHERE id=?");
    $stmt->bind_param('i', $u_id);
    $stmt->execute();
    $stmt->bind_result($pwd_hash);
    $stmt->fetch();

    if (password_verify($_POST['password'], $pwd_hash)) {
	     // login succeeded
       // note that we don't want to delete the session email - we want to keep it available to make sign in easy in case
       // the user signs out
	     $_SESSION['user_id'] = $u_id;
       $_SESSION['logged_in'] = 1;
	     header("location: home");
       exit();
    } else {
       // login must have failed because password didn't match password in records
       // let the user know the password was incorrect and exit
       addToMessage("Incorrect password.");
       exit();
    }
  }

  // login must have failed because email address isn't in the records
  // let the user know the email address isn't registered, then we finish
  addToMessage("\"" + $_SESSION['email_attempt'] + "\" isn't associated with any account.");

 ?>
