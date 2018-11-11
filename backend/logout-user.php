<?php
  session_start();

  // preventing CSRF attacks
  if (!hash_equals($_SESSION['token'], $_POST['token'])) {
    header("location: ../requestforgery");
    exit();
  }

  session_unset();
  session_destroy();
  header("location: ../landing");
  exit();
 ?>
