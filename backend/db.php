<?php
  $host = 'localhost';
  $user = 'phpaccess';
  $pass = 'Unfortunately,dogscan’tread-yet';
  $db = 'WARMGLUE_USERS';
  $mysqli = new mysqli($host,$user,$pass,$db);

  if ($mysqli->connect_errno) {
    printf("Connection Failed: %s\n", $mysqli->connect_error);
  }
?>
