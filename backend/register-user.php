<?php
  session_start();

  $_SESSION['db_access_allowed'] = 1;
  require 'db.php';
  $_SESSION['confirm_email_access_allowed'] = 1;
  require 'confirm-email.php';
  $_SESSION['helper_functions_access_allowed'] = 1;
  require 'helper-functions.php';

  // connect
  $mysqli = public_connect(intval($_POST['signup_access']));

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
    header("location: ../signup");
    exit();
  } else if (!isEmail($e)) {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "\"".$e."\" doesn't look like an email address.");
    header("location: ../signup");
    exit();
  } else {
    // we're good to go
  }

  // we search to make sure this isn't a taken email
  $stmt = $mysqli->prepare("SELECT COUNT(*) FROM user_emails WHERE email=?");
  $stmt->bind_param('s', $e);
  $stmt->execute();
  $stmt->bind_result($count_str);
  $stmt->fetch();
  $stmt->close();

  $count = intval($count_str);

  if ($count == 1) {
    // email must already be associated with an account
    // let the user know and exit
    $_SESSION['message'] = updateMessage($_SESSION['message'], "\"".$e."\" is already associated with an account.");
    header("location: ../signup");
    exit();
  } else {
    // we need to make sure the passwords have been input
    if ($_POST['password0'] === "" || $_POST['password1'] === "") {
      $_SESSION['message'] = updateMessage($_SESSION['message'], "You have to complete both password fields to proceed.");
      header("location: ../signup");
      exit();
    }

    // we then have to make sure the passwords match one another
    if ($_POST['password0'] === $_POST['password1']) {
      // make sure passwords meet minimum complexity requirements
      if (!passwordComplexEnough($_POST['password0'])) {
        $_SESSION['message'] = updateMessage($_SESSION['message'], "Your password must be at least 8 characters long.");
        header("location: ../signup");
        exit();
      }

      // make sure user has agreed to the TOS and PP
      if (!($_POST['agree'] === "y")) {
        $_SESSION['message'] = updateMessage($_SESSION['message'], "You must agree to the terms of service and privacy policy to proceed.");
        header("location: ../signup");
        exit();
      }

      // we're good to create a user
      $stmt = $mysqli->prepare("INSERT INTO users (salted_hash) VALUES (?)");
      $stmt->bind_param('s', password_hash($_POST['password0'], PASSWORD_DEFAULT));
      $stmt->execute();
      $stmt->close();

      // get user id
      $u_id = $mysqli->insert_id;

      // add their email to the db
      $stmt = $mysqli->prepare("INSERT INTO user_emails (user_id, email, is_primary) VALUES (?, ?, 1)");
      $stmt->bind_param("is", $u_id, $e);
      $stmt->execute();
      $stmt->close();

      // automatically log in, and have the user complete their profile
      // note that we don't want to delete the session email - we want to keep it available to make sign in easy in case
      // the user signs out
      $_SESSION['user_id'] = $u_id;
      $_SESSION['logged_in'] = 1;
      $_SESSION['token'] = bin2hex(random_bytes(32));
      $_POST['registering'] = 1;

      // immediately confirm primary email
      confirmEmail($mysqli, $_SESSION['token']);

      header("location: ../profile");
      exit();
    } else {
      // passwords don't match one another
      // let the user know and exit
      $_SESSION['message'] = updateMessage($_SESSION['message'], "Those passwords don't match.");
      header("location: ../signup");
      exit();
    }
  }

?>
