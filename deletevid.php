<?php
  // start session and check session for user id
  session_start();
  if(!isset($_SESSION['userid'])) {
    header("Location:login.php");
  }

  include 'includes/library.php';
  $mid = $_POST['id']; // get the movie id

  // Connect to database and delete movie
  $pdo = & dbconnect();
  $sql = "delete from Movies where mID = ?";
  $pdo->prepare($sql)->execute([$mid]);

?>
