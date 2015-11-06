<!doctype html>
<html>
<body>

<div id="navbar">
<!-- Wanna implement a persistent search bar here -->
	<div id="searchbar">
		<form method="POST" action="search.php" name="SearchBar">
			<input type="text" name="search">
			<input type="submit" value="Search" name="submit">
		</form>
	</div>

</div>

</body>

<head>
	<title>My Movies</title>
	<link href="styles.css" rel="stylesheet" type="text/css">
</head>

<body>
	<h1>Update Database</h1>
	<h2>Choose a following form to fill out</h2>

<!-- The Actor Form TODO: Add director too -->
<div class = "form">
<form method = "Post" action = "" id = "actor">
	<h1>Add an actor to database</h1>

	<p>Identity:	<input type="radio" name="job" value="actor" >Actor
	<input type="radio" name="job" value="director">Director<br/></p>

	<label>First Name:</label>
	<input type = "text" name="first" maxlength="20"><br/>

	<label>Last Name:</label>
	<input type ="text" name ="last" maxlength="20"><br/>

	<input type="radio" name="sex" value="Male" >Male
	<input type="radio" name="sex" value="Female">Female<br/>

	<label >Date of Birth:</label>
	<input type="text" name="dob"> YYYY-MM-DD<br/>
	
	<label >Date of Death:</label>
	<input type="text" name="dod"> (Optional: leave empty if still alive)<br/>

	<input type="submit" value="Submit" name="subActor" />
</form>
</div>

<p>
<?php
if(isset($_POST['subActor']))
{

	$db_connection = mysql_connect("localhost", "cs143", "");
	if(!$db_connection) {
		$errmsg = mysql_error($db_connection);
		print "Connection failed: $errmsg <br />";
		exit(1);
	}
	mysql_select_db("CS143", $db_connection);

	//get form variables
	$first = $_POST['first'];
	$last = $_POST['last'];
	$sex = $_POST['sex'];
	$dob = $_POST['dob'];
	$dod = $_POST['dod'];
	$job = $_POST['job'];

	if($job == "") {echo "please choose Actor or Director <br/>";}
	if($first == "") {echo "please input a first name <br/>";}
	if($last == "") {echo "please input a last name <br/>";}
	if($sex == "") {echo "please choose a sex <br/>";}
	if($dob == "") {echo "please input a date of birth <br/>";}
	
if ($job == 'actor' && $first != "" && $last != "" && $sex != "" && $dob != "") {
	//get max id 
	$query = "select * from MaxPersonID;";
	$result = mysql_query($query, $db_connection);
	$row = mysql_fetch_row($result);
	$id = $row[0];
	$id++;
	//update value
	$query = "update MaxPersonID set id=$id"; 
	mysql_query($query, $db_connection);
	
	//insert into Actor
	$query = "insert into Actor values (%s, '%s', '%s', '%s', '%s', '%s' );";
	$query_to_issue = sprintf($query, $id, mysql_real_escape_string($last), mysql_real_escape_string($first), mysql_real_escape_string($sex), mysql_real_escape_string($dob), mysql_real_escape_string($dod) );
	mysql_query($query_to_issue, $db_connection);
	
	

	mysql_close($db_connection);
	
	echo "Thank you for your input of $first $last on Actor";
}

if($job == 'director'&& $first != "" && $last != "" && $sex != "" && $dob != ""){
	
	$query = "select * from MaxPersonID;";
	$result = mysql_query($query, $db_connection);
	$row = mysql_fetch_row($result);
	$id = $row[0];
	$id++;
	//update value
	$query = "update MaxPersonID set id=$id"; 
	mysql_query($query, $db_connection);
	
	//insert into Director
	$query = "insert into Director values (%s, '%s', '%s', '%s', '%s');";
	$query_to_issue = sprintf($query, $id, mysql_real_escape_string($last), mysql_real_escape_string($first), mysql_real_escape_string($dob), mysql_real_escape_string($dod) );
	mysql_query($query_to_issue, $db_connection);
	
	mysql_close($db_connection);
	if($first == "") {echo "please input a first name <br/>";}
	if($last == "") {echo "please input a last name <br/>";}
	if($sex == "") {echo "please choose a sex <br/>";}
	if($dob == "") {echo "please input a date of birth <br/>";}

	echo "Thank you for your input of $first $last on Director";
}
} 
?>
</p>
<!-- Adding Movie TODO: Genre -->

<div class="form">
<form method="POST" action="" id="movie">
	<h1>Add a movie to database</h1>
	
	<label >Title:</label>
	<input type="text" name="title" maxlength="100"><br/>
	
	<label >Year:</label>
	<input type="text" name="year" maxlength="20"><br/>
	
	<label >Rating:</label>
	<input type="text" name="rating"><br/>
	
	<label >Company:</label>
	<input type="text" name="company" maxlength="50"> <br/>

	<label> Genre: </label>

<?php // getting genres to create checkbox
	$db_connection = mysql_connect("localhost", "cs143", "");
	mysql_select_db("CS143", $db_connection);

	$genre_sql = "SELECT DISTINCT genre
				  FROM MovieGenre";

	$genre_result = mysql_query($genre_sql);

	while ($row=mysql_fetch_row($genre_result))
	{
		echo "<input type='checkbox' name='genre[]' value='$row[0]'> $row[0]";
	}
	echo "<br>";
?>

	<input type="submit" value="Submit" name="submitMovie" />
</form>
</div>

<p>
<?php
if(isset($_POST['submitMovie']))
{
	$db_connection = mysql_connect("localhost", "cs143", "");
	mysql_select_db("CS143", $db_connection);
	
	//get form variables
	$title = $_POST['title'];
	$year = $_POST['year'];
	$rating = $_POST['rating'];
	$company = $_POST['company'];
	$genre = $_POST['genre'];

	if($title == "") {echo "please input movie title <br/>";}
	if($year == "") {echo "please input a year <br/>";}
	if($rating == "") {echo "please input a rating <br/>";}
	if($company == "") {echo "please choose a movie company <br/>";}
	
if( $title != "" && $year != "" && $rating != "" && $company != ""){
	//get max id 
	$query = "select * from MaxMovieID;";
	$result = mysql_query($query, $db_connection);
	$row = mysql_fetch_row($result);
	$id = $row[0];
	$id++;
	
	//update value
	$query = "update MaxMovieID set id=$id"; 
	mysql_query($query, $db_connection);
	
	//insert into Movie
	$query = "insert into Movie values (%s, '%s', '%s', '%s', '%s');";
	$query_to_issue = sprintf($query, $id, mysql_real_escape_string($title), mysql_real_escape_string($year), mysql_real_escape_string($rating), mysql_real_escape_string($company) );
	mysql_query($query_to_issue, $db_connection);


	//$search = mysql_real_escape_string($genre);
	//$words = explode(' ', preg_replace('/\s+/', ' ', $search));

	foreach ($genre as $key) {
		$genre_query = "INSERT INTO MovieGenre
						VALUES ('$id', '$key')";
		$genre_add = mysql_query($genre_query, $db_connection);
	}
		
	mysql_close($db_connection);
	echo "Thank you for your input on Movie";
}
} 
?>
</p>


<!-- Movie Actor form -->
<div class="form">
<form method="POST" action="" id="actorMovie">
	<h1>Add an actor to a movie</h1>
	
	<label >Movie:</label>
	<?php 
		$db_connection = mysql_connect("localhost", "cs143", "");
		mysql_select_db("CS143", $db_connection);

		$movie_sql = "SELECT title
					  FROM Movie
					  ORDER BY title ASC";
		$movie_result = mysql_query($movie_sql, $db_connection);
		echo "<select name='movie'>";
		while($row=mysql_fetch_row($movie_result)) {
			echo "<option value='$row[0]'>".$row[0]."</option>";
		}
		echo "</select> <br>";
	?>

	<label >Actor Name:</label>
	<?php 
		$actor_sql = "SELECT first, last
					  FROM Actor
					  ORDER BY first ASC";
		$actor_result = mysql_query($actor_sql, $db_connection);
		echo "<select name='aname'>";
		while($row=mysql_fetch_row($actor_result)) {
			echo "<option value='$row[0] $row[1]'>".$row[0].' '.$row[1]."</option>";
		}
		echo "</select> <br>";
		mysql_close($db_connection);
	?>
	
	<label >Role:</label>
	<input type="text" name="role"><br/>
	
	<input type="submit" value="Submit" name="submitActorMovie" />
</form>
</div>

<p>
<?php
if(isset($_POST['submitActorMovie']))
{
	$db_connection = mysql_connect("localhost", "cs143", "");
	mysql_select_db("CS143", $db_connection);
	
	//get form variables
	$movie = $_POST['movie'];
	$name = $_POST['aname'];
	$role = $_POST['role'];
	

	if( $role == "")
		echo "please input a role";

	if ($role != "")
	{

	//get movieID
	$searchTerm = "select id from Movie where title='%s';";
	$query = sprintf($searchTerm, mysql_real_escape_string($movie) );
	$result = mysql_query($query, $db_connection);
	$row = mysql_fetch_row($result);
	$mid = $row[0];
	
	//get actorID

	$search = mysql_real_escape_string($name);
	$words = explode(' ', preg_replace('/\s+/', ' ', $search));
	$act_sql = "SELECT id
				FROM Actor
				WHERE first='$words[0]' AND last='$words[1]'";
	$act_result = mysql_query($act_sql);
	$row = mysql_fetch_row($act_result);
	$aid = $row[0];

	//insert into MovieActor
	//echo "movie".$mid.'actor'.$aid;

	$query = "insert into MovieActor values (%s, '%s', '%s');";
	$query_to_issue = sprintf($query, $mid, $aid, mysql_real_escape_string($role) );
	mysql_query($query_to_issue, $db_connection);
	
	if ( mysql_error($db_connection) != "" )
		echo "Sorry input did not go through. The actor and movie must already exist in the database. <br>";
	else
		echo "Thank you for your input on Movie and Actor";
		
	mysql_close($db_connection);
}
} 
?>
</p>

<!-- Movie Director form -->
<div class="form">
<form method="POST" action="" id="directorMovie">
	<h1>Add a director to a movie</h1>
	
	<label >Movie:</label>
	<?php 
		$db_connection = mysql_connect("localhost", "cs143", "");
		mysql_select_db("CS143", $db_connection);

		$movie_sql = "SELECT title
					  FROM Movie
					  ORDER BY title ASC";
		$movie_result = mysql_query($movie_sql, $db_connection);
		echo "<select name='movie'>";
		while($row=mysql_fetch_row($movie_result)) {
			echo "<option value='$row[0]'>".$row[0]."</option>";
		}
		echo "</select> <br>";
	?>
	
	<label >Director Name:</label>
	<?php 
		$dir_sql = "SELECT first, last
					FROM Director
					ORDER BY first ASC";
		$dir_result = mysql_query($dir_sql, $db_connection);
		echo "<select name='dname'>";
		while($row=mysql_fetch_row($dir_result)) {
			echo "<option value='$row[0] $row[1]'>".$row[0].' '.$row[1]."</option>";
		}
		echo "</select> <br>";
		mysql_close($db_connection);
	?>
	<input type="submit" value="Submit" name="subDirMovie" />

</form>
</div>

<p>
<?php
if(isset($_POST['subDirMovie']))
{
	$db_connection = mysql_connect("localhost", "cs143", "");
	mysql_select_db("CS143", $db_connection);
	
	//get form variables
	$movie = $_POST['movie'];
	$name = $_POST['dname'];
	
	//get movieID
	$searchTerm = "select id from Movie where title='%s';";
	$query = sprintf($searchTerm, mysql_real_escape_string($movie) );
	$result = mysql_query($query, $db_connection);
	$row = mysql_fetch_row($result);
	$mid = $row[0];
	
	//get directorID
	$search = mysql_real_escape_string($name);
	$words = explode(' ', preg_replace('/\s+/', ' ', $search));
	$direct_sql = "SELECT id
				   FROM Director
				   WHERE first='$words[0]' AND last='$words[1]'";
	$direct_result = mysql_query($direct_sql);
	$row = mysql_fetch_row($direct_result);
	$did = $row[0];
	
	//insert into MovieActor
	$query = "insert into MovieDirector values (%s, '%s');";
	$query_to_issue = sprintf($query, $mid, $did);
	mysql_query($query_to_issue, $db_connection);
	
	//echo $words[0].$words[1].'director'.$did.'movie'.$mid;
	
	if ( mysql_error($db_connection) != "" )
		echo "Sorry input did not go through. The director and movie must already exist in the database. <br>";
	else
		echo "Thank you for your input on Movie and director";
		
	mysql_close($db_connection);
} 
?>
</p>

</body>