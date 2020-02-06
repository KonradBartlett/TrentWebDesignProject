<?php
  // start session and check session for user id
  session_start();
  if(!isset($_SESSION['userid'])) {
    header("Location:account.php");
    exit();
  }

  include 'includes/library.php';

  $userid = $_SESSION['userid'];  // get user id from session
  // connect to database and retrieve all movie entries for current user
  $pdo = & dbconnect();
  $sql = "select title, mID, cover from Movies where uID = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$userid]);

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $PAGE_TITLE = "Movie Collection";
  include "includes/head_includes.php"; ?>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
</head>

<body>
  <?php include "includes/headernav.php"; ?>
  <main id="main-page">
    <div>
      <?php foreach($stmt as $row): // display all the movies?>
        <div class="movie" id="<?php echo $row['mID']; ?>">
          <figure>
            <?php if($row['cover'] != ""): ?>
              <img src="<?php echo "/~margaretkikkert/".$row['cover'] ?>" alt="" />
            <?php else: // set a default cover if one wasn't provided ?>
              <img src="img/nocover.png" alt="" />
            <?php endif; ?>
            <figcaption><?php echo $row['title']; ?></figcaption>
          </figure>
          <div class="movie-buttons">
            <a href="editvid.php?id=<?php echo $row['mID']; ?>"> <i class="fas fa-edit"></i></a>
            <a href="displaydetails.php?id=<?php echo $row['mID']; ?>"> <i class="fas fa-info-circle"></i></a>
            <a href="deletevid.php?id=<?php echo $row['mID']; ?>"> <i class="fas fa-trash-alt"></i></a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div>
    	<a href="">&#8592; Previous</a>
    	<a href="">Next &#8594;</a>
    </div>
  </main>
  <footer>
    <p>&copy; Movie Collector's Inc. 2019</p>
  </footer>
</body>
</html>
