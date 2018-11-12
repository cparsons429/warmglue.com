<?php
  session_start();

  // preventing CSRF attacks
  if (!hash_equals($_SESSION['token'], $_POST['token'])) {
    header("location: ../requestforgery");
    exit();
  }

  header("location: ../home");
  exit();
 ?>
