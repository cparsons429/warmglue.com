<?php
  session_start();

  // preventing information leakage
  if (!$_SESSION['change_password_verify_allowed']) {
    header("location: ../accessdenied");
    exit();
  } else {
    $_SESSION['change_password_verify_allowed'] = 0;
  }

  function change_pw_account($allowed_access, $token) {
    // preventing information leakage
    // return 0 if there's no account with the given token or the reset window has expired; otherwise, return the account id
    if (!$allowed_access) {
      header("location: ../accessdenied");
      exit();
    }

    $_SESSION['db_access_allowed'] = 1;
    require 'db.php';

    $mysqli = public_connect($allowed_access);

    $stmt = $mysqli->prepare("SELECT COUNT(*), id, UNIX_TIMESTAMP(pw_reset_limit) FROM users WHERE pw_reset_token=?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($count_str, $u_id_str, $max_time_str);
    $stmt->fetch();
    $stmt->close();

    $count = intval($count_str);
    $u_id = intval($u_id_str);
    $max_time = intval($max_time_str);

    if ($count == 1) {
      if (time() > $max_time) {
        return 0;
      }

      return $u_id;
    }

    return 0;
  }
 ?>
