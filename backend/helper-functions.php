<?php
  $days_in_each_month = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

  function addToMessage($str) {
    if ($_SESSION['message'] == "") {
      $_SESSION['message'] = "\n\n" + $str;
    } else {
      $_SESSION['message'] += "\n" + $str;
    }
  }

  function isEmail($str) {
    return preg_match("^[\w!#$%&'\.*+/=?^_`{|}~-]+@[\w\-]+(\.[\w\-]+)+$", $str);
  }

  function isName($str) {
    return preg_match("/^[a-zA-Z\s,.'-\pL]+$/u", $str);
  }

  function isDate($str) {
    if (preg_match("^\d{1,2}/\d{1,2}/\d{4}", $str)) {
      // substring is weird for php; the second parameter is the length of the string, not the index to finish at
      // strrpos is just strpos for last intead of first occurrence
      $month = intval(substr($str, 0, strpos("/")), 10);
      $day = intval(substr($str, strpos("/") + 1, strrpos("/") - (strpos("/") + 1)), 10);
      $year = intval(substr($str, strrpos("/") + 1), 10);

      if ($month > 12 || $month < 1) {
        return false;
      }
      else {
        // we need to make sure this is a valid day for its month
        if ($month == 1) {

        }
      }
    }
    else if (preg_match("^\d{1,2}-\d{1,2}-\d{4}", $str)) {

    }
  }
 ?>
