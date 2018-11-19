<?php
  session_start();

  // preventing information leakage
  if (!$_SESSION['confirm_email_access_allowed']) {
    header("location: ../accessdenied");
    exit();
  } else {
    $_SESSION['confirm_email_access_allowed'] = 0;
  }

  function confirmEmail($mysqli, $token) {
    session_start();

    // preventing CSRF attacks
    if (!hash_equals($_SESSION['token'], $token)) {
      header("location: ../requestforgery");
      exit();
    }

    // preventing information leakage
    $_SESSION['mail_access_allowed'] = 1;
    require 'mail.php';

    // get user's first name
    $stmt = $mysqli->prepare("SELECT first_name, last_name FROM users WHERE id=?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($f_name, $l_name);
    $stmt->fetch();
    $stmt->close();

    // get all incomplete emails
    $stmt = $mysqli->prepare("SELECT email FROM user_emails WHERE user_id=? AND is_connected=0");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($to);

    // for testing purposes
    $a_hash = "blah";

    while ($stmt->fetch()) {
      // send an email to this address with verification link
      $mail = setup_mail($token);
      $mail->From = 'warmglue@warmglue.com';
      $mail->FromName = 'warmglue';
      $mail->AddAddress($to, $f_name." ".$l_name);
      $mail->Subject = 'Connect email to warmglue';
      $mail->Body = 'Hi '.$f_name.',

      Click the link below, or copy-paste it into your browser window, to connect this email to warmglue:

      http://ec2-18-234-43-121.compute-1.amazonaws.com/confirmemail?email='.$to.'&hash='.$a_hash.'

      If you didn\'t sign up for this email, reply to let warmglue know there\'s been a mistake.

      -warmglue';

      $mail->Send();
    }

    $stmt->close();
  }
 ?>
