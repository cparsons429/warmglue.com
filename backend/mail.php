<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  session_start();

  // preventing information leakage
  if (!$_SESSION['mail_access_allowed']) {
    header("location: ../accessdenied");
    exit();
  } else {
    $_SESSION['mail_access_allowed'] = 0;
  }

  function public_setup_mail($allowed_access) {
    // preventing information leakage
    if (!$allowed_access) {
      header("location: ../requestforgery");
      exit();
    }

    require 'phpmailer/phpmailer/src/Exception.php';
    require 'phpmailer/phpmailer/src/PHPMailer.php';
    require 'phpmailer/phpmailer/src/SMTP.php';

    $mail = new PHPMailer;

    $mail->IsSMTP();
    $mail->Host = 'email-smtp.us-east-1.amazonaws.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = 'AKIAJWD4TF2I2WR6ZLTQ';
    $mail->Password = 'AoU60gYiP1RRZSSVfTc5pqlyzMEYPP+L7HovsbZhO7f/';
    $mail->SMTPSecure = 'tls';

    return $mail;
  }

  function authenticated_setup_mail($token) {
    // preventing CSRF attacks
    if (!hash_equals($_SESSION['token'], $token)) {
      header("location: ../requestforgery");
      exit();
    }

    require 'phpmailer/phpmailer/src/Exception.php';
    require 'phpmailer/phpmailer/src/PHPMailer.php';
    require 'phpmailer/phpmailer/src/SMTP.php';

    $mail = new PHPMailer;

    $mail->IsSMTP();
    $mail->Host = 'email-smtp.us-east-1.amazonaws.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = 'AKIAJWD4TF2I2WR6ZLTQ';
    $mail->Password = 'AoU60gYiP1RRZSSVfTc5pqlyzMEYPP+L7HovsbZhO7f/';
    $mail->SMTPSecure = 'tls';

    return $mail;
  }

 ?>
