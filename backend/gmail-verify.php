<?php
  session_start();

  // preventing information leakage
  if (!$_SESSION['verification_access_allowed']) {
    header("location: ../accessdenied");
    exit();
  }

  function verifyGmail($mysqli, $id, $e) {
    require '../vendor/autoload.php';

    session_start();

    // create client
    $client = new Google_Client();
    $client->setApplicationName('Gmail Token Getter');
    $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
    $client->setAuthConfig('/client_secrets.json');
    $client->setAccessType('offline');
    $client->setPrompt('consent');
    $client->setLoginHint($e);
    $client->setRedirectUri('https://www.warmglue.com/backend/email-verification-queue');

    // get access token from mysql database
    $stmt = $mysqli->prepare("SELECT access_token FROM user_emails WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($accessTokenJson);
    $stmt->fetch();
    $stmt->close();

    // setting access token, and handling case when access token is an empty entry in mysql
    try {
      $client->setAccessToken(json_decode($accessTokenJson, true));
    } catch (Exception $exc) {
      $client->setAccessToken('');
    }

    if ($client->isAccessTokenExpired()) {
      // if there's no previous token or it's expired
      if ($client->getRefreshToken()) {
        // refresh the token if possible, else fetch a new one
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());

        $stmt = $mysqli->prepare("UPDATE user_emails SET access_token=? WHERE id=?");
        $stmt->bind_param('si', json_encode($client->getAccessToken()), $id);
        $stmt->execute();
        $stmt->close();
      } else {
        // request authorization from the user
        $authUrl = $client->createAuthUrl();

        header('location: '.filter_var($authUrl, FILTER_SANITIZE_URL));
        exit();
      }
    }
  }

 ?>
