<!--
Movie Collection Login

User with an existing account can log in to the site
Checks that user's password matches the username and
creates a cookie for the username if requested by
remember me checkbox
-->
<?php

  // start session
  session_start();

  // check for username cookie
  if(isset($_COOKIE['username'])) {
    $username = $_COOKIE['username'];
  } else {
    $username = "";
  }

  $error = false;

  // if the user presses login
  if(isset($_POST['login'])) {
    // get username and password from form
    $username = $_POST['username'];
    $password = $_POST['password'];
    include 'includes/library.php';

    // connect to database and check user credentials
    $pdo = & dbconnect();
    $sql = "select uID, userpass from Users where username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    // store database values
    $dbpass = $info['userpass'];
    $userid = $info['uID'];

    // if they checked remember me, create a cookie with their username
    if(isset($_POST['remember'])) {
      setcookie("username", $username, time()+60*60*24);
    }

    // verify that they input a password and that is matches database value
    if($dbpass && password_verify($password, $dbpass)) {
      $_SESSION['userid'] = $userid;  // store user id in session
      // redirect to the home page
      header("Location: index.php");
      exit();
    }else{  // otherwise set an error
      $error=true;
    }
  }

  if(isset($_POST['reset'])) {
    $username="";
    $password="";
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $PAGE_TITLE = "Movie Collection Login";
  include "includes/head_includes.php"; ?>
</head>

<body>
  <?php include "includes/headernav.php"; ?>
  <main id="login-page">
      <h1>Login</h1>
      <?php if($error) { echo "<div class=\"error\">Your username or password was invalid</div>"; } ?>
      <form id="voting" name="voting" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <div>
          <label for="username">Username:</label>
          <input type="text" name="username" id="username" value="<?php echo $username ?>" required/>
        </div>
        <div>
          <label for="password">Password:</label>
          <input type="password" name="password" id="password" required/>
        </div>
        <div>
          <label for="remember">Remember me</label>
          <input type="checkbox" name="remember" id="remember" value="remember_me" checked />
        </div>
        <div>
          <input type="submit" name="login" value="Login"/>
          <input type="button" name="reset" value="Reset"/>
        </div>
      </form>
      <div><a href="">Forget password?</a></div>
  </main>
  <footer>
    <p>&copy; Movie Collector's Inc. 2019</p>
  </footer>
</body>
</html>
