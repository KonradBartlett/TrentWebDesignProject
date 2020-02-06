<!--
Create an account

This page allows the user to create an account on the website
It performs form validation to prevent multiple users with the same
username or email and ensures they have a strong enough password
-->

<?php
  // create default values for variables
  $errors = array();
  $username = "";
  $name="";
  $email="";

  // When user submits the form
  if(isset($_POST['submit'])) {
    include 'includes/library.php';

    // check for username errors
    $username = $_POST['username'];
    if($username == "") {
      $errors[] = "username";
    }
    // check for name errors
    $name = $_POST['name'];
    if(strlen($name) <= 0 || strpos($name, " ")===FALSE) {
      $errors[] = "name";
    }
    // check for valid email
    $email = $_POST['email'];
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "email";
    }
    // check for password errors
    $userpass = $_POST['password'];
    if($userpass == "") {
      $errors[] = "password";
    }
    // make sure passwords match
    $confirm_pass = $_POST['confirm_pass'];
    if($confirm_pass != $userpass) {
      $errors[] = "no match";
    }

    // if no errors so far, connect to database and check some values
    if(sizeof($errors) == 0) {
      $pdo = & dbconnect();

      // check for existing username
      $sql="select 1 from Users where username = ?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$username]);
      if($stmt->fetchColumn()) {
        $errors[] = "username exists";
      }

      // check for existing email
      $sql="select 1 from Users where email = ?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$email]);
      if($stmt->fetchColumn()) {
        $errors[] = "email exists";
      }
    }

    // if there are still no errors, add database record
    if(sizeof($errors) == 0) {

      // hash password
      $hash = password_hash($userpass, PASSWORD_DEFAULT);
      // add record
      $sql="insert into Users (username, name, email, userpass) values (?,?,?,?)";
      $pdo->prepare($sql)->execute([$username, $name, $email, $hash]);
      $id = $pdo->lastInsertId();

      // start the session and store the user id
      session_start();
      $_SESSION['userid'] = $id;

      // redirect to move list
      header("Location:index.php");
      exit();
    }
  }
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $PAGE_TITLE = "Create an Account";
  include "includes/head_includes.php"; ?>
</head>

<body>
  <?php include "includes/headernav.php"; ?>
  <main id="account-page">
    <div>
      <h1>Create an Account</h1>
      <h2>Please enter your information</h2>
    </div>
    <form id="create-account" name="create-account" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
      <div>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?php echo $username ?>" required />
        <span <?php echo in_array("username", $errors) ? "class='error'" : "class='noerror'"; ?>>You must enter a username</span>
        <span <?php echo in_array("username exists", $errors) ? "class='error'" : "class='noerror'"; ?>>Username already exists</span>
      </div>
      <div>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" pattern="[A-Za-z ,.'-]+$" value="<?php echo $name ?>" required />
        <span <?php echo in_array("name", $errors) ? "class='error'" : "class='noerror'" ?>>You must enter your full name</span>
      </div>
      <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo $email; ?>" required />
        <span <?php echo in_array("email", $errors) ? "class='error'" : "class='noerror'"; ?>>You must enter a valid email address</span>
        <span <?php echo in_array("email exists", $errors) ? "class='error'" : "class='noerror'"; ?>>There is already an account with this email</span>
      </div>
      <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required />
        <span <?php echo in_array("password", $errors) ? "class='error'" : "class='noerror'"; ?>>Password is required</span>
      </div>
      <div>
        <label for="confirm_pass">Confirm Password:</label>
        <input type="password" name="confirm_pass" id="confirm_pass" required />
        <span <?php echo in_array("no match", $errors) ? "class='error'" : "class='noerror'"; ?>>Passwords do not match</span>
      </div>
      <div id="password-error" class="noerror">
        Make your password stronger. Please include a mix of 3 of the following:
        <ul>
          <li>uppercase character</li>
          <li>number</li>
          <li>special character</li>
        </ul>
      </div>
      <input type="submit" name="submit" value="Create Account" />
    </form>
  </main>
  <footer>
    <p>&copy; Movie Collector's Inc. 2019</p>
  </footer>
</body>
</html>
