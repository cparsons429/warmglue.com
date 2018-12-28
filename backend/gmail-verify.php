<?php
  session_start();

  // preventing information leakage
  if (!$_SESSION['verification_access_allowed']) {
    header("location: ../accessdenied");
    exit();
  }

  function verifyGmail($mysqli, $id, $e) {
    session_start();

    // preventing information leakage
    $_SESSION['db_access_allowed'] = 1;
    require 'db.php';
    $_SESSION['helper_functions_access_allowed'] = 1;
    require 'helper-functions.php';

    // create client
    $client = new Google_Client();
    $client->setApplicationName('Gmail Token Getter');
    $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
    $client->setAuthConfig('/client_secrets.json');
    $client->setAccessType('offline');
    $client->setPrompt('consent');
    $client->setLoginHint($e);
    $client->setRedirectUri('https://www.warmglue.com/backend/email-verification-queue');

    // set access token from mysql database
    $stmt = $mysqli->prepare("SELECT access_token FROM user_emails WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($accessToken);
    $stmt->close();

    $needAuthorization = 0;

    if ($accessToken === "") {
      // if this email has not been verified before
      $needAuthorization = 1;
    } else {
      // if this email has been verified before
      $client->setAccessToken($accessToken);

      if ($client->isAccessTokenExpired()) {
        // if access token is expired

        if ($client->getRefreshToken()) {
          // if we can just get a refresh token, do that
          $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());

          // save the new access token to mysql
          $stmt = $mysqli->prepare("UPDATE user_emails SET access_token=? WHERE id=?");
          $stmt->bind_param('si', $client->getAccessToken(), $id);
          $stmt->execute();
          $stmt->close();
        } else {
          // refresh token won't work, so need to re-request authorization
          $need_authorization = 1;
        }
      } else {
        // the access token works, so we were sent here in error
        $stmt = $mysqli->prepare("UPDATE user_emails SET is_connected=1 WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
      }
    }

    if ($needAuthorization) {
      $authUrl = $client->createAuthUrl();
      header('location: '.filter_var($authUrl, FILTER_SANITIZE_URL));
      exit();
    }
  }

 ?>
