<!--
Delete Account

Allows a logged in user to delete their account
User must provide their password and confirm their
choice to delete by checking a checkbox
-->
<?php
  // Start session and check session for userid
  session_start();
  if(!isset($_SESSION['userid'])) {
    header("Location:account.php");
    exit();
  }

  // set error to false and get user id from session
  $error = false;
  $userid = $_SESSION['userid'];

  // if the user submits the form
  if(isset($_POST['submit'])) {
    include 'includes/library.php';
    $pass = $_POST['password']; // get input password from the form

    // retrieve stored password
    $pdo = & dbconnect();
    $query = "select userpass from Users where uID = ? ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userid]);
    $stored_pass = $stmt->fetchColumn();

    // verify the password
    if($stored_pass && password_verify($pass, $stored_pass)) {
      // delete all account media
      $query = "delete from Movies where uID = ?";
      $pdo->prepare($query)->execute([$userid]);

      // delete account info
      $query = "delete from Users where uID = ?";
      $pdo->prepare($query)->execute([$userid]);

      // destroy session and cookies
      session_destroy();
      // delete any remaining cookies
      foreach($_COOKIE as $cookie) {
        setcookie($cookie, "", 1);
      }

      // redirect to login page
      header("Location:login.php");
      exit();
    } else {  // otherwise report an error
      $error = true;
    }

  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $PAGE_TITLE = "Delete Account";
  include "includes/head_includes.php"; ?>
</head>

<body>
  <?php include "includes/headernav.php"; ?>
  <main id="delete-account-page">
    <div>
      <h1>Delete Account</h1>
      <h2>Are you sure you want to delete your account?</h2>
    </div>
    <div>
      <?php if($error) { echo "<span class=\"error\">Password is incorrect</span>"; } ?>
      <form id="delete-account" name="delete-account" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <div>
          <label for="password">Confirm password:</label>
          <input type="password" name="password" id="password" required />
        </div>
        <div>
          <input type="checkbox" name="confirm" id="confirm" required />
          <label for="confirm">Yes, I am sure I want to delete my account</label>
        </div>
        <input type="submit" name="submit" value="Delete Account" />
      </form>
    </div>
  </main>
  <footer>
    <p>&copy; Movie Collector's Inc. 2019</p>
  </footer>
</body>
</html>
