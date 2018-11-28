<?php
  session_start();

  $_SESSION['db_access_allowed'] = 1;
  require 'db.php';
  $_SESSION['helper_functions_access_allowed'] = 1;
  require 'helper-functions.php';

  // connect, while preventing CSRF attacks
  $mysqli = authenticated_connect($_POST['token']);

  // clear our error message, and let frontend know it was just redirected from the backend
  $_SESSION['message'] = null;
  $_SESSION['backend_redirect'] = 1;
  $_SESSION['intro_number'] = intval($_POST['intro_num']);
  $_SESSION['scroll_value'] = intval($_POST['scroll_val']);

  // save the rating and reason the user attempted in case submission fails
  // make sure to escape string to prevent sql injections
  $rate_str = trim($_POST['rating']);
  $reas = trim($_POST['reason']);
  $_SESSION['rating_attempt'] = htmlentities($rate_str);
  $_SESSION['reason_attempt'] = htmlentities($reas);

  // make sure first name matches regex to prevent sql injections, and make sure the user has input an first name
  if ($rate_str === "") {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "You have to provide a rating to proceed.");
  } else if (!isRating($rate_str)) {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "Your rating has to be an integer from 1 to 5. \"1\" means the intro was poor; \"5\" means it was great.");
  } else {
    // we're good to go
    $rate = intval($rate_str);
  }

  if ($reas === "") {
    $reas = null;
  }

  if (!isset($_SESSION['message'])) {
    // there were no errors, and we're good to go
    $stmt = $mysqli->prepare("UPDATE intros SET provided_rating=?, rating_reason=? WHERE id=?");
    $stmt->bind_param('isi', $rate, $reas, $_POST['intro_id']);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = updateMessage($_SESSION['message'], "Rating succesfully saved.");
    $_SESSION['rating_attempt'] = null;
    $_SESSION['reason_attempt'] = null;
  }

  header("location: ../home");
  exit();
 ?>
