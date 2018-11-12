<?php
  session_start();

  // preventing information leakage
  if (!$_SESSION['pull_profile_access_allowed']) {
    header("location: ../accessdenied");
    exit();
  } else {
    $_SESSION['pull_profile_access_allowed'] = 0;
  }

  function getInfo($token) {
    session_start();

    // preventing information leakage
    $_SESSION['db_access_allowed'] = 1;
    require 'db.php';
    $_SESSION['helper_functions_access_allowed'] = 1;
    require 'helper-functions.php';

    // connect, while preventing CSRF attacks
    $mysqli = authenticated_connect($token);

    // get first and last name
    $stmt = $mysqli->prepare("SELECT first_name, last_name FROM users WHERE id=?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($first, $last);
    $stmt->fetch();

    $names = array();

    if (isset($first)) {
      array_push($names, $first);
    } else {
      array_push($names, "");
    }

    if (isset($last)) {
      array_push($names, $last);
    } else {
      array_push($names, "");
    }

    $stmt->close();

    // now, for emails

    // find current emails
    $stmt = $mysqli->prepare("SELECT email, is_primary, updated FROM user_emails WHERE user_id=? ORDER BY updated DESC");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($email, $primary_str, $_);

    // get non-empty searches
    $emails = array();

    while ($stmt->fetch()) {
      $primary = intval($primary_str);

      if ($primary) {
        // if it's our primary email, make it the first element
        array_unshift($emails, htmlentities($email));
      } else {
        // otherwise, make it the last element
        array_push($emails, htmlentities($email));
      }
    }

    $stmt->close();

    // fill in empty values for first 4 emails in case they don't exist
    // this is so we don't have to include special logic for those first 4 emails depending whether or not there has been
    // an input email
    for ($i = count($emails); $i < 4; $i++) {
      array_push($emails, "");
    }

    // now, for occupations

    // connect, while preventing CSRF attacks
    $mysqli = authenticated_connect($token);

    // find current occupations
    $stmt = $mysqli->prepare("SELECT position, organization, start_date, end_date, projects FROM user_occupations WHERE user_id=? ORDER BY DATE(start_date) DESC");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($position, $organization, $start_date, $end_date, $projects);

    // get non-empty occupations
    $occupations = array();

    while($stmt->fetch()) {
      // in case the user didn't input any project here
      if (!isset($projects)) {
        $projects_edited = "";
      } else {
        $projects_edited = htmlentities($projects);
      }

      array_push($occupations, array(htmlentities($position), htmlentities($organization), getDateStr($start_date), getDateStr($end_date), $projects_edited));
    }

    $stmt->close();

    // fill in empty values for first 3 occupations in case they don't exist
    // this is so we don't have to include special logic for those first 3 occupations depending whether or not there has
    // been an input occupation
    for ($i = count($occupations); $i < 3; $i++) {
      array_push($occupations, array("", "", "", "", ""));
    }

    return array($names, $emails, $occupations);
  }

 ?>
