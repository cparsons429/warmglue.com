<?php
  session_start();

  // preventing information leakage
  if (!$_SESSION['pull_searches_access_allowed']) {
    header("location: ../accessdenied");
    exit();
  } else {
    $_SESSION['pull_searches_access_allowed'] = 0;
  }

  function getSearches($token) {
    session_start();

    // preventing information leakage
    $_SESSION['db_access_allowed'] = 1;
    require 'db.php';

    // connect, while preventing CSRF attacks
    $mysqli = authenticated_connect($token);

    // find current searches
    $stmt = $mysqli->prepare("SELECT search, searched FROM searches WHERE user_id=? AND is_archived=0 ORDER BY searched DESC");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($search, $_);

    // get non-empty searches
    $searches = array();

    while($stmt->fetch()) {
      array_push($searches, htmlentities($search));
    }

    $stmt->close();

    // fill in empty values for searches that don't exist
    // this is so we don't have to include special logic for those first 5 searches depending whether or not there has been // an input search
    for ($i = count($searches); $i < 5; $i++) {
      array_push($searches, "");
    }

    return $searches;
  }

 ?>
