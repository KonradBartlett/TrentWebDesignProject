<?php
  // start session and check session for user id
  session_start();
  if(!isset($_SESSION['userid'])) {
    header("Location:login.php");
  }

  include 'includes/library.php';

  // set default variable values
  $errors = array();
  $changes = false;

  $userid = $_SESSION['userid'];  // get user id from session
  // connect to database and retrieve user information
  $pdo = & dbconnect();
  $query = "select username, name, email, userpass from Users where uID = ?";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$userid]);
  $info = $stmt->fetch(PDO::FETCH_ASSOC);
  // store info in variables
  $username = $info['username'];
  $name = $info['name'];
  $email = $info['email'];
  $stored_pass = $info['userpass'];

  // if the user submits the form
  if(isset($_POST['submit'])) {

    // check for username errors
    $new_username = $_POST['username'];
    if($new_username == "") {
      $errors[] = "username";
    }
    // check for name errors
    $new_name = $_POST['name'];
    if(strlen($new_name) <= 0 || strpos($new_name, " ")===FALSE) {
      $errors[] = "name";
    }
    // check for valid email
    $new_email = $_POST['email'];
    if(!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "email";
    }
    // check for password errors
    $entered_pass = $_POST['password'];
    $new_pass = $_POST['new-password'];
    $confirm_pass = $_POST['confirm_pass'];
    // make sure passwords match
    if($confirm_pass != $new_pass) {
      $errors[] = "no match";
    }
    // make sure they entered their current password
    if($entered_pass == "") {
      $errors[] = "no password";
    }
    // verify entered password matches database
    if(!password_verify($entered_pass, $stored_pass)) {
      $errors[] = "password";
    }

    // if no errors so far, check some values
    if(sizeof($errors) == 0) {

      // if they entered a different username, check if it already exists
      if($new_username != $username) {
        $sql="select 1 from Users where username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$new_username]);
        if($stmt->fetchColumn()) {
          $errors[] = "username exists";
        }
      }

      // if they entered a different email, check if it already exists
      if($new_email != $email) {
        $sql="select 1 from Users where email = ?";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([$new_email]);
        if($stmt->fetchColumn()) {
          $errors[] = "email exists";
        }
      }
    }

    // if there are still no errors, make the database changes
    if(sizeof($errors) == 0) {
      // if they didn't enter a new password, store the current one again
      if($new_pass == "") {
        $new_pass = $entered_pass;
      }
      $new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
      $sql = "update Users set username=?, name=?, email=?, userpass=? where username=?";
      $stmt=$pdo->prepare($sql);
      $stmt->execute([$new_username, $new_name, $new_email, $new_pass, $username]);
      $changes = true;
    }
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $PAGE_TITLE = "Edit your Account";
  include "includes/head_includes.php"; ?>
</head>

<body>
  <?php include "includes/headernav.php"; ?>
  <main id="account-page">
    <div>
      <h1>Edit your Account</h1>
    </div>
    <form id="create-account" name="create-account" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
      <div>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?php echo $username; ?>" required />
        <?php if(in_array("username exists", $errors)) { echo "<span class=\"error\">Username already exists</span>"; } ?>
      </div>
      <div>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" pattern="[A-Za-z ,.'-]+$" value="<?php echo $name ?>"required />
        <?php if(in_array("name", $errors)) { echo "<span class=\"error\">You must enter your full name</span>"; } ?>
      </div>
      <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo $email ?>" required />
        <?php if(in_array("email", $errors)) { echo "<span class=\"error\">You must enter a valid email address</span>"; } ?>
        <?php if(in_array("email exists", $errors)) { echo "<span class=\"error\">There is already an account with this email</span>"; } ?>
      </div>
      <div>
        <label for="new-password">New Password:</label>
        <input type="password" name="new-password" id="new-password" />
      </div>
      <div>
        <label for="confirm_pass">Confirm Password:</label>
        <input type="password" name="confirm_pass" id="confirm_pass" />
        <?php if(in_array("no match", $errors)) { echo "<span class=\"error\">Passwords do not match</span>"; } ?>
      </div>
      <div>
        <label for="password">Current Password:</label>
        <input type="password" name="password" id="password" required />
        <?php if(in_array("no password", $errors)) { echo "<span class=\"error\">Password is required</span>"; } ?>
        <?php if(in_array("password", $errors)) { echo "<span class=\"error\">Incorrect password</span>"; } ?>
      </div>
      <input type="submit" name="submit" value="Submit Changes" />
    </form>
    <?php if($changes) { echo "<div>Changes saved</div>"; } ?>
  </main>
  <footer>
    <p>&copy; Movie Collector's Inc. 2019</p>
  </footer>
</body>
</html>
