<?php
  function public_connect() {
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
