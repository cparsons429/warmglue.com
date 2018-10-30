<?php
  require 'db.php';
  require 'helper-functions.php';

  // note that we don't need to worry about a session error message here - there's nothing a user can input in to a search // that forces an error, and we're not showing an error message on this page

  // we also don't need to save our searches to a session variable, because no matter what, the form will go through

  // get our searches
  $searches = array();

  for ($i = 0; $i < 5; $i++) {
    $search_name = "search" + strval(intdiv($i, 10)) + strval($i%10);

    // only add a search if it's not empty and not already input
    if (!($_POST[$search_name] === "") && !searchAlreadyTaken($searches, $search)) {
      array_push($searches, $search);
    }
  }

  // find current searches
  $stmt = $mysqli->prepare("SELECT id, search FROM searches WHERE user_id=?");
  $stmt->bind_param('i', $_SESSION['user_id']);
  $stmt->execute();
  $stmt->bind_result($s_id, $search);

  // delete or ignore searches that are already in the db
  while($stmt->fetch()) {
    if (searchAlreadyTaken($searches, $search)) {
      // this search is already included in the user's input, so we'll ignore the user's input as a duplicate when we add
      // new searches
      unset($searches[searchIndex($searches, $search)]);
    } else {
      // this search isn't included in the occupations input by the user, so we need to delete it
      $del_stmt = $mysqli->prepare("DELETE from searches WHERE id=?");
      $del_stmt->bind_param('i', $s_id);
      $del_stmt->execute();
    }
  }

  // add searches that are included in the new input, but aren't included in the db
  foreach ($searches as $input_search) {
    $stmt = $mysqli->prepare("INSERT INTO searches (user_id, search) VALUES (?, ?)");
    $stmt->bind_param('is', $_SESSION['user_id'], $input_search);
    $stmt->execute();
  }

 ?>
