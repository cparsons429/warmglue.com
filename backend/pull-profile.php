<?php
  require 'db.php';

  function getName() {
    // get first and last name
    $stmt = $mysqli->prepare("SELECT first_name, last_name FROM users WHERE id=?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($first, $last);
    $stmt->fetch();

    return array($first, $last);
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
        array_unshift($emails, $email)
      } else {
        // otherwise, make it the last element
        array_push($emails, $email);
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
    $stmt = $mysqli->prepare("SELECT email, is_primary, updated FROM user_emails WHERE user_id=? ORDER BY MAX(updated) DESC");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($email, $primary, $_);

    // get non-empty searches
    $emails = array();

    while($stmt->fetch()) {
      if ($primary) {
        // if it's our primary email, make it the first element
        array_unshift($emails, $email)
      } else {
        // otherwise, make it the last element
        array_push($emails, $email);
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

 ?>
