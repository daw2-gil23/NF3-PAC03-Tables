<?php
// function to generate ratings
function generate_ratings($rating) {
    $movie_rating = '';
    global $cont;
    for ($i = 0; $i < $rating; $i++) {
        $cont++;
        $movie_rating .= '<img style="height: 15px;	" src="star.png" alt="star"/>';
    }
    global $cont2;
    $cont2++;
    return [$movie_rating,$cont,$cont2];
}


// take in the id of a director and return his/her full name
function get_director($director_id) {

    global $db;

    $query = 'SELECT 
            people_fullname 
       FROM
           people
       WHERE
           people_id = ' . $director_id;
    $result = mysqli_query($db, $query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return $people_fullname;
}

// take in the id of a lead actor and return his/her full name
function get_leadactor($leadactor_id) {

    global $db;

    $query = 'SELECT
            people_fullname
        FROM
            people 
        WHERE
            people_id = ' . $leadactor_id;
    $result = mysqli_query($db, $query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return $people_fullname;
}

// take in the id of a movie type and return the meaningful textual
// description
function get_movietype($type_id) {

    global $db;

    $query = 'SELECT 
            movietype_label
       FROM
           movietype
       WHERE
           movietype_id = ' . $type_id;
    $result = mysqli_query($db, $query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return $movietype_label;
}

// function to calculate if a movie made a profit, loss or just broke even
function calculate_differences($takings, $cost) {

    $difference = $takings - $cost;

    if ($difference < 0) {     
        $color = 'red';
        $difference = '$' . abs($difference) . ' million';
    } elseif ($difference > 0) {
        $color ='green';
        $difference = '$' . $difference . ' million';
    } else {
        $color = 'blue';
        $difference = 'broke even';
    }

    return '<span style="color:' . $color . ';">' . $difference . '</span>';
}

//connect to MySQL
$db = mysqli_connect('localhost', 'root', 'root') or 
    die ('Unable to connect. Check your connection parameters.');

// make sure you're using the right database
mysqli_select_db($db,'moviesite') or die(mysqli_error($db));

// retrieve information
$query = 'SELECT
        movie_name, movie_year, movie_director, movie_leadactor,
        movie_type, movie_running_time, movie_cost, movie_takings
    FROM
        movie
    WHERE
        movie_id = ' . $_GET['movie_id'];
$result = mysqli_query($db, $query) or die(mysqli_error($db));

$row = mysqli_fetch_assoc($result);
$movie_name         = $row['movie_name'];
$movie_director     = get_director($row['movie_director']);
$movie_leadactor    = get_leadactor($row['movie_leadactor']);
$movie_year         = $row['movie_year'];
$movie_running_time = $row['movie_running_time'] .' mins';
$movie_takings      = $row['movie_takings'] . ' million';
$movie_cost         = $row['movie_cost'] . ' million';
$movie_health       = calculate_differences($row['movie_takings'],
                          $row['movie_cost']);

// display the information
echo <<<ENDHTML
<html>
 <head>
  <title>Details and Reviews for: $movie_name</title>
 </head>
 <body>
  <div style="text-align: center;">
   <h2>$movie_name</h2>
   <h3><em>Details</em></h3>
   <table cellpadding="2" cellspacing="2"
    style="width: 70%; margin-left: auto; margin-right: auto;">
    <tr>
     <td><strong>Title</strong></strong></td>
     <td>$movie_name</td>
     <td><strong>Release Year</strong></strong></td>
     <td>$movie_year</td>
    </tr><tr>
     <td><strong>Movie Director</strong></td>
     <td>$movie_director</td>
     <td><strong>Cost</strong></td>
     <td>$$movie_cost<td/>
    </tr><tr>
     <td><strong>Lead Actor</strong></td>
     <td>$movie_leadactor</td>
     <td><strong>Takings</strong></td>
     <td>$$movie_takings<td/>
    </tr><tr>
     <td><strong>Running Time</strong></td>
     <td>$movie_running_time</td>
     <td><strong>Health</strong></td>
     <td>$movie_health<td/>
    </tr>
   </table>
ENDHTML;

// retrieve reviews for this movie
$query = 'SELECT
        review_movie_id, review_date, reviewer_name, review_comment,
        review_rating
    FROM
        reviews
    WHERE
        review_movie_id = ' . $_GET['movie_id'] . '
    ORDER BY
        review_date DESC';


$result = mysqli_query($db, $query) or die(mysqli_error($db));

$id=$_GET['movie_id'];

echo <<< ENDHTML
    <h3><em>Reviews</em></h3>
    <table cellpadding="2" cellspacing="2" style="width: 90%; margin-left: auto; margin-right: auto;">
        <tr>
            <th style="width: 7em;"><a href="asc.php?movie_id=$id&order=review_date&ad= ASC">Date</a></th>
            <th style="width: 10em;"><a href="asc.php?movie_id=$id&order=reviewer_name&ad= ASC">Reviewer</a></th>
            <th><a href="asc.php?movie_id=$id&order=review_comment&ad= ASC">Comments</a></th>
            <th style="width: 5em;"><a href="asc.php?movie_id=$id&order=review_rating&ad= ASC">Rating</a></th>
        </tr>
ENDHTML;

$cont2=1;
while ($row = mysqli_fetch_assoc($result)) {
    echo "contador:";
    echo $cont2;

    $date = $row['review_date'];
    $name = $row['reviewer_name'];
    $comment = $row['review_comment'];
    list($movie_rating,$cont,$cont2) = generate_ratings($row['review_rating']);
    $rating = $movie_rating;


    if($cont2%2==0){
        echo <<<ENDHTML
        <tr>
          <td style="vertical-align:top; text-align: center; background: blueviolet;">$date</td>
          <td style="vertical-align:top; background: blueviolet;">$name</td>
          <td style="vertical-align:top; background: blueviolet;">$comment</td>
          <td style="vertical-align:top; background: blueviolet;">$rating</td>
        </tr>
        ENDHTML;
    }else{
        echo <<<ENDHTML
        <tr>
          <td style="vertical-align:top; text-align: center; background: aquamarine;">$date</td>
          <td style="vertical-align:top; background: aquamarine;">$name</td>
          <td style="vertical-align:top; background: aquamarine;">$comment</td>
          <td style="vertical-align:top; background: aquamarine;">$rating</td>
        </tr>
        ENDHTML;   
    }

}

$media=$cont/$cont2;
echo <<<ENDHTML
    <p>Calificación media: $media</p>
  </div>
 </body>
</html>
ENDHTML;
?>
