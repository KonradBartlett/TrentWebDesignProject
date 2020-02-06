<?php

  include 'includes/library.php';
  $pdo =  & dbconnect();

  $username = $_GET['username'];  // get the entered username

  // search for username in database
  $sql = "select 1 from Users where username = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$username]);

  // return true if found, else false
  if($stmt->fetchColumn()) {
    echo true;
  } else {
    echo false;
  }

?>
