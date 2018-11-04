<?php
  session_start();

  require 'db.php';
  require 'helper-functions.php';

  // clear our error message, and let frontend know it was just redirected from the backend
  $_SESSION['message'] = null;
  $_SESSION['backend_redirect'] = 1;

  // save the email the user attempted for use on multiple pages / in case submission fails
  // make sure to escape string to prevent sql injections
  $e = trim($_POST['email']);
  $_SESSION['email_attempt'] = htmlentities($e);

  // make sure email matches regex to prevent sql injections, and make sure the user has input an email
  if ($e === "") {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "You have to provide an email to proceed.");
    $_SESSION['email_attempt'] = null;
    header("location: ../login");
    exit();
  } else if (!isEmail($e)) {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "\"".$e."\" doesn't look like an email address.");
    header("location: ../login");
    exit();
  } else {
    // we're good to go
  }

  // we search to make sure this is a taken email
  $stmt = $mysqli->prepare("SELECT COUNT(*), user_id FROM user_emails WHERE email=?");
  $stmt->bind_param('s', $e);
  $stmt->execute();
  $stmt->bind_result($count, $u_id);
  $stmt->fetch();
  $stmt->close();

  // compare the submitted password to the actual password hash
  if ($count == 1) {
    // make sure the user has input a password
    if ($_POST['password'] === "") {
      $_SESSION['message'] = updateMessage($_SESSION['message'], "You have to input your password to proceed.");
      header("location: ../login");
      exit();
    }

    // we search for the password for this email
    $stmt = $mysqli->prepare("SELECT salted_hash FROM users WHERE id=?");
    $stmt->bind_param('i', $u_id);
    $stmt->execute();
    $stmt->bind_result($pwd_hash);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($_POST['password'], $pwd_hash)) {
	     // login succeeded
	     $_SESSION['user_id'] = $u_id;
       $_SESSION['logged_in'] = 1;
       $_SESSION['token'] = bin2hex(random_bytes(32));
	     header("location: ../home");
       exit();
    } else {
       // login must have failed because password didn't match password in records
       // let the user know the password was incorrect and exit
       $_SESSION['message'] = updateMessage($_SESSION['message'], "Incorrect password.");
       header("location: ../login");
       exit();
    }
  }

  // login must have failed because email address isn't in the records
  // let the user know the email address isn't registered, then we finish
  $_SESSION['message'] = updateMessage($_SESSION['message'], "\"".$e."\" isn't associated with any account.");
  header("location: ../login");
  exit();

 ?>
