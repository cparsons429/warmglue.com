<?php
  session_start();

  $_SESSION['db_access_allowed'] = 1;
  require 'db.php';
  $_SESSION['helper_functions_access_allowed'] = 1;
  require 'helper-functions.php';
  $_SESSION['mail_access_allowed'] = 1;
  require 'mail.php';

  // connect
  $mysqli = public_connect(intval($_POST['reset_pw_access']));

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
    header("location: ../resetpassword");
    exit();
  } else if (!isEmail($e)) {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "\"".$e."\" doesn't look like an email address.");
    header("location: ../resetpassword");
    exit();
  } else {
    // we're good to go
  }

  // we search to make sure this is a taken email
  $stmt = $mysqli->prepare("SELECT COUNT(*), user_id FROM user_emails WHERE email=?");
  $stmt->bind_param('s', $e);
  $stmt->execute();
  $stmt->bind_result($count_str, $u_id_str);
  $stmt->fetch();
  $stmt->close();

  $count = intval($count_str);
  $u_id = intval($u_id_str);

  // if this email address is associated with an account, we're good to send a password reset email
  if ($count == 1) {
    $stmt = $mysqli->prepare("SELECT first_name, last_name FROM users WHERE id=?");
    $stmt->bind_param('i', $u_id);
    $stmt->execute();
    $stmt->bind_result($f_name, $l_name);
    $stmt->fetch();
    $stmt->close();

    $reset_token = random_string();

    $stmt = $mysqli->prepare("UPDATE users SET pw_reset_token=?, pw_reset_limit=? WHERE id=?");
    $stmt->bind_param('ssi', $reset_token, date('Y-m-d H:i:s', strval(time() + 3600)), $u_id);
    $stmt->execute();
    $stmt->close();

    $mail = public_setup_mail(intval($_POST['reset_pw_access']));
    $mail->From = 'warmglue@warmglue.com';
    $mail->FromName = 'warmglue';
    $mail->AddAddress($e, $f_name." ".$l_name);
    $mail->Subject = 'Reset warmglue password';
    $mail->Body = "Hi ".$f_name.",\r\n\r\nClick the link below, or copy-paste it into your browser window, to reset your warmglue password:\r\n\r\nhttps://www.warmglue.com/changepassword?token=".$reset_token."\r\n\r\nThis link will expire in one hour.\r\n\r\nIf you didn't request a password reset, you can safely ignore this email.\r\n\r\n-warmglue";

    $mail->Send();

    $_SESSION['reset_confirmed'] = 1;
    header("location: ../resetsent");
    exit();
  }

  // login must have failed because email address isn't in the records
  // let the user know the email address isn't registered, then we finish
  $_SESSION['message'] = updateMessage($_SESSION['message'], "\"".$e."\" isn't associated with any account.");
  header("location: ../resetpassword");
  exit();

 ?>
