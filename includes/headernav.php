<?php
  $current_page = basename($_SERVER['PHP_SELF']);
  $active = "class=\"active\"";
 ?>
<header>
  <div>
    <h1>The Movie Collector</h1>
    <h2>Welcome to your movie database!</h2>
  </div>
</header>
<nav>
  <ul>
    <?php if(isset($_SESSION['userid'])): ?>
      <li><a href="index.php" <?php if($current_page == "index.php") echo $active; ?>>Movie List</a></li>
      <li><a href="addvid.php" <?php if($current_page == "addvid.php") echo $active; ?>>Add a Movie</a></li>
      <li><a href="search.php" <?php if($current_page == "search.php") echo $active; ?>>Search</a></li>
      <li><a href="editaccount.php" <?php if($current_page == "editaccount.php") echo $active; ?>>Edit Account</a></li>
      <li><a href="deleteaccount.php" <?php if($current_page == "deleteaccount.php") echo $active; ?>>Delete Account</a></li>
      <li><a href="logout.php">Logout</a></li>
    <?php else: ?>
      <li><a href="account.php" <?php if($current_page == "account.php") echo $active; ?>>Create Account</a></li>
      <li><a href="login.php" <?php if($current_page == "login.php") echo $active; ?>>Login</a></li>
    <?php endif; ?>

  </ul>
</nav>
