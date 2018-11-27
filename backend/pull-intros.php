<?php
  session_start();

  // preventing information leakage
  if (!$_SESSION['pull_intros_access_allowed']) {
    header("location: ../accessdenied");
    exit();
  } else {
    $_SESSION['pull_intros_access_allowed'] = 0;
  }

  function getIntros($token) {
    session_start();

    // preventing information leakage
    $_SESSION['db_access_allowed'] = 1;
    require 'db.php';
    $_SESSION['helper_functions_access_allowed'] = 1;
    require 'helper-functions.php';

    // connect, while preventing CSRF attacks
    $mysqli = authenticated_connect($token);

    // find current intros
    $stmt = $mysqli->prepare("SELECT id, link0_id, link1_id, suggested, provided_rating, rating_reason FROM intros WHERE user_id=? ORDER BY suggested DESC");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();

    $stmt->bind_result($intro_id_str, $link0_id_str, $link1_id_str, $suggested_str, $provided_rating_str, $rating_reason);

    $intros_initial = array();

    while ($stmt->fetch()) {
      $intro_id = intval($intro_id_str);
      $link0_id = intval($link0_id_str);
      $link1_id = intval($link1_id_str);
      $suggested = getDateStr($suggested_str);
      $provided_rating = strval($provided_rating_str);

      array_push($intros_initial, array($intro_id, $link0_id, $link1_id, $suggested, $provided_rating, $rating_reason));
    }

    $stmt->close();

    // we now use our two links to find the name of the person with whom the user was introed

    $intros = array();

    foreach ($intros_initial as $intro_initial) {
      $stmt = $mysqli->prepare("SELECT user_id, other_user_id, other_connection_id FROM links WHERE id=? OR id=?");
      $stmt->bind_param('ii', $intro_initial[1], $intro_initial[2]);
      $stmt->execute();
      $stmt->bind_result($u_id_str, $o_u_id_str, $o_c_id_str);
      $stmt->fetch();

      $u_ids = array();

      if (isset($o_c_id_str)) {
        // we can only have a connection included if that connection is the person with whom the user was introed
        $c_id = intval($o_c_id_str);
      } else {
        array_push($u_ids, intval($u_id_str), intval($o_u_id_str));

        $stmt->fetch();

        if (isset($o_c_id_str)) {
          // we can only have a connection included if that connection is the person with whom the user was introed
          $c_id = intval($o_c_id_str);
        } else {
          array_push($u_ids, intval($u_id_str), intval($o_u_id_str));
        }
      }

      $stmt->close();

      if (isset($c_id)) {
        // the connection must be the person with whom the user was introed, so include their name
        $stmt = $mysqli->prepare("SELECT first_name, last_name FROM connections WHERE id=?");
        $stmt->bind_param('i', $c_id);
      } else {
        // the user's id will be one of the four id's
        // the connector will be two of the four id's
        // the person with whom the user was introed must be the remaining id
        $stmt = $mysqli->prepare("SELECT first_name, last_name FROM users WHERE id=?");

        unset($u_ids[array_search($_SESSION['user_id'], $u_ids)]);

        if ($u_ids[1] == $u_ids[2]) {
          $stmt->bind_param('i', $u_ids[0]);
        } else if ($u_ids[0] == $u_ids[2]) {
          $stmt->bind_param('i', $u_ids[1]);
        } else {
          $stmt->bind_param('i', $u_ids[2]);
        }
      }

      $stmt->execute();
      $stmt->bind_result($f_name, $l_name);
      $stmt->fetch();
      $stmt->close();

      // format of returned intros: id, first name, last name, suggested date, rating, reason
      array_push($intros, array($intro_initial[0], $f_name, $l_name, $intro_initial[3], $intro_initial[4], $intros_initial[5]));
    }

    return $intros;
  }
 ?>
