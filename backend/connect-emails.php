<?php
  session_start();

  $_SESSION['db_access_allowed'] = 1;
  require 'db.php';
  $_SESSION['helper_functions_access_allowed'] = 1;
  require 'helper-functions.php';
  $_SESSION['mail_access_allowed'] = 1;
  require 'mail.php';

  // connect, while preventing CSRF attacks
  $mysqli = authenticated_connect($_SESSION['token']);

  // find all unconnected emails
  $stmt = $mysqli->prepare("SELECT id, email FROM user_emails WHERE user_id=? AND is_connected=0");
  $stmt->bind_param('i', $_SESSION['user_id']);
  $stmt->execute();
  $stmt->bind_result($id_str, $e);

  $unconnected_e = [];

  while ($stmt->fetch()) {
    $id = intval($id_str);
    $unconnected_e[$id] = $e;
  }

  $stmt->close();

  $_SESSION['e_queue'] = [];

  foreach ($unconnected_e as $id => $e) {
    // get the domain
    $at_pos = strpos($e, "@");
    $hostname = substr($e, $at_pos + 1);

    // see if this esp has already been catalogued
    $stmt = $mysqli->prepare("SELECT COUNT(*), esp FROM esps WHERE domain=?");
    $stmt->bind_param('s', $hostname);
    $stmt->execute();
    $stmt->bind_result($count_str, $esp);
    $stmt->close();

    $count = intval($count_str);

    if ($count > 0) {
      // this esp has already been catalogued
      array_push($_SESSION['e_queue'], [$id, $e, $esp]);
    } else {
      // this esp has not been catalogued
      $stmt = $mysqli->prepare("SELECT first_name, last_name FROM users WHERE id=?");
      $stmt->bind_param('i', $_SESSION['user_id']);
      $stmt->execute();
      $stmt->bind_result($f_name, $l_name);
      $stmt->fetch();
      $stmt->close();

      // require user to reply to an email, whose header will be analyzed to find the esp
      $mail = authenticated_setup_mail($_SESSION['token']);
      $mail->From = 'warmglue@warmglue.com';
      $mail->FromName = 'warmglue';
      $mail->AddAddress($e, $f_name." ".$l_name);
      $mail->Subject = 'Connect email to warmglue';
      $mail->Body = "Hi ".$f_name.",\r\n\r\nSince warmglue hasn't registered an email address at ".$hostname." before, please verify your email".$e."by replying with the sentence \"I'm not a robot.\"\r\n\r\nIf you didn't sign up for warmglue, you can safely ignore this email.\r\n\r\n-warmglue";

      $mail->Send();
    }
  }

  header('location: email-verification-queue');
  exit();

 ?>
