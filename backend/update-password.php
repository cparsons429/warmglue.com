<?php
  require 'db.php';
  require 'helper-functions.php';

  if ($_SESSION['logged_in'] == 1) {
    // the user is accessing changepassword after logging in
    $u_id = $_SESSION['user_id'];
  } else {
    // the user is either accessing changepassword from a reset email, or didn't receive any such email

  }

  if ($_POST['password0'] === $_POST['password1']) {
    // we're good to update password
    $stmt = $mysqli->prepare("UPDATE users SET (salted_hash) VALUES (?) WHERE id=?");
    $stmt->bind_param("si", password_hash($_POST['password0'], PASSWORD_DEFAULT), $u_id);
    $stmt->execute();
  } else {
    // passwords don't match one another
    // let the user know and exit
    addToMessage("Those passwords don't match.");
    exit();
  }
 ?>
