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
<a href=input.php> Click here to Add to our Database! </a>
</body>
</html>

<?php

	if(isset($_GET["mid"]))
	{
		$mid = $_GET["mid"];
	}
	else
	{
		$mid = 1074; // default = Die Another Day
	}

	$db_connection = mysql_connect("localhost", "cs143", "");
	mysql_select_db("CS143", $db_connection);

	$movie_sql = "SELECT title, year, rating, company 
				  FROM Movie
				  WHERE id = '$mid'";

	$movie_result = mysql_query($movie_sql, $db_connection);
	$row = mysql_fetch_row($movie_result);
	
	echo "<h2> Movie Information </h2>";
	echo "Title: ".$row[0]."(".$row[1].")<br>";
	echo "MPAA Rating: ".$row[2]."<br>";
	echo "Company: ".$row[3]."<br>";

	$genre_sql = "SELECT genre
				  FROM MovieGenre
				  WHERE mid = '$mid'";
	$genre_result = mysql_query($genre_sql,$db_connection);
	echo "Genre: ";
	while ($row = mysql_fetch_row($genre_result)) {
		echo $row[0]." ";
	} echo "<br>";

	$dir_sql = "SELECT D.first, D.last
			    FROM Director D, MovieDirector MD
			    WHERE MD.mid = '$mid' AND MD.did = D.id";
	$dir_result = mysql_query($dir_sql, $db_connection);
	echo "Director(s): ";
	while ($row = mysql_fetch_row($dir_result)) {
		echo $row[0]." ".$row[1]." ";
	} 



	echo "<h2> Actors in the movie </h2>";
	$actors_sql = "SELECT A.first, A.last, MA.role, A.id
				   FROM Actor A, MovieActor MA
				   WHERE MA.mid = '$mid' AND MA.aid = A.id";

	$actors_result = mysql_query($actors_sql, $db_connection);
	while($row = mysql_fetch_row($actors_result))
	{
		echo "<a href='actorbrowse.php?aid=".$row[3]."'>".$row[0]." ".$row[1]."</a> acted as '$row[2]' <br>";
	}


	echo "<h2> User Review </h2>";

	$average_sql = "SELECT AVG(rating), COUNT(*)
				    FROM Review
				    WHERE mid = '$mid'";
	$average_result = mysql_query($average_sql, $db_connection);
	$row = mysql_fetch_row($average_result);
	echo "<h4> Average Score: ".$row[0]."/5 by ".$row[1]." review(s) </h4>";

	echo "<a href='review.php?mid=".$mid."'> Make a Review </a> <br>"; 

	echo "<strong>All Reviews: </strong><br><br>";

	$review_sql = "SELECT *
				   FROM Review
				   WHERE mid='$mid'";

	$review_result = mysql_query($review_sql, $db_connection);

	while($row = mysql_fetch_row($review_result))
	{
		echo $row[3]."/5 by ".$row[0]."<br>";
		echo "on ".$row[1]."<br>";
		echo $row[4]."<br><br>";
	}

	mysql_close($db_connection);
?>