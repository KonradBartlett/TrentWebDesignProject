<?php
  // start the session
  session_start();

  // destroy the session
  session_destroy();

  // delete any remaining cookies
  foreach($_COOKIE as $cookie) {
    setcookie($cookie, "", 1);
  }

  // redirect to login page
  header("Location:login.php");
  exit();
?>
