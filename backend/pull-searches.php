<?php
  require 'db.php';

  function getSearches() {
    // find current searches
    $stmt = $mysqli->prepare("SELECT search, updated FROM searches WHERE user_id=? ORDER BY MAX(updated) DESC");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($search, $_);

    // get non-empty searches
    $searches = array();

    while($stmt->fetch()) {
      array_push($searches, htmlentities($search));
    }

    // fill in empty values for searches that don't exist
    // this is so we don't have to include special logic for those first 5 searches depending whether or not there has been // an input search
    for ($i = count($searches); $i < 5; $i++) {
      array_push($searches, "");
    }

    return $searches;
  }

 ?>
