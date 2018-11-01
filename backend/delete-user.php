<?php
  require 'db.php';
  require 'helper-functions.php';

  // preventing CSRF attacks
  if (!hash_equals($_SESSION['token'], $_POST['token'])) {
    die("Request forgery detected");
  }

  // clear our error message so that we can display the correct errors to the user
  $_SESSION['message'] = null;

  // make sure email matches regex to prevent sql injections
  if (!isEmail($_POST['email'])) {
    // let user know that this isn't an email address and exit
    addToMessage("\"" + $_POST['email'] + "\" doesn't look like an email address.");
    exit();
  }

  // we search to make sure this is the same email as the user's email
  $stmt = $mysqli->prepare("SELECT email FROM user_emails WHERE user_id=?");
  $stmt->bind_param('i', $_SESSION['user_id']);
  $stmt->execute();
  $stmt->bind_result($e_pull);

  while ($stmt->fetch()) {
    if ($e_pull === $_POST['email']) {
      // the user has input an email that belongs to their account, so they are confirmed to cancel their account

      // delete user from users list
      $stmt = $mysqli->prepare("DELETE FROM users WHERE id=?");
      $stmt->bind_param("i", $_SESSION['user_id']);
      $stmt->execute();

      // delete user emails
      $stmt = $mysqli->prepare("DELETE FROM user_emails WHERE user_id=?");
      $stmt->bind_param("i", $_SESSION['user_id']);
      $stmt->execute();

      // delete user occupations
      $stmt = $mysqli->prepare("DELETE FROM user_occupations WHERE user_id=?");
      $stmt->bind_param("i", $_SESSION['user_id']);
      $stmt->execute();

      // delete user searches
      $stmt = $mysqli->prepare("DELETE FROM searches WHERE user_id=?");
      $stmt->bind_param("i", $_SESSION['user_id']);
      $stmt->execute();

      // get all links that are associated with this user, in order to delete all associated intros and meetings
      // note this also handles the case for connections that are only linked with this user, as any intro or meeting with // these connections *must* be facilitated by a link from this user
      $stmt = $mysqli->prepare("SELECT id FROM links WHERE user_id=? OR other_user_id=?");
      $stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
      $stmt->execute();
      $stmt->bind_result($link_id);

      while($stmt->fetch()) {
        // delete associated intros
        $del_stmt = $mysqli->prepare("DELETE FROM intros WHERE link0_id=? OR link1_id=?");
        $del_stmt->bind_param("ii", $link_id, $link_id);
        $del_stmt->execute();

        // delete associated meetings
        $del_stmt = $mysqli->prepare("DELETE FROM meetings WHERE link_id=?");
        $del_stmt->bind_param("i", $link_id);
        $del_stmt->execute();
      }

      // find which connections are linked with this user
      $stmt = $mysqli->prepare("SELECT other_connection_id FROM links WHERE user_id=?");
      $stmt->bind_param("i", $_SESSION['user_id']);
      $stmt->execute();
      $stmt->bind_result($connection_id);

      while ($stmt->fetch()) {
        // if this connection is *only* connected with this user, and not with any other user, delete everything relating to
        // this connection from the db
        $count_stmt = $mysqli->prepare("SELECT COUNT(*) FROM links WHERE other_connection_id=?")
        $count_stmt->bind_param("i", $connection_id);
        $count_stmt->execute();
        $count_stmt->bind_result($count);
        $count_stmt->fetch();

        if ($count == 0) {
          // delete connection from connections list
          $stmt = $mysqli->prepare("DELETE FROM connections WHERE id=?");
          $stmt->bind_param("i", $connection_id);
          $stmt->execute();

          // delete connection emails
          $stmt = $mysqli->prepare("DELETE FROM connection_emails WHERE connection_id=?");
          $stmt->bind_param("i", $connection_id);
          $stmt->execute();

          // delete connection occupations
          $stmt = $mysqli->prepare("DELETE FROM connection_occupations WHERE connection_id=?");
          $stmt->bind_param("i", $connection_id);
          $stmt->execute();
        }
      }

      // delete links this user has, as well as links other users have with this user
      $stmt = $mysqli->prepare("DELETE FROM links WHERE user_id=? OR other_user_id=?");
      $stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
      $stmt->execute();

      header("location: ../accountcancelled");
      exit();
    }
  }

  // let user know that this isn't an email address associated with their account and exit
  addToMessage("\"" + $_POST['email'] + "\" isn't an email address associated with your account.");
  exit();

 ?>
