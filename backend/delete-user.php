<?php
  function delete_account($token) {
    session_start();

    require 'db.php';
    require 'helper-functions.php';

    // preventing CSRF attacks
    if (!hash_equals($_SESSION['token'], $token)) {
      die("Request forgery detected");
    }

    // delete user from users list
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    // delete user emails
    $stmt = $mysqli->prepare("DELETE FROM user_emails WHERE user_id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    // delete user occupations
    $stmt = $mysqli->prepare("DELETE FROM user_occupations WHERE user_id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    // delete user searches
    $stmt = $mysqli->prepare("DELETE FROM searches WHERE user_id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    // get all links that are associated with this user, in order to delete all associated intros and meetings
    // note this also handles the case for connections that are only linked with this user, as any intro or meeting with
    // these connections *must* be facilitated by a link from this user
    $stmt = $mysqli->prepare("SELECT id FROM links WHERE user_id=? OR other_user_id=?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($link_id_str);

    $links_to_delete = array();

    while($stmt->fetch()) {
      $link_id = intval($link_id_str);
      array_push($links_to_delete, $link_id);
    }

    $stmt->close();

    // delete associated intros
    $stmt = $mysqli->prepare("DELETE FROM intros WHERE link0_id=? OR link1_id=?");

    foreach ($links_to_delete as $l_to_delete) {
      $stmt->bind_param("ii", $l_to_delete, $l_to_delete);
      $stmt->execute();
    }

    $stmt->close();

    // delete associated meetings
    $stmt = $mysqli->prepare("DELETE FROM meetings WHERE link_id=?");

    foreach ($links_to_delete as $l_to_delete) {
      $stmt->bind_param("i", $l_to_delete);
      $stmt->execute();
    }

    $stmt->close();

    // find which connections are linked with this user
    $stmt = $mysqli->prepare("SELECT other_connection_id FROM links WHERE user_id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($connection_id_str);

    $connection_ids = array();

    while ($stmt->fetch()) {
      $connection_id = intval($connection_id_str);
      array_push($connection_ids, $connection_id);
    }

    $stmt->close();

    // if this connection is *only* connected with this user, and not with any other user, delete everything relating to
    // this connection from the db
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM links WHERE other_connection_id=?");

    $connections_to_delete = array();

    foreach ($connection_ids as $connection_id) {
      $stmt->bind_param("i", $connection_id);
      $stmt->execute();
      $stmt->bind_result($count_str);
      $stmt->fetch();

      $count = intval($count_str);

      if ($count == 1) {
        // this connection is only linked with one user (the deleting user), so delete them
        array_push($connections_to_delete, $connection_id);
      }
    }

    $stmt->close();

    foreach ($connections_to_delete as $c_to_delete) {
      // delete connection from connections list
      $stmt = $mysqli->prepare("DELETE FROM connections WHERE id=?");
      $stmt->bind_param("i", $c_to_delete);
      $stmt->execute();
      $stmt->close();

      // delete connection emails
      $stmt = $mysqli->prepare("DELETE FROM connection_emails WHERE connection_id=?");
      $stmt->bind_param("i", $c_to_delete);
      $stmt->execute();
      $stmt->close();

      // delete connection occupations
      $stmt = $mysqli->prepare("DELETE FROM connection_occupations WHERE connection_id=?");
      $stmt->bind_param("i", $c_to_delete);
      $stmt->execute();
      $stmt->close();
    }

    // finally, delete links this user has, as well as links other users have with this user
    $stmt = $mysqli->prepare("DELETE FROM links WHERE user_id=? OR other_user_id=?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    // log the user out for the final time
    session_unset();
    session_destroy();
  }

 ?>
