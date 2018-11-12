<?php
  session_start();

  // preventing information leakage
  if (!$_SESSION['helper_functions_access_allowed']) {
    header("location: ../accessdenied");
    exit();
  } else {
    $_SESSION['helper_functions_access_allowed'] = 0;
  }

  function updateMessage($original, $str) {
    // handles new line issues depending on whether this is the only, or one of multiple, messages
    if (!isset($original)) {
      return htmlentities($str);
    } else {
      return $original."\n".htmlentities($str);
    }
  }

  function isStartDateFormat($str) {
    // let us know whether it's a valid start date format
    // possibilities: w/ slash or dash, w/ 2 or 4 digits for the year
    return preg_match("/^\d{1,2}\/\d{1,2}\/\d{2}+$/", $str) || preg_match("/^\d{1,2}\/\d{1,2}\/\d{4}+$/", $str) || preg_match("/^\d{1,2}-\d{1,2}-\d{2}+$/", $str) || preg_match("/^\d{1,2}-\d{1,2}-\d{4}+$/", $str);
  }

  function isEndDateFormat($str) {
    // let us know whether it's a valid end date format
    // possibilities are same as start date, plus empty string indicating occupation is current
     return isStartDateFormat($str) || ($str === "");
  }

  function getMonthDayYear($str) {
    // assuming the string has been validated to match the corresponding regex, return the month, date, and year
    if (preg_match("/^\d{1,2}\/\d{1,2}\/\d{2,4}+$/", $str)) {
      // substring is weird for php; the second parameter is the length of the string, not the index to finish at
      // strrpos is just strpos for last intead of first occurrence
      $slash0 = strpos($str, "/");
      $slash1 = strrpos($str, "/");

      $month = intval(substr($str, 0, $slash0), 10);
      $day = intval(substr($str, $slash0 + 1, $slash1 - ($slash0 + 1)), 10);
      $year = intval(substr($str, $slash1 + 1), 10);
    } else if (preg_match("/^\d{1,2}-\d{1,2}-\d{2,4}+$/", $str)) {
      // same as before, just with dashes instead of slashes separating the input
      $dash0 = strpos($str, "-");
      $dash1 = strrpos($str, "-");

      $month = intval(substr($str, 0, $dash0), 10);
      $day = intval(substr($str, $dash0 + 1, $dash1 - ($dash0 + 1)), 10);
      $year = intval(substr($str, $dash1 + 1), 10);
    }
    else {
      // the user must want the current date
      return null;
    }

    if ($year < 100) {
      // convert 2-digit year to 4-digit year
      if ($year <= intval(date("y")) + 20) {
        // we add in the buffer of 20 because we're pretty certain the user is talking about a date 20 years in the future // rather than 80 years in the past
        $year += intval(date("Y")) - intval(date("y"));
      } else {
        $year += intval(date("Y")) - intval(date("y")) - 100;
      }
    }

    return array($month, $day, $year);
  }

  function areDatesCorrectlyOrdered($b_month, $b_day, $b_year, $a_month, $a_day, $a_year) {
    // make sure b doesn't happen after a
    if ($a_year < $b_year) {
      return 0;
    } else if ($a_year > $b_year) {
      return 1;
    } else {
      if ($a_month < $b_month) {
        return 0;
      } else if ($a_month > $b_month) {
        return 1;
      } else {
        if ($a_day < $b_day) {
          return 0;
        } else {
          return 1;
        }
      }
    }
  }

  function isDate($str) {
    // assuming the string has been validated to match the corresponding regex, we want to make sure it's a valid date
    // get our month, date, and year
    $date_info = getMonthDayYear($str);

    if (!isset($date_info)) {
      // user input the current date
      return 1;
    }

    $month = $date_info[0];
    $day = $date_info[1];
    $year = $date_info[2];

    // make sure our year makes sense; date can't be earlier than 1/1/1900
    if ($year < 1900) {
      return 0;
    }

    // make sure our month makes sense
    if ($month < 1 or $month > 12) {
      return 0;
    }

    // see if this is a leap year
    if (($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0)) {
      $days_in_each_month = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    } else {
      $days_in_each_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    }

    // make sure our day makes sense
    if ($day < 1 || $days_in_each_month[$month - 1] < $day) {
      return 0;
    }

    // make sure our date isn't later than today
    $now_date_info = getMonthDayYear(date("n/j/Y"));
    $now_month = $now_date_info[0];
    $now_day = $now_date_info[1];
    $now_year = $now_date_info[2];

    return areDatesCorrectlyOrdered($month, $day, $year, $now_month, $now_day, $now_year);
  }

  function datesInOrder($before, $after) {
    // make sure "before" doesn't happen after "after"
    $before_date_info = getMonthDayYear($before);
    $before_month = $before_date_info[0];
    $before_day = $before_date_info[1];
    $before_year = $before_date_info[2];

    $after_date_info = getMonthDayYear($after);

    if (!isset($after_date_info)) {
      // user input the current date
      return 1;
    }

    $after_month = $after_date_info[0];
    $after_day = $after_date_info[1];
    $after_year = $after_date_info[2];

    // make sure "before" doesn't happen after "after"
    return areDatesCorrectlyOrdered($before_month, $before_day, $before_year, $after_month, $after_day, $after_year);
  }

  function getDateStr($date) {
    // after pulling $date from the sql db, convert it to a human-readable string in the mm/dd/yyyy or "now" format
    if (!isset($date)) {
      // in case this is a current date
      return "";
    }

    $year = substr($date, 0, 4);
    $month = substr($date, 5, 2);
    $day = substr($date, 8);

    return $month."/".$day."/".$year;
  }

  function isEmail($str) {
    // return whether the email matches the appropriate regex
    return preg_match("/^[\w!#$%&'\.*+\/=?^_`{|}~-]+@[\w\-]+(\.[\w\-]+)+$/", $str);
  }

  function isName($str) {
    // return whether the name matches the appropriate regex
    return preg_match("/^[a-zA-Z\s,.'-\pL]+$/", $str);
  }

  function emailIndex($arr, $email) {
    // return index of the email in the given array, or -1 if it's not in the array
    foreach ($arr as $key => $value) {
      if ($email === $value) {
        return $key;
      }
    }

    return -1;
  }

  function emailAlreadyTaken($arr, $email) {
    // return whether the email is already contained in the given array
    return !(emailIndex($arr, $email) == -1);
  }

  function emptyOccupation($occupation) {
    // return whether the occupation is empty
    return $occupation[0] === "" && $occupation[1] === "" && $occupation[2] === "" && $occupation[3] === "" && $occupation[4] === "";
  }

  function occupationIndex($arr, $occupation) {
    // return index of the occupation in the given array, or -1 if it's not in the array
    foreach ($arr as $key => $value) {
      $duplicate = 1;

      for ($i = 0; $i < 5; $i++) {
        if (!($occupation[$i] === $value[$i])) {
          $duplicate = 0;
          break;
        }
      }

      if ($duplicate == 1) {
        return $key;
      }
    }

    return -1;
  }

  function occupationAlreadyTaken($arr, $occupation) {
    // return whether the occupation is already contained in the given array
    return !(occupationIndex($arr, $occupation) == -1);
  }

  function searchIndex($arr, $search) {
    // functionally identical to emailIndex
    return emailIndex($arr, $search);
  }

  function searchAlreadyTaken($arr, $search) {
    // functionally identical to emailAlreadyTaken
    return emailAlreadyTaken($arr, $search);
  }

  function passwordComplexEnough($pwd) {
    // return whether the password is complex enough
    // right now, our only requirement is that it's at least 8 characters long
    return (strlen($pwd) >= 8);
  }

  function nullToEmpty($str) {
    // turning null to empty string
    if (!(isset($str))) {
      return "";
    }

    return $str;
  }
 ?>
