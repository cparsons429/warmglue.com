<?php
  session_start();

  $_SESSION['db_access_allowed'] = 1;
  require 'db.php';
  $_SESSION['helper_functions_access_allowed'] = 1;
  require 'helper-functions.php';

  // connect
  $mysqli = public_connect($_POST['reset_pw_id'] != 0);

  // clear our error message, and let frontend know it was just redirected from the backend
  $_SESSION['message'] = null;
  $_SESSION['backend_redirect'] = 1;

  // we need to make sure the passwords have been input
  if ($_POST['password0'] === "" || $_POST['password1'] === "") {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "You have to complete both password fields to proceed.");

    if ($_SESSION['logged_in']) {
      header("location: ../changepassword");
      exit();
    } else {
      header("location: ../changepassword?token=".$_POST['token']);
      exit();
    }
  }

  // we then have to make sure the passwords match one another
  if ($_POST['password0'] === $_POST['password1']) {
    // make sure passwords meet minimum complexity requirements
    if (!passwordComplexEnough($_POST['password0'])) {
      $_SESSION['message'] = updateMessage($_SESSION['message'], "Your password must be at least 8 characters long.");

      if ($_SESSION['logged_in']) {
        header("location: ../changepassword");
        exit();
      } else {
        header("location: ../changepassword?token=".$_POST['token']);
        exit();
      }
    }

    // we're good to update the password
    $stmt = $mysqli->prepare("UPDATE users SET salted_hash=?, pw_reset_token=null, pw_reset_limit=null WHERE id=?");
    $stmt->bind_param('si', password_hash($_POST['password0'], PASSWORD_DEFAULT), $_POST['reset_pw_id']);
    $stmt->execute();
    $stmt->close();

    $_SESSION['change_successful'] = 1;
    header("location: ../successfulchange");
    exit();
  } else {
    // passwords don't match one another
    // let the user know and exit
    $_SESSION['message'] = updateMessage($_SESSION['message'], "Those passwords don't match.");

    if ($_SESSION['logged_in']) {
      header("location: ../changepassword");
      exit();
    } else {
      header("location: ../changepassword?token=".$_POST['token']);
      exit();
    }
  }
 ?>
