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

	$mid = $_GET['mid'];

	$movie_sql = "SELECT title
				  FROM Movie
				  WHERE id = '$mid'";

	$db_connection = mysql_connect("localhost", "cs143", "");
	mysql_select_db("CS143", $db_connection);

	$movie_result = mysql_query($movie_sql, $db_connection);
	$row = mysql_fetch_row($movie_result); 

	mysql_close($db_connection);

?>

<html>
<body>
		<form method="POST" action="newreview.php?mid=<?php echo $mid ?>" name="reviewpg">
			Movie: <?php echo $row[0] ?> <br>
			Your Name: <input type="text" name="reviewer"> <br>
			Rating: <input type="number" name="score" min="1" max="5"> <br>
			Comment: <br>
			<TEXTAREA NAME="comment" ROWS=8 COLS=60></textarea> <br>
			<input type="submit" value="Submit" name="submit">
		</form>
</body>
</html>

<?php
	if ($_GET['message'] == 'empty')
		echo "<h3>Please fill out both your name and the score</h3>";

	if ($_GET['message'] == 'error')
		echo "<h3> There was a syntax error </h3>";
?>