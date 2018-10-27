<?php
  require 'db.php';
  require 'helper-functions.php';

  // clear our error message so that we can display the correct errors to the user
  $_SESSION['message'] = "";

  // save the first and last names the user attempted in case submission fails
  // make sure to escape string to prevent sql injections
  $_SESSION['first_name_attempt'] = htmlentities($_POST['first_name']);
  $_SESSION['last_name_attempt'] = htmlentities($_POST['last_name']);

  // make sure names match regex to prevent sql injections
  if (!isName($_POST['first_name'])) {
    addToMessage("\"" + $_SESSION['first_name_attempt'] + "\" seems to have some non-standard characters for a name.");
  }

  if (!isName($_POST['last_name'])) {
    addToMessage("\"" + $_SESSION['last_name_attempt'] + "\" seems to have some non-standard characters for a name.");
  }

  // make sure emails match regex to prevent sql injections
  // for every non-empty email, create a variable that's included in SESSION
  // for every valid email, create a variable that's included in emails
  // if all emails fail, we don't want to show a redundant error message; hence, the extra variable $at_least_one_invalid
  $emails = array();
  $_SESSION['email_attempts'] = array();
  $at_least_one_invalid = 0;

  for ($i = 0; $i < 30; i++) {
    $email_name = "email" + strval(intdiv($i, 10)) + strval($i%10);
    $sanitized_email = htmlentities($_POST[$email_name]);

    if ($sanitized_email === "" || emailAlreadyTaken($_SESSION['email_attempts'], $sanitized_email)) {
      // there's nothing here, or this is a duplicate email
    } else {
      // there's something here
      array_push($_SESSION['email_attempts'], $sanitized_email);

      // to make sure that our email isn't already taken
      $stmt = $mysqli->prepare("SELECT COUNT(*), user_id FROM user_emails WHERE email=?");
      $stmt->bind_param('s', $_POST['email']);
      $stmt->execute();
      $stmt->bind_result($count, $u_id);
      $stmt->fetch();

      if (!isEmail($_POST[$email_name]) {
        // the thing here is not valid
        addToMessage("\"" + $sanitized_email + "\" doesn't look like an email address.");
        $at_least_one_invalid = 1;
      } else if ($count == 1 && !($u_id == $_SESSION['user_id'])) {
        // this email is already taken by another account
        addToMessage("\"" + $sanitized_email + "\" is already registered with another account.");
        $at_least_one_invalid = 1;
      } else {
        // the thing here is valid
        array_push($emails, $_POST[$email_name]);
      }
    }
  }

  if (count($_SESSION['email_attempts']) == 0 && $at_least_one_invalid == 0) {
    addToMessage("You have to provide at least one valid email address to proceed.");
  }

  // make sure each occupation has the first 4 entries completed
  // then, make sure that the dates make sense for each occupation
  $occupations = array();
  $_SESSION['occupation_attempts'] = array();
  $at_least_one_invalid = 0;

  for ($i = 0; $i < 100; $i++) {
    $iteration_name = strval(intdiv($i, 10)) + strval($i%10);

    $position_name = "position" + $iteration_name;
    $organization_name = "organization" + $iteration_name;
    $start_name = "startdate" + $iteration_name;
    $end_name = "enddate" + $iteration_name;
    $projects_name = "projects" + $iteration_name;

    $sanitized_position = htmlentities($_POST[$position_name]);
    $sanitized_organization = htmlentities($_POST[$organization_name]);
    $sanitized_start = htmlentities($_POST[$start_name]);
    $sanitized_end = htmlentities($_POST[$end_name]);
    $sanitized_projects = htmlentities($_POST[$projects_name]);

    $sanitized_occupation = array($sanitized_position, $sanitized_organization, $sanitized_start, $sanitized_end, $sanitized_projects);

    if (emptyOccupation($sanitized_occupation) || occupationAlreadyTaken($_SESSION['occupation_attempts'], $sanitized_occupation)) {
      // there's nothing here, or this is a duplicate occupation
    } else {
      // there's something here
      array_push($_SESSION['occupation_attempts'], $sanitized_occupation);

      if ($position_value === "" || $organization_value === "" || $start_value === "" || $end_value === "") {
        // they must have filled out at least 1 entry, but not filled out at least 1 of the 4 necessary entries

        if (!$position_value === "") {
          addToMessage("Your occupation \"" + $sanitized_occupation[0] + "\" requires at minimum a position, an organization, and valid start and end dates.");
        } else if (!$organization_value === "") {
          addToMessage("Your occupation at \"" + $sanitized_occupation[1] + "\" requires at minimum a position, an organization, and valid start and end dates.");
        } else if (!$start_value === "") {
          addToMessage("Your occupation starting on \"" + $sanitized_occupation[2] + "\" requires at minimum a position, an organization, and valid start and end dates.");
        } else if (!$end_value === ""){
          addToMessage("Your occupation ending on \"" + $sanitized_occupation[3] + "\" requires at minimum a position, an organization, and valid start and end dates.");
        } else {
          addToMessage("Your occupation where you worked on \"" + $sanitized_occupation[4] +"\" requires at minimum a position, an organization, and valid start and end dates.")
        }

        $at_least_one_invalid = 1;
      } else if (!isStartDateFormat($_POST[$start_name]) || !isEndDateFormat($_POST[$end_name])) {
        // invalid format for one or both of the dates

        if (isEndDateFormat($_POST[$end_name])) {
          addToMessage("Your occupation as a " + $sanitized_occupation[0] + " at " + $sanitized_occupation[1] + " doesn't have a valid start date. Valid start dates are of the form mm/dd/yyyy, like \"5/25/2015\".");
        } else if (isStartDateFormat($_POST[$start_name])) {
          addToMessage("Your occupation as a " + $sanitized_occupation[0] + " at " + $sanitized_occupation[1] + " doesn't have a valid end date. Valid end dates are of the form mm/dd/yyyy, like \"6/26/2016\", or \"now\" if you still work or study there.");
        } else {
          addToMessage("Your occupation as a " + $sanitized_occupation[0] + " at " + $sanitized_occupation[1] + " has invalid start and end dates. Valid start dates are of the form mm/dd/yyyy, like \"5/25/2015\", and valid end dates are of the form mm/dd/yyyy, like \"6/26/2016\", or \"now\" if you still work or study there.");
        }

        $at_least_one_invalid = 1;
      } else if (!isDate($_POST[$start_name]) || !isDate($_POST[$end_name])) {
        // invalid value for one or both of the dates

        if (isDate($_POST[$end_name)) {
          addToMessage("Your occupation as a " + $sanitized_occupation[0] + " at " + $sanitized_occupation[1] + " has a start date that doesn't seem like a recent, real date.");
        } else if (isDate($_POST[$start_name)) {
          addToMessage("Your occupation as a " + $sanitized_occupation[0] + " at " + $sanitized_occupation[1] + " has an end date that doesn't seem like a recent, real date.");
        } else {
          addToMessage("Your occupation as a " + $sanitized_occupation[0] + " at " + $sanitized_occupation[1] + " has a start and an end date that don't seem like recent, real dates.");
        }

        $at_least_one_invalid = 1;
      } else if (!datesCorrectlyOrdered($_POST)){
        // the start date is happening after the end date

        addToMessage("Your occupation as a " + $sanitized_occupation[0] + " at " + $sanitized_occupation[1] + " has a start date that occurs later than its end date.");

        $at_least_one_invalid = 1;
      } else {
        // the thing here is valid
        $escaped_position = escape_string($_POST[$position_name]);
        $escaped_organization = escape_string($_POST[$organization_name]);
        $escaped_projects = escape_string($_POST[$projects_name]);

        array_push($occupations, array($escaped_position, $escaped_organization, $_POST[$start_name], $_POST[$end_name], $escaped_projects));
      }
  }

  if (count($_SESSION['occupation_attempts']) == 0 && $at_least_one_invalid == 0) {
    addToMessage("You have to provide at least one valid occupation to proceed.");
  }

  if ($_SESSION['message'] === "") {
    // there were no errors, and we're good to go
    // update our user entry
    $stmt = $mysqli->prepare("UPDATE users SET (first_name, last_name) VALUES (?, ?) WHERE user_id=?");
    $stmt->bind_param('ssi', $_POST['first_name'], $_POST['last_name'], $_SESSION['user_id']);
    $stmt->execute();

    // find current emails
    $stmt = $mysqli->prepare("SELECT id, email, is_primary FROM user_emails WHERE user_id=?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($e_id, $email, $primary);

    $primary_email == $emails[0];

    // delete or ignore emails that are already in the db
    while ($stmt->fetch()) {
      if (emailAlreadyTaken($emails, $email)) {
        // this email is included in the user's input, so we'll ignore the user's input as a duplicate when we add new emails
        $email_index = emailIndex($emails, $email);

        // if this email is set as primary when it shouldn't be, or vice versa, reset it correctly
        if ($primary === !($emails[$email_index] === $primary_email)) {
          $upd_stmt = $mysqli->prepare("UPDATE user_emails SET (is_primary) VALUES (?) WHERE id=?")
          $upd_stmt = $mysqli->bind_param('ii', !$primary, $e_id);
          $upd_stmt->execute();
        }

        // now ignore it
        unset($emails[$email_index]);
      } else {
        // this email isn't included in the emails input by the user, so we need to delete it
        $del_stmt = $mysqli->prepare("DELETE from user_emails WHERE id=?");
        $del_stmt->bind_param('i', $e_id);
        $del_stmt->execute();
      }
    }

    // add emails that are included in the new input, but aren't included in the db
    foreach ($emails as &$input_email) {
      $stmt = $mysqli->prepare("INSERT INTO user_emails (user_id, email, is_primary) VALUES (?, ?, ?)");
      $stmt->bind_param('isi', $_SESSION['user_id'], $input_email, ($input_email === $primary_email));
      $stmt->execute();
    }

    // find current occupations
    $stmt = $mysqli->prepare("SELECT id, position, organization, start_date, end_date, projects FROM user_occupations where user_id=?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($o_id, $position, $organization, $start_date, $end_date, $projects);

    // delete or ignore occupations that are already in the db
    while ($stmt->fetch()) {
      $occupation = array($position, $organization, $start_date, $end_date, $projects);

      if (occupationAlreadyTaken($occupations, $occupation)) {
        // this occupation is already included in the user's input, so we'll ignore the user's input as a duplicate when we // add new occupations
        unset($occupations[occupationIndex($occupations, $occupation)]);
      }
      else {
        // this occupation isn't included in the occupations input by the user, so we need to delete it
        $del_stmt = $mysqli->prepare("DELETE from user_occupations WHERE id=?");
        $del_stmt->bind_param('i', $o_id);
        $del_stmt->execute();
      }
    }

    // add occupations that are included in the new input, but aren't included in the db
    foreach ($occupations as &$input_occupation) {
      // convert all date strings to standardized format for insert into mysql
      $start = getMonthDayYear($input_occupation[2]);
      $end = getMonthDayYear($input_occupation[3]);

      $start_str = $start[0] + "," + $start[1] + "," + $start[2];
      $end_str = $end[0] + "," + $end[1] + "," $end[2];

      if ($input_occupation[4] === "") {
        // in case the last entry is empty, leave it at null when we insert our new value into sql
        $stmt = $mysqli->prepare("INSERT INTO user_occupations (user_id, position, organization, start_date, end_date) VALUES (?, ?, ?, STR_TO_DATE(?, '%m,%d,%Y'), STR_TO_DATE(?, '%m,%d,%Y'))");
        $stmt->bind_param('issss', $_SESSION['user_id'], $input_occupation[0], $input_occupation[1], $start_str, $end_str);
      } else {
        // if the last entry isn't empty, then proceed with the value of projects
        $stmt = $mysqli->prepare("INSERT INTO user_occupations (user_id, position, organization, start_date, end_date) VALUES (?, ?, ?, STR_TO_DATE(?, '%m,%d,%Y'), STR_TO_DATE(?, '%m,%d,%Y'), ?)");
        $stmt->bind_param('issss', $_SESSION['user_id'], $input_occupation[0], $input_occupation[1], $start_str, $end_str, $input_occupation[4]);
      }

      $stmt->execute();
    }

    // delete the session variables for these entries
    $_SESSION['first_name_attempt'] = "";
    $_SESSION['last_name_attempt'] = "";
    $_SESSION['email_attempts'] = array();
    $_SESSION['occupation_attempts'] = array();
  }

?>
