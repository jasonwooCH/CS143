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
<a href=input.php> Click here to Add to our Database! </a><br><br>
</body>
</html>

<?php

	if(!isset($_POST['search']) or empty($_POST['search'])) {
		header("Location:index.php");
	}

	$db_connection = mysql_connect("localhost", "cs143", "");
	mysql_select_db("CS143", $db_connection);

	// SEARCH QUERY BUILDING
	$search = mysql_real_escape_string($_POST['search']);
	$words = explode(' ', preg_replace('/\s+/', ' ', $search));
	$names = array();
	foreach ($words as $key) {
		$names[] = "first LIKE '$key%' OR last LIKE '$key%'";
	}

	$actor_search = implode(') AND (', $names);

	$actor_sql = "SELECT first, last, dob, id 
				  FROM Actor 
				  WHERE (".$actor_search.");";
	//echo $actor_sql."<br>";

	echo 'Results from the Actor Database: <br>';

	$actor_result = mysql_query($actor_sql, $db_connection);
	while($row = mysql_fetch_row($actor_result))
	{
		echo "Actor: <a href='actorbrowse.php?aid=".$row[3]."'>". $row[0]." ".$row[1]. "(" . $row[2]. ")</a>" ;
		echo "<br>";
	}
	echo "<br>";

	$movie_search = $search;
	$movie_sql = "SELECT title, year, id 
				  FROM Movie 
				  WHERE title LIKE '%$movie_search%';";

	echo 'Results from the Movie Database: <br>';
	$movie_result = mysql_query($movie_sql, $db_connection);
	while($row = mysql_fetch_row($movie_result))
	{
		echo "Movie: <a href='moviebrowse.php?mid=".$row[2]."'>". $row[0]. "(" . $row[1]. ")</a>" ;
		echo "<br>";
	}

	mysql_close($db_connection);

?>