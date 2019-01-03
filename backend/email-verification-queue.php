<?php
  session_start();

  $_SESSION['db_access_allowed'] = 1;
  require 'db.php';
  $_SESSION['helper_functions_access_allowed'] = 1;
  require 'helper-functions.php';
  $_SESSION['verification_access_allowed'] = 1;
  require 'gmail-verify.php';
  /*
  require 'yahoo-verify.php';
  require 'outlook-verify.php';
  require 'aol-verify.php';
  require 'icloud-verify.php';
  require 'comcast-verify.php';
  */
  $_SESSION['verification_access_allowed'] = 0;
  require '../vendor/autoload.php';

  // connect, while preventing CSRF attacks
  $mysqli = authenticated_connect($_SESSION['token']);

  if ($_SESSION['first_verify_queue'] == 1) {
    // in case this is our very first email, we were just redirected here from connect-emails, not from a verification script
    // so we want to skip this database-writing step
    $_SESSION['first_verify_queue'] = 0;
  } else if ($_SESSION['e_queue'][0][2] === "gmail") {
    if (isset($_GET['code'])) {
        // if the user succesfully verified a gmail email
        // create client
        $client = new Google_Client();
        $client->setApplicationName('Gmail Token Writer');
        $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
        $client->setAuthConfig('/client_secrets.json');
        $client->setAccessType('offline');
        $client->setRedirectUri('https://www.warmglue.com/backend/email-verification-queue');
        $client->authenticate($_GET['code']);

        // making sure provided email matches desired email
        $service = new Google_Service_Gmail($client);

        try {
          // try to get the user's email with this client
          // if this fails, this means the user must have verified a different email
          $userInfo = $service->users->getProfile($_SESSION['e_queue'][0][1]);

          // save access token to mysql
          $stmt = $mysqli->prepare("UPDATE user_emails SET is_connected=1, access_token=? WHERE id=?");
          $stmt->bind_param('si', json_encode($client->getAccessToken()), $_SESSION['e_queue'][0][0]);
          $stmt->execute();
          $stmt->close();
        } catch (Exception $exc) {
          // user verified a different email, so just leave this email as unconnected and move on
        }
    }

    array_shift($_SESSION['e_queue']);
  } else if ($_SESSION['e_queue'][0][2] === "yahoo") {
    if (isset($_GET['code'])) {
      // if the user succesfully verified a gmail email, label it as connected and include its access token
      $stmt = $mysqli->prepare("UPDATE user_emails SET is_connected=1, access_token=? WHERE id=?");
      $stmt->bind_param('si', $_GET['code'], $_SESSION['e_queue'][0][0]);
      $stmt->execute();
      $stmt->close();
    }

    array_shift($_SESSION['e_queue']);
  } else if ($_SESSION['e_queue'][0][2] === "outlook") {
    if (isset($_GET['code'])) {
      // if the user succesfully verified a gmail email, label it as connected and include its access token
      $stmt = $mysqli->prepare("UPDATE user_emails SET is_connected=1, access_token=? WHERE id=?");
      $stmt->bind_param('si', $_GET['code'], $_SESSION['e_queue'][0][0]);
      $stmt->execute();
      $stmt->close();
    }

    array_shift($_SESSION['e_queue']);
  } else if ($_SESSION['e_queue'][0][2] === "aol") {
    if (isset($_GET['code'])) {
      // if the user succesfully verified a gmail email, label it as connected and include its access token
      $stmt = $mysqli->prepare("UPDATE user_emails SET is_connected=1, access_token=? WHERE id=?");
      $stmt->bind_param('si', $_GET['code'], $_SESSION['e_queue'][0][0]);
      $stmt->execute();
      $stmt->close();
    }

    array_shift($_SESSION['e_queue']);
  } else if ($_SESSION['e_queue'][0][2] === "icloud") {
    if (isset($_GET['code'])) {
      // if the user succesfully verified a gmail email, label it as connected and include its access token
      $stmt = $mysqli->prepare("UPDATE user_emails SET is_connected=1, access_token=? WHERE id=?");
      $stmt->bind_param('si', $_GET['code'], $_SESSION['e_queue'][0][0]);
      $stmt->execute();
      $stmt->close();
    }

    array_shift($_SESSION['e_queue']);
  } else if ($_SESSION['e_queue'][0][2] === "comcast") {
    if (isset($_GET['code'])) {
      // if the user succesfully verified a gmail email, label it as connected and include its access token
      $stmt = $mysqli->prepare("UPDATE user_emails SET is_connected=1, access_token=? WHERE id=?");
      $stmt->bind_param('si', $_GET['code'], $_SESSION['e_queue'][0][0]);
      $stmt->execute();
      $stmt->close();
    }

    array_shift($_SESSION['e_queue']);
  } else if (count($_SESSION['e_queue']) > 0) {
    // it's an "other" esp, so label email as unconnected
    array_shift($_SESSION['e_queue']);
  } else {
    // all emails were connected
  }

  if (count($_SESSION['e_queue']) > 0) {
    // attempt to verify with another email
    if ($_SESSION['e_queue'][0][2] === "gmail") {
      verifyGmail($mysqli, $_SESSION['e_queue'][0][0], $_SESSION['e_queue'][0][1]);
      exit();
    } else if ($_SESSION['e_queue'][0][2] === "yahoo") {
      verifyYahoo($mysqli, $_SESSION['e_queue'][0][0], $_SESSION['e_queue'][0][1]);
      exit();
    } else if ($_SESSION['e_queue'][0][2] === "outlook") {
      verifyOutlook($mysqli, $_SESSION['e_queue'][0][0], $_SESSION['e_queue'][0][1]);
      exit();
    } else if ($_SESSION['e_queue'][0][2] === "aol") {
      verifyAol($mysqli, $_SESSION['e_queue'][0][0], $_SESSION['e_queue'][0][1]);
      exit();
    } else if ($_SESSION['e_queue'][0][2] === "icloud") {
      verifyIcloud($mysqli, $_SESSION['e_queue'][0][0], $_SESSION['e_queue'][0][1]);
      exit();
    } else if ($_SESSION['e_queue'][0][2] === "comcast") {
      verifyComcast($mysqli, $_SESSION['e_queue'][0][0], $_SESSION['e_queue'][0][1]);
      exit();
    } else {
      // if it's an "other" esp, send it back here so that the email is labeled as unconnected
      header("location: email-verification-queue");
      exit();
    }
  } else if ($_SESSION['registering'] == 1) {
    // if they're registering, send them to search
    $_SESSION['e_queue'] = null;
    $_SESSION['registering'] = 0;
    header("location: ../search");
    exit();
  } else {
    // otherwise, send them home
    $_SESSION['e_queue'] = null;
    header("location: ../home");
    exit();
  }
 ?>
