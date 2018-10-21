<?php
  require 'backend/db-users.php';

  // make sure first name matches regex to prevent sql injections
  if (preg_match("/^[a-zA-Z\s,.'-\pL]+$/u", $_POST['first_name'])) {
    $first = $_POST['first_name'];
  } else {
    $_SESSION['message'] = "\"" + htmlentities($_POST['first_name']) + "\" seems to have some non-standard characters for a name. Try again.";
    exit();
  }

  // make sure last name name matches regex to prevent sql injections
  if (preg_match("/^[a-zA-Z\s,.'-\pL]+$/u", $_POST['last_name'])) {
    $last = $_POST['last_name'];
  } else {
    $_SESSION['message'] = "\"" + htmlentities($_POST['last_name']) + "\" seems to have some non-standard characters for a name. Try again.";
    exit();
  }

  // make sure emails match regex to prevent sql injections
  for ($i = 0; $i < 30; i)
?>
