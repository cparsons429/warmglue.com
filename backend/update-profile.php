<?php
  session_start();

  require 'db.php';
  require 'helper-functions.php';

  // preventing CSRF attacks
  if (!hash_equals($_SESSION['token'], $_POST['token'])) {
    die("Request forgery detected");
  }

  // clear our error message, and let frontend know it was just redirected from the backend
  $_SESSION['message'] = null;
  $_SESSION['backend_redirect'] = 1;

  // save the first and last names the user attempted in case submission fails
  // make sure to escape string to prevent sql injections
  $f_name = trim($_POST['first_name']);
  $l_name = trim($_POST['last_name']);
  $_SESSION['first_name_attempt'] = htmlentities($f_name);
  $_SESSION['last_name_attempt'] = htmlentities($l_name);

  // make sure first name matches regex to prevent sql injections, and make sure the user has input an first name
  if ($f_name === "") {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "You have to provide a first name to proceed.");
    $_SESSION['first_name_attempt'] = null;
  } else if (!isName($f_name)) {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "\"".$f_name."\" seems to have some non-standard characters for a name.");
  } else {
    // we're good to go
  }

  // make sure last name matches regex to prevent sql injections, and make sure the user has input a last name
  if ($l_name === "") {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "You have to provide a last name to proceed.");
    $_SESSION['last_name_attempt'] = null;
  } else if (!isName($l_name)) {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "\"".$l_name."\" seems to have some non-standard characters for a name.");
  } else {
    // we're good to go
  }

  // make sure emails match regex to prevent sql injections
  // for every non-empty email, create a variable that's included in SESSION
  // for every valid email, create a variable that's included in emails
  // if all emails fail, we don't want to show a redundant error message; hence, the extra variable $at_least_one_invalid
  $emails = array();
  $_SESSION['email_attempts'] = array();

  for ($i = 0; $i < 30; $i++) {
    $email_name = "email".strval(intdiv($i, 10)).strval($i%10);

    $e = trim($_POST[$email_name]);

    if ($e === "" || emailAlreadyTaken($emails, $e)) {
      // there's nothing here, or this is a duplicate email
    } else {
      // there's something here
      array_push($_SESSION['email_attempts'], htmlentities($e));

      // to make sure that our email isn't already taken
      $stmt = $mysqli->prepare("SELECT COUNT(*), user_id FROM user_emails WHERE email=?");
      $stmt->bind_param('s', $e);
      $stmt->execute();
      $stmt->bind_result($count_str, $u_id_str);
      $stmt->fetch();
      $stmt->close();

      $count = intval($count_str);
      $u_id = intval($u_id_str);

      if (!isEmail($e)) {
        // this is not a valid email
        $_SESSION['message'] = updateMessage($_SESSION['message'], "\"".$e."\" doesn't look like an email address.");
      } else if ($count == 1 && !($u_id == $_SESSION['user_id'])) {
        // this email is already taken by another account
        $_SESSION['message'] = updateMessage($_SESSION['message'], "\"".$e."\" is already registered with another account.");
      } else {
        // the thing here is valid
        array_push($emails, $e);
      }
    }
  }

  if (count($_SESSION['email_attempts']) == 0) {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "You have to provide at least one valid email address to proceed.");
    $_SESSION['email_attempts'] = null;
  } else if (count($_SESSION['email_attempts']) < 4) {
    for ($i = count($_SESSION['email_attempts']); $i < 4; $i++) {
      array_push($_SESSION['email_attempts'], "");
    }
  } else {
    // we have sufficient emails, and we don't need to input blank entries on those extra spots
  }

  // make sure each occupation has the first 4 entries completed
  // then, make sure that the dates make sense for each occupation
  $occupations = array();
  $_SESSION['occupation_attempts'] = array();

  for ($i = 0; $i < 100; $i++) {
    $iteration_name = strval(intdiv($i, 10)).strval($i%10);

    $position_name = "position".$iteration_name;
    $organization_name = "organization".$iteration_name;
    $start_name = "startdate".$iteration_name;
    $end_name = "enddate".$iteration_name;
    $projects_name = "projects".$iteration_name;

    $o = array(trim($_POST[$position_name]), trim($_POST[$organization_name]), trim($_POST[$start_name]), trim($_POST[$end_name]), trim($_POST[$projects_name]));
    $sanitized_occupation = array();

    foreach ($o as $value) {
      array_push($sanitized_occupation, htmlentities($value));
    }

    // now we check to see if this is a valid occupation
    if (emptyOccupation($o) || occupationAlreadyTaken($occupations, $o)) {
      // there's nothing here, or this is a duplicate occupation
    } else {
      // there's something here
      array_push($_SESSION['occupation_attempts'], $sanitized_occupation);

      if ($o[0] === "" || $o[1] === "" || $o[2] === "") {
        // they must have filled out at least 1 entry, but not filled out at least 1 of the 3 necessary entries

        if (!$o[0] === "") {
          $_SESSION['message'] = updateMessage($_SESSION['message'], "Your occupation \"".$o[0]."\" requires at minimum a position, an organization, and a valid start date.");
        } else if (!$o[1] === "") {
          $_SESSION['message'] = updateMessage($_SESSION['message'], "Your occupation at \"".$o[1]."\" requires at minimum a position, an organization, and a valid start date.");
        } else if (!$o[2] === "") {
          $_SESSION['message'] = updateMessage($_SESSION['message'], "Your occupation starting on \"".$o[2]."\" requires at minimum a position, an organization, and a valid start date.");
        } else if (!$o[3] === "") {
          $_SESSION['message'] = updateMessage($_SESSION['message'], "Your occupation ending on \"".$o[3]."\" requires at minimum a position, an organization, and a valid start date.");
        } else {
          $_SESSION['message'] = updateMessage($_SESSION['message'], "Your occupation where you worked on \"".$o[4]."\" requires at minimum a position, an organization, and a valid start date.");
        }
      } else if (!isStartDateFormat($o[2]) || !isEndDateFormat($o[3])) {
        // invalid format for one or both of the dates

        if (isEndDateFormat($o[3])) {
          $_SESSION['message'] = updateMessage($_SESSION['message'], "Your occupation as a ".$o[0]." at ".$o[1]." doesn't have a valid start date. Valid start dates are of the form mm/dd/yyyy, like \"5/25/2015\".");
        } else if (isStartDateFormat($o[2])) {
          $_SESSION['message'] = updateMessage($_SESSION['message'], "Your occupation as a ".$o[0]." at ".$o[1]." doesn't have a valid end date. Valid end dates are of the form mm/dd/yyyy, like \"6/26/2016\". Leave the end date empty if you still work or study there.");
        } else {
          $_SESSION['message'] = updateMessage($_SESSION['message'], "Your occupation as a ".$o[0]." at ".$o[1]." has invalid start and end dates. Valid start dates are of the form mm/dd/yyyy, like \"5/25/2015\", and valid end dates are either of the form mm/dd/yyyy, or empty.");
        }
      } else if (!isDate($o[2]) || !isDate($o[3])) {
        // invalid value for one or both of the dates

        if (isDate($o[3])) {
          $_SESSION['message'] = updateMessage($_SESSION['message'], "Your occupation as a ".$o[0]." at ".$o[1]." has a start date that doesn't seem like a recent, real date.");
        } else if (isDate($o[2])) {
          $_SESSION['message'] = updateMessage($_SESSION['message'], "Your occupation as a ".$o[0]." at ".$o[1]." has an end date that doesn't seem like a recent, real date.");
        } else {
          $_SESSION['message'] = updateMessage($_SESSION['message'], "Your occupation as a ".$o[0]." at ".$o[1]." has start and end dates that don't seem like recent, real dates.");
        }
      } else if (!datesInOrder($o[2], $o[3])){
        // the start date is happening after the end date

        $_SESSION['message'] = updateMessage($_SESSION['message'], "Your occupation as a ".$o[0]." at ".$o[1]." has a start date that occurs later than its end date.");
      } else {
        // the thing here is valid

        array_push($occupations, $o);
      }
    }
  }

  if (count($_SESSION['occupation_attempts']) == 0) {
    $_SESSION['message'] = updateMessage($_SESSION['message'], "You have to provide at least one valid occupation to proceed.");
    $_SESSION['occupation_attempts'] = null;
  } else if (count($_SESSION['occupation_attempts']) < 3) {
    for ($i = count($_SESSION['occupation_attempts']); $i < 3; $i++) {
      array_push($_SESSION['occupation_attempts'], array("", "", "", "", ""));
    }
  } else {
    // we have sufficient occupations, and we don't need to input blank entries on those extra spots
  }

  if (!isset($_SESSION['message'])) {
    // there were no errors, and we're good to go
    // update our user entry
    $stmt = $mysqli->prepare("UPDATE users SET first_name=?, last_name=? WHERE id=?");
    $stmt->bind_param('ssi', $f_name, $l_name, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    // find current emails
    $stmt = $mysqli->prepare("SELECT id, email, is_primary FROM user_emails WHERE user_id=?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($e_id_str, $e_pull, $primary_str);

    $primary_email = $emails[0];

    $emails_to_delete = array();
    $emails_to_switch_primary = array();

    // delete or ignore emails that are already in the db
    while ($stmt->fetch()) {
      $e_id = intval($e_id_str);
      $primary = intval($primary_str);

      if (emailAlreadyTaken($emails, $e_pull)) {
        // this email is included in the user's input, so we'll ignore the user's input as a duplicate when we add new emails
        $email_index = emailIndex($emails, $e_pull);

        // if this email is set as primary when it shouldn't be, or vice versa, reset it correctly
        if ($primary == !($emails[$email_index] === $primary_email)) {
          array_push($emails_to_switch_primary, array(!$primary, $e_id));
        }

        // now ignore it
        unset($emails[$email_index]);
      } else {
        // this email isn't included in the emails input by the user, so we need to delete it
        array_push($emails_to_delete, $e_id);
      }
    }

    array_values($emails);

    $stmt->close();

    // resetting primary emails
    $stmt = $mysqli->prepare("UPDATE user_emails SET is_primary=? WHERE id=?");

    foreach ($emails_to_switch_primary as $e_to_switch) {
      $stmt->bind_param('ii', $e_to_switch[0], $e_to_switch[1]);
      $stmt->execute();
    }

    $stmt->close();

    // deleting non-included emails
    $stmt = $mysqli->prepare("DELETE FROM user_emails WHERE id=?");

    foreach ($emails_to_delete as $e_to_delete) {
      $stmt->bind_param('i', $e_to_delete);
      $stmt->execute();
    }

    $stmt->close();

    // add emails that are included in the new input, but aren't included in the db
    $stmt = $mysqli->prepare("INSERT INTO user_emails (user_id, email, is_primary) VALUES (?, ?, ?)");

    foreach ($emails as $input_email) {
      $stmt->bind_param('isi', $_SESSION['user_id'], $input_email, intval($input_email === $primary_email));
      $stmt->execute();
    }

    $stmt->close();

    // find current occupations
    $stmt = $mysqli->prepare("SELECT id, position, organization, start_date, end_date, projects FROM user_occupations WHERE user_id=?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($o_id, $position_pull, $organization_pull, $start_date_pull, $end_date_pull, $projects_pull);

    $occupations_to_delete = array();

    // delete or ignore occupations that are already in the db
    while ($stmt->fetch()) {
      $o_id = intval($o_id);
      $o_pull = array($position_pull, $organization_pull, $start_date_pull, $end_date_pull, $projects_pull);

      if (occupationAlreadyTaken($occupations, $o_pull)) {
        // this occupation is already included in the user's input, so we'll ignore the user's input as a duplicate when we // add new occupations
        unset($occupations[occupationIndex($occupations, $o_pull)]);
      }
      else {
        // this occupation isn't included in the occupations input by the user, so we need to delete it
        array_push($occupations_to_delete, $o_id);
      }
    }

    $stmt->close();

    // deleting non-included occupations
    $stmt = $mysqli->prepare("DELETE FROM user_occupations WHERE id=?");

    foreach ($occupations_to_delete as $o_to_delete) {
      $stmt->bind_param('i', $o_to_delete);
      $stmt->execute();
    }

    $stmt->close();

    // add occupations that are included in the new input, but aren't included in the db
    foreach ($occupations as $input_occupation) {
      // convert all date strings to standardized format for insert into mysql
      $start = getMonthDayYear($input_occupation[2]);
      $end = getMonthDayYear($input_occupation[3]);

      $start_str = $start[0].",".$start[1].",".$start[2];

      echo sprintf("%s", $start_str);

      if (isset($end)) {
        // we need to handle if this occupation is still a place where the user works (denoted by getMonthDayYear returning
        // null to indicate that the user input the current date)
        $end_str = $end[0].",".$end[1].",".$end[2];

        if ($input_occupation[4] === "") {
          // in case the last entry is empty, leave it at null when we insert our new value into sql
          $stmt = $mysqli->prepare("INSERT INTO user_occupations (user_id, position, organization, start_date, end_date) VALUES (?, ?, ?, STR_TO_DATE(?, '%c,%e,%Y'), STR_TO_DATE(?, '%c,%e,%Y'))");
          $stmt->bind_param('issss', $_SESSION['user_id'], $input_occupation[0], $input_occupation[1], $start_str, $end_str);
          $stmt->execute();
          $stmt->close();
        } else {
          // if the last entry isn't empty, then proceed with the value of projects
          $stmt = $mysqli->prepare("INSERT INTO user_occupations (user_id, position, organization, start_date, end_date, projects) VALUES (?, ?, ?, STR_TO_DATE(?, '%c,%e,%Y'), STR_TO_DATE(?, '%c,%e,%Y'), ?)");
          $stmt->bind_param('isssss', $_SESSION['user_id'], $input_occupation[0], $input_occupation[1], $start_str, $end_str, $input_occupation[4]);
          $stmt->execute();
          $stmt->close();
        }
      } else {
        // the user just input a current occupation

        if ($input_occupation[4] === "") {
          // in case the last entry is empty, leave it at null when we insert our new value into sql
          $stmt = $mysqli->prepare("INSERT INTO user_occupations (user_id, position, organization, start_date) VALUES (?, ?, ?, STR_TO_DATE(?, '%c,%e,%Y'))");
          $stmt->bind_param('isss', $_SESSION['user_id'], $input_occupation[0], $input_occupation[1], $start_str);
          $stmt->execute();
          $stmt->close();
        } else {
          // if the last entry isn't empty, then proceed with the value of projects
          $stmt = $mysqli->prepare("INSERT INTO user_occupations (user_id, position, organization, start_date, projects) VALUES (?, ?, ?, STR_TO_DATE(?, '%c,%e,%Y'), ?)");
          $stmt->bind_param('issss', $_SESSION['user_id'], $input_occupation[0], $input_occupation[1], $start_str, $input_occupation[4]);
          $stmt->execute();
          $stmt->close();
        }
      }
    }

    // delete the session variables for these entries
    $_SESSION['first_name_attempt'] = null;
    $_SESSION['last_name_attempt'] = null;
    $_SESSION['email_attempts'] = null;
    $_SESSION['occupation_attempts'] = null;

    // if they're registering, send them to search
    // otherwise, send them back home
    if ($_SESSION['registering'] == 1) {
      $_SESSION['registering'] = 0;
      header("location: ../search");
      exit();
    } else {
      header("location: ../home");
      exit();
    }
  } else {
    // the user didn't complete the form correctly
    header("location: ../profile");
    exit();
  }
?>
