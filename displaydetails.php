<?php
  // start session and check session for user id
  session_start();
  if(!isset($_SESSION['userid'])) {
    header("Location:login.php");
  }

  include 'includes/library.php';
  $mid = $_GET['id'];             // get the movie id from the url
  $userid = $_SESSION['userid'];  // get user id from the session

  // connect to database and retrieve relevant movie information
  $pdo = & dbconnect();
  $sql = "select * from Movies where mID = ? and uID = ?";
  $result = $pdo->prepare($sql);
  $result->execute([$mid, $userid]);
  $movie = $result->fetch(PDO::FETCH_ASSOC);

  // store the information in variables
  $title = $movie['title'];
  $runtime = $movie['runtime'];
  $year = $movie['year'];
  $rating = $movie['rating'];
  $plot = $movie['plot'];
  $actors = $movie['actors'];
  $cover = $movie['cover'];
  $studio = $movie['studio'];
  $mpaa = $movie['mpaa'];
  $theatre = $movie['theatreDate'];
  $dvd = $movie['dvdDate'];
  $genres = $movie['genre'];
  $type = $movie['vType'];
  if(substr_count($type, ", ") > 0) { // if more than one video type is entered, separate the string to make an array
    $types = explode(", ", $type);
  } else {
    $types = array($type);  // otherwise just make an array with 1 item
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $PAGE_TITLE = "Movie details";
  include "includes/head_includes.php"; ?>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
</head>

<body>
  <?php include "includes/headernav.php"; ?>
  <main id="details-page">
    <div>
      <?php if($cover != ""): ?>
        <img src="<?php echo "/~margaretkikkert/".$cover; ?>" alt="" />
      <?php endif; ?>
      <h1><?php echo $title; ?></h1>
      <div><span>Rating: </span><span><?php echo $rating; ?>/5</span></div>
      <div><span>MPAA: </span><span><?php echo $mpaa; ?></span></div>
      <div><span>Year: </span><span><?php echo $year; ?></span></div>
      <div><span>Runtime: </span><span><?php echo $runtime; ?></span></div>
      <div><span>Studio: </span><span><?php echo $studio; ?></span></div>
      <div><span>Theatrical Release: </span><span><?php echo $theatre; ?></span></div>
      <div><span>DVD Release: </span><span><?php echo $dvd; ?></span></div>
      <div><span>Actors: </span><span><?php echo $actors; ?></span></div>
      <div><span>Genre: </span><span><?php echo $genres; ?></span></div>
      <div><?php echo $plot; ?></div>
      <div>
        <span>DVD<?php echo (in_array("DVD", $types)) ? "<i class=\"fas fa-check\"></i>" : "<i class=\"fas fa-times\"></i>"?></span>
        <span>Blu-ray<?php echo (in_array("Blu-ray", $types)) ? "<i class=\"fas fa-check\"></i>" : "<i class=\"fas fa-times\"></i>"?></span>
        <span>4K Disk<?php echo (in_array("4K-Disk", $types)) ? "<i class=\"fas fa-check\"></i>" : "<i class=\"fas fa-times\"></i>"?></span>
        <span>Digital SD<?php echo (in_array("SD", $types)) ? "<i class=\"fas fa-check\"></i>" : "<i class=\"fas fa-times\"></i>"?></span>
        <span>Digital HD<?php echo (in_array("HD", $types)) ? "<i class=\"fas fa-check\"></i>" : "<i class=\"fas fa-times\"></i>"?></span>
        <span>Digital 4K<?php echo (in_array("4K-Digital", $types)) ? "<i class=\"fas fa-check\"></i>" : "<i class=\"fas fa-times\"></i>"?></span>
      </div>
    </div>
  </main>
  <footer>
    <p>&copy; Movie Collector's Inc. 2019</p>
  </footer>
</body>
</html>
