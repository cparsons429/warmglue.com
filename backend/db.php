<?php
  session_start();

  // preventing information leakage
  if (!$_SESSION['db_access_allowed']) {
    header("location: ../accessdenied");
    exit();
  } else {
    $_SESSION['db_access_allowed'] = 0;
  }

  function public_connect($allowed_access) {
    // preventing information leakage
    if (!$allowed_access) {
      header("location: ../accessdenied");
      exit();
    }

    // before the user has authenticated
    $host = 'localhost';
    $user = 'phpaccess';
    $pass = 'Unfortunately,dogscan’tread-yet';
    $db = 'WARMGLUE_USERS';
    $mysqli = new mysqli($host,$user,$pass,$db);

    if ($mysqli->connect_errno) {
      header("location: ../servererror");
      exit();
    }

    return $mysqli;
  }

  function authenticated_connect($token) {
    // preventing CSRF attacks
    if (!hash_equals($_SESSION['token'], $token)) {
      header("location: ../requestforgery");
      exit();
    }

    $host = 'localhost';
    $user = 'phpaccess';
    $pass = 'Unfortunately,dogscan’tread-yet';
    $db = 'WARMGLUE_USERS';
    $mysqli = new mysqli($host,$user,$pass,$db);

    if ($mysqli->connect_errno) {
      header("location: ../servererror");
      exit();
    }

    return $mysqli;
  }
?>
