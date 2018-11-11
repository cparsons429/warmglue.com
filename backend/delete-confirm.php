<?php
  session_start();

  require 'db.php';
  require 'helper-functions.php';

  // connect, while preventing CSRF attacks
  $mysqli = authenticated_connect($_POST['token']);

  // clear our error message, and let frontend know it was just redirected from the backend
  $_SESSION['message'] = null;
  $_SESSION['backend_redirect'] = 1;

  // escape output string to protect against sql injections
  $e_str = trim($_POST['email']);

  // make sure email matches regex to prevent sql injections
  if ($e_str === "") {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "You have to provide an email to proceed.");
    header("location: ../cancelaccount");
    exit();
  } else if (!isEmail($e_str)) {
    $_SESSION['message']= updateMessage($_SESSION['message'], "\"".$e_str."\" doesn't look like an email address.");
    header("location: ../cancelaccount");
    exit();
  } else {
    // we're good to go
  }

  // we search to make sure this is the same email as the user's email
  $stmt = $mysqli->prepare("SELECT email FROM user_emails WHERE user_id=?");
  $stmt->bind_param('i', $_SESSION['user_id']);
  $stmt->execute();
  $stmt->bind_result($e_pull);

  $correct_email = 0;

  while ($stmt->fetch()) {
    if ($e_pull === $e_str) {
      // the user has input an email that belongs to their account, so they are confirmed to cancel their account
      $correct_email = 1;
    }
  }

  $stmt->close();

  if (!$correct_email) {
    // let user know that this isn't an email address associated with their account and exit
    $_SESSION['message'] = updateMessage($_SESSION['message'], "\"".$e_str."\" isn't an email address associated with your account.");
    header("location: ../cancelaccount");
    exit();
  } else {
    // user is confirmed to delete their account
    $_SESSION['delete_confirmed'] = 1;
    header("location: ../accountcancelled");
    exit();
  }
 ?>
