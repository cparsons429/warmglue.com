<?php
  session_start();

  require 'db.php';
  require 'helper-functions.php';

  // preventing CSRF attacks
  if (!hash_equals($_SESSION['token'], $_POST['token'])) {
    die("Request forgery detected");
  }

  // note that we don't need to worry about a session error message here - there's nothing a user can input in to a search // that forces an error, and we're not showing an error message on this page

  // we also don't need to save our searches to a session variable, because no matter what, the form will go through

  // get our searches
  $searches = array();

  for ($i = 0; $i < 5; $i++) {
    $search_name = "search".strval(intdiv($i, 10)).strval($i%10);
    $search = trim($_POST[$search_name]);

    // only add a search if it's not empty and not already input
    if (!($search === "") && !searchAlreadyTaken($searches, $search)) {
      array_push($searches, $search);
    }
  }

  // find current searches
  $stmt = $mysqli->prepare("SELECT id, search FROM searches WHERE user_id=?");
  $stmt->bind_param('i', $_SESSION['user_id']);
  $stmt->execute();
  $stmt->bind_result($s_id_str, $s_current);

  $searches_to_delete = array();

  // delete or ignore searches that are already in the db
  while($stmt->fetch()) {
    $s_id = intval($s_id_str);

    if (searchAlreadyTaken($searches, $s_current)) {
      // this search is already included in the user's input, so we'll ignore the user's input as a duplicate when we add
      // new searches
      unset($searches[searchIndex($searches, $s_current)]);
    } else {
      // this search isn't included in the occupations input by the user, so we need to delete it
      array_push($searches_to_delete, $s_id);
    }
  }

  $stmt->close();

  // deleting non-included searches
  $stmt = $mysqli->prepare("DELETE FROM searches WHERE id=?");

  foreach ($searches_to_delete as $s_to_delete) {
    $stmt->bind_param('i', $s_to_delete);
    $stmt->execute();
  }

  $stmt->close();

  // add searches that are included in the new input, but aren't included in the db
  $stmt = $mysqli->prepare("INSERT INTO searches (user_id, search) VALUES (?, ?)");

  foreach ($searches as $input_search) {
    $stmt->bind_param('is', $_SESSION['user_id'], $input_search);
    $stmt->execute();
  }

  $stmt->close();

  // if they're registering, send them to profile
  // otherwise, send them back home
  if ($_SESSION['registering'] == 1) {
    $_SESSION['registering'] = 0;
    header("location: ../profile");
    exit();
  } else {
    header("location: ../home");
    exit();
  }

 ?>
