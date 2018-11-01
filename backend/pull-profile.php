<?php
  require 'db.php';

  function getName() {
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

    return $names;
  }

  function getEmails() {
    // find current emails
    $stmt = $mysqli->prepare("SELECT email, is_primary, updated FROM user_emails WHERE user_id=? ORDER BY MAX(updated) DESC");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($email, $primary, $_);

    // get non-empty searches
    $emails = array();

    while($stmt->fetch()) {
      if ($primary) {
        // if it's our primary email, make it the first element
        array_unshift($emails, htmlentities($email));
      } else {
        // otherwise, make it the last element
        array_push($emails, htmlentities($email));
      }
    }

    // fill in empty values for first 4 emails in case they don't exist
    // this is so we don't have to include special logic for those first 4 emails depending whether or not there has been
    // an input email
    for ($i = count($emails); $i < 4; $i++) {
      array_push($emails, "");
    }

    return $emails;
  }

  function getOccupations() {
    // find current occupations
    $stmt = $mysqli->prepare("SELECT position, organization, start_date, end_date, projects FROM user_occupations WHERE user_id=? ORDER BY MAX(start_date) DESC");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($position, $organization, $start_date, $end_date, $projects);

    // get non-empty searches
    $occupations = array();

    while($stmt->fetch()) {
      // converting dates to mm/dd/yyyy or "now" format
      $start_edited = getDateStr($start_date);
      $end_edited = getDateStr($end_date);

      // in case the user didn't input any project here
      if (!isset($projects)) {
        $projects_edited = "";
      } else {
        $projects_edited = htmlentities($projects);
      }

      array_push($occupations, array(htmlentities($position), htmlentities($organization), $start_edited, $end_edited, $projects_edited));
    }

    // fill in empty values for first 3 occupations in case they don't exist
    // this is so we don't have to include special logic for those first 3 occupations depending whether or not there has
    // been an input occupation
    for ($i = count($occupations); $i < 3; $i++) {
      array_push($occupations, array("", "", "", "", ""));
    }

    return $occupations;
  }

 ?>
