<!--
Add a Movie

A logged in user can enter details about a movie
they wish to add to their collection
The form does validation checking to ensure they
provide a title, genre, mpaa rating, and video type
-->
<?php
  // start the session and check session for user id
  session_start();
  if(!isset($_SESSION['userid'])) {
    header("Location:login.php");
  }

  include 'includes/library.php';
  include 'includes/upload.php';

  // Set default variable values
  $errors = array();
  $userid = $_SESSION['userid'];
  $title=NULL;
  $year=0;
  $runtime=0;
  $theatre=NULL;
  $dvd=NULL;
  $actors=NULL;
  $studio=NULL;
  $plot=NULL;
  $mpaa=NULL;
  $rating=0;
  $genres=array();
  $types=array();

  // When the user submits the form
  if(isset($_POST['submit'])) {

    // Set the variables with values in the form
    $mpaa = $_POST['mpaa'] ?? NULL;
    $year = $_POST['year'] ?? 0;
    $runtime = $_POST['runtime'] ?? 0;
    $theatre = $_POST['theatre'] ?? NULL;
    $dvd = $_POST['dvd'] ?? NULL;
    $actors = $_POST['actors'] ?? NULL;
    $studio = $_POST['studio'] ?? NULL;
    $plot = $_POST['plot'] ?? NULL;
    $rating = $_POST['rating'] ?? "";

    // Set an error if there is no title
    $title = $_POST['title'] ?? "";
    if($title == "")
      $errors[] = "title";

    // Store genre based on how many were selected
    $genres = $_POST['genre'] ?? array();
    if(sizeof($genres) > 1) {
      $genre = join(", ", $genres); // if more than one, join the values into a string
    } else if(sizeof($genres) == 1) {
      $genre = $genres[0];  // if only 1, store it alone
    } else {
      $genre = NULL;  // if 0, store null
    }

    $types = $_POST['type'] ?? array();
    // Set an error if no video type was selected
    if(sizeof($types) == 0) {
      $errors[] = "type";
    }
    // Store video type based on how many were selected
    if(sizeof($types > 1)) {
      $type = join(", ", $types);
    } else {
      $type = $types[0];
    }

    // Continue with insertion if there are no errors
    if(sizeof($errors) == 0) {
      // connect to database and insert movie information
      $pdo = & dbconnect();
      $sql = "insert into Movies (uID, runtime, year, rating, title, plot, actors, genre, vType, studio, mpaa, theatreDate, dvdDate) values (?,?,?,?,?,?,?,?,?,?,?,?,?)";
      $stmt = $pdo->prepare($sql)->execute([$userid, $runtime, $year, $rating, $title, $plot, $actors, $genre, $type, $studio, $mpaa, $theatre, $dvd]);
      $mid = $pdo->lastInsertId();  // get the movie ID for the cover
      // upload the cover if provided
      if(is_uploaded_file($_FILES['cover']['tmp_name'])) {
        $newname = createFilename('cover', 'www_data/covers/', 'cover', $mid);
        checkAndMoveFile('cover', 10240000, WEBROOT.$newname);
        $sql = "update Movies set cover=? where mID=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$newname, $mid]);
      }
      // redirect to details page for the added movie
      header("Location:displaydetails.php?id=$mid");
      exit();
    }
  }

  // if user selects reset button, clear all the fields
  if(isset($_POST['reset'])) {
    $title="";
    $year="";
    $runtime="";
    $theatre="";
    $dvd="";
    $actors="";
    $studio="";
    $plot="";
    $mpaa="";
    $genres=array();
    $types=array();
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php $PAGE_TITLE = "Add a Movie";
  include "includes/head_includes.php"; ?>
</head>

<body>
  <?php include "includes/headernav.php"; ?>
  <main id="add-vid-page">
    <h1>Add a Movie</h1>
    <form enctype="multipart/form-data" id="add-movie" name="add-movie" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
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
      <input type="submit" name="submit" value="Add Movie" />
      <input type="submit" name="reset" value="Reset" />
    </form>
  </main>
  <footer>
    <p>&copy; Movie Collector's Inc. 2019</p>
  </footer>
</body>
</html>
