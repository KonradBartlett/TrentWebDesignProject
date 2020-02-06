<?php
  // start session and check session for userid
  session_start();
  if(!isset($_SESSION['userid'])) {
    header("Location:login.php");
  }

  include 'includes/library.php';
  include 'includes/upload.php';

  $errors = array();
  $mid = $_GET['id'];             // get the movie id from the url
  $userid = $_SESSION['userid'];  // get user id from session

  // Connect to database and retrieve movie data from database
  $pdo = & dbconnect();
  $sql = "select * from Movies where mID = ? and uID = ?";
  $result = $pdo->prepare($sql);
  $result->execute([$mid, $userid]);
  $movie = $result->fetch(PDO::FETCH_ASSOC);

  // store movie info in variables
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
  $type = $movie['vType'];
  // turn video types into an array
  if(substr_count($type, ", ") > 0) {
    $types = explode(", ", $type);
  } else {
    $types = array($type);
  }
  // turn genres into an array
  $genre = $movie['genre'];
  if(substr_count($genre, ", ") > 0) {
    $genres = explode(", ", $genre);
  } else {
    $genres = array($genre);
  }

  // if the user submits the form
  if(isset($_POST['submit'])) {

    // store entered values
    $mpaa = $_POST['mpaa'] ?? NULL;
    $year = $_POST['year'] ?? 0;
    $runtime = $_POST['runtime'] ?? 0;
    $theatre = $_POST['theatre'] ?? NULL;
    $dvd = $_POST['dvd'] ?? NULL;
    $actors = $_POST['actors'] ?? NULL;
    $studio = $_POST['studio'] ?? NULL;
    $plot = $_POST['plot'] ?? NULL;
    $rating = $_POST['rating'] ?? "";

    // Set an error if no title is entered
    $title = $_POST['title'] ?? "";
    if($title == "")
      $errors[] = "title";
    // Store genres as a string
    $genres = $_POST['genre'] ?? array();
    if(sizeof($genres) > 1) {
      $genre = join(", ", $genres);
    } else if(sizeof($genres) == 1) {
      $genre = $genres[0];
    } else {
      $genre = NULL;
    }

    $types = $_POST['type'] ?? array();
    // Set an error if no video type is chosen
    if(sizeof($types) == 0) {
      $errors[] = "type";
    }
    // Store video types as a string
    if(sizeof($types > 1)) {
      $type = join(", ", $types);
    } else {
      $type = $types[0];
    }

    // if there are no errors, update movie entry
    if(sizeof($errors) == 0) {
      $pdo = & dbconnect();
      $sql = "update Movies set runtime=?, year=?, rating=?, title=?, plot=?, actors=?, genre=?, vType=?, studio=?, mpaa=?, theatreDate=?, dvdDate=? where mID = ? and uID = ?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$runtime, $year, $rating, $title, $plot, $actors, $genre, $type, $studio, $mpaa, $theatre, $dvd, $mid, $userid]);
      // upload the cover if provided
      if(is_uploaded_file($_FILES['cover']['tmp_name'])) {
        $newname = createFilename('cover', 'www_data/covers/', 'cover', $mid);
        checkAndMoveFile('cover', 10240000, WEBROOT.$newname);
        $sql = "update Movies set cover=? where mID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$newname, $mid]);
      }
      // redirect to details page for edited movie
      header("Location:displaydetails.php?id=".$mid);
      exit();
    }
  }

  // if user resets form, clear all input fields
  if(isset($_POST['reset'])) {
    $title="";
    $year=0;
    $runtime=0;
    $theatre="";
    $dvd="";
    $actors="";
    $studio="";
    $plot="";
    $mpaa="";
    $cover="";
    $genres=array();
    $types=array();
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $PAGE_TITLE = "Edit Movie";
  include "includes/head_includes.php"; ?>
</head>

<body>
  <?php include "includes/headernav.php"; ?>
  <main id="edit-page">
    <h1>Edit Movie</h1>
    <form enctype="multipart/form-data" id="add-movie" name="add-movie" method="post" action="<?php echo $_SERVER['PHP_SELF']."?id=".$mid; ?>">
      <div>
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo $title; ?>" required />
        <span <?php echo in_array("title", $errors) ? "class='error'" : "class='noerror'" ?>>Please enter a title</span>
      </div>
      <div>
        <label for="rating">Rating:</label>
        <select name="rating" id="rating">
          <option value="0" <?php if($rating == 0) echo "selected"; ?>>Select One</option>
          <option value="1" <?php if($rating == 1) echo "selected"; ?>>1 star</option>
          <option value="2" <?php if($rating == 2) echo "selected"; ?>>2 stars</option>
          <option value="3" <?php if($rating == 3) echo "selected"; ?>>3 stars</option>
          <option value="4" <?php if($rating == 4) echo "selected"; ?>>4 stars</option>
          <option value="5" <?php if($rating == 5) echo "selected"; ?>>5 stars</option>
        </select>
      </div>
      <div>
        <label for="genre">Genre:</label>
        <select name="genre[]" id="genre" multiple>
          <option value="Action" <?php if(in_array("Action", $genres)) echo "selected"; ?>>Action</option>
          <option value="Adventure" <?php if(in_array("Adventure", $genres)) echo "selected"; ?>>Adventure</option>
          <option value="Crime" <?php if(in_array("Crime", $genres)) echo "selected"; ?>>Crime</option>
          <option value="Drama" <?php if(in_array("Drama", $genres)) echo "selected"; ?>>Drama</option>
          <option value="Fantasy" <?php if(in_array("Fantasy", $genres)) echo "selected"; ?>>Fantasy</option>
          <option value="Historical" <?php if(in_array("Historical", $genres)) echo "selected"; ?>>Historical</option>
          <option value="Horror" <?php if(in_array("Horror", $genres)) echo "selected"; ?>>Horror</option>
          <option value="Musical" <?php if(in_array("Musical", $genres)) echo "selected"; ?>>Musical</option>
          <option value="Scifi" <?php if(in_array("Scifi", $genres)) echo "selected"; ?>>Science Fiction</option>
          <option value="Thriller" <?php if(in_array("Thriller", $genres)) echo "selected"; ?>>Thriller</option>
          <option value="War" <?php if(in_array("War", $genres)) echo "selected"; ?>>War</option>
          <option value="Western" <?php if(in_array("Western", $genres)) echo "selected"; ?>>Western</option>
        </select>
        <span class="noerror">Please select a genre</span>
      </div>
      <div>
        <div class="noerror">Please select a rating</div>
        <fieldset>
          <legend>MPAA Rating:</legend>
          <input type="radio" name="mpaa" id="g" value="G" <?php if($mpaa=="G") echo "checked"; ?>>
          <label for="g">G</label>
          <input type="radio" name="mpaa" id="pg" value="PG" <?php if($mpaa=="PG") echo "checked"; ?>>
          <label for="pg">PG</label>
          <input type="radio" name="mpaa" id="pg13" value="PG-13" <?php if($mpaa=="PG-13") echo "checked"; ?>>
          <label for="pg13">PG-13</label>
          <input type="radio" name="mpaa" id="r" value="R" <?php if($mpaa=="R") echo "checked"; ?>>
          <label for="r">R</label>
          <input type="radio" name="mpaa" id="nc17" value="NC-17" <?php if($mpaa=="NC-17") echo "checked"; ?>>
          <label for="nc17">NC-17</label>
        </fieldset>
      </div>
      <div>
        <label for="year">Year:</label>
        <input type="text" name="year" id="year" value="<?php echo $year; ?>" />
      </div>
      <div>
        <label for="runtime">Runtime:</label>
        <input type="text" name="runtime" id="runtime" value="<?php echo $runtime; ?>" /><span>(mins)</span>
      </div>
      <div>
        <label for="theatre-release">Theatre Release:</label>
        <input type="text" name="theatre" id="theatre-release" value="<?php echo $theatre; ?>" /><span>YYYY-MM-DD</span>
        <span class="noerror">Please enter a valid date</span>
      </div>
      <div>
        <label for="dvd-release">DVD Release:</label>
        <input type="text" name="dvd" id="dvd-release" value="<?php echo $dvd; ?>" /><span>YYYY-MM-DD</span>
        <span class="noerror">Please enter a valid date</span>
      </div>
      <div>
        <label for="actors">Actors:</label>
        <input type="text" name="actors" id="actors" value="<?php echo $actors; ?>" />
      </div>
      <div>
        <input type="hidden" name="MAX_FILE_SIZE" value ="10240000" />
        <label for="cover">Cover:</label>
        <input type="file" name="cover" id="cover" />
        <?php if($cover != ""): ?>
          <div><img src="<?php echo "/~margaretkikkert/".$cover; ?>" alt="" /></div>
        <?php endif; ?>
      </div>
      <div>
        <label for="studio">Studio:</label>
        <input type="text" name="studio" id="studio" value="<?php echo $studio; ?>" />
      </div>
      <div>
        <label for="plot">Plot Summary:</label>
        <textarea rows="4" cols="50" name="plot" id="plot" maxlength="2500"><?php echo $plot; ?></textarea>
        <span>2500</span>
      </div>
      <div>
        <div <?php echo in_array("type", $errors) ? "class='error'" : "class='noerror'" ?>>Please choose a movie type</div>
        <fieldset >
          <legend>Video Type</legend>
          <input type="checkbox" name="type[]" id="dvd" value="DVD" <?php if(in_array("DVD", $types)) echo "checked"; ?> />
          <label for="dvd">DVD</label>
          <input type="checkbox" name="type[]" id="bluray" value="Blu-ray" <?php if(in_array("Blu-ray", $types)) echo "checked"; ?> />
          <label for="bluray">Blu-ray</label>
          <input type="checkbox" name="type[]" id="4kdisk" value="4K-Disk" <?php if(in_array("4K-Disk", $types)) echo "checked"; ?> />
          <label for="4kdisk">4K Disk</label>
          <input type="checkbox" name="type[]" id="sd" value="SD" <?php if(in_array("SD", $types)) echo "checked"; ?> />
          <label for="sd">Digital SD</label>
          <input type="checkbox" name="type[]" id="hd" value="HD" <?php if(in_array("HD", $types)) echo "checked"; ?> />
          <label for="hd">Digital HD</label>
          <input type="checkbox" name="type[]" id="4kdigital" value="4K-Digital" <?php if(in_array("4K-Digital", $types)) echo "checked"; ?> />
          <label for="4kdigital">Digital 4K</label>
        </fieldset>
      </div>
      <input type="submit" name="submit" value="Save Changes" />
      <input type="submit" name="reset" value="Reset" />
    </form>
  </main>
  <footer>
    <p>&copy; Movie Collector's Inc. 2019</p>
  </footer>
</body>
</html>
