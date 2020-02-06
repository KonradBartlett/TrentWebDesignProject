<?php
  // start session and check session for user id
  session_start();
  if(!isset($_SESSION['userid'])) {
    header("Location:login.php");
  }

  include 'includes/library.php';

  // get user id from session and set default variable values
  $userid = $_SESSION['userid'];
  $term="";
  $stmt=array();

  // if the user submits a search
  if(isset($_POST['search'])) {

    $term = $_POST['term']; // store the entered search term

    // connect to database and retrieve movies with titles that contain the term
    $pdo = & dbconnect();
    $sql="select title, cover, mID from Movies where title like ? and uID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%".$term."%", $userid]);
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $PAGE_TITLE = "Movie Collection Search";
  include "includes/head_includes.php"; ?>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
</head>

<body>
  <?php include "includes/headernav.php"; ?>
  <main id="search-page">
    <!-- Search Form -->
    <form id="search" name="search" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    	<label for="term">Search Term:</label>
    	<input type="text" name="term" id="term" value="<?php echo $term; ?>" required/>
    	<input type="submit" name="search" value="Search"/>
    </form>
    <!-- Search Results -->
    <div>
      <?php foreach($stmt as $row): ?>
        <div class="movie">
          <figure>
            <img src="<?php echo "/~margaretkikkert/".$row['cover'] ?>" alt="<?php echo $row['title']; ?> cover" />
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
  </main>
  <footer>
    <p>&copy; Movie Collector's Inc. 2019</p>
  </footer>
</body>
</html>
