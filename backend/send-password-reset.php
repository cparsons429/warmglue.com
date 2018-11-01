<?php
  require 'db.php';
  require 'helper-functions.php';

  // save the email the user attempted for use on multiple pages / in case submission fails
  // make sure to escape string to prevent sql injections
  $_SESSION['email_attempt'] = htmlentities($_POST['email']);
  
  // need to make email send script
 ?>
