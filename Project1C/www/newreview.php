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

	if (empty($_POST['reviewer']) or empty($_POST['score']))
	{
		header("Location: review.php?mid=".$mid."&message=empty");
	}
	else {
		$db_connection = mysql_connect("localhost", "cs143", "");
		mysql_select_db("CS143", $db_connection);

		$name = mysql_real_escape_string($_POST['reviewer']);
		$rate = $_POST['score'];
		$comment = $_POST['comment'];

		$reviewsql = "INSERT INTO Review 
					  VALUES('$name', 
					  		 NOW(),
					  		 '$mid',
					  		 '$rate',
					  		 '$comment')";

		$result = mysql_query($reviewsql, $db_connection);

		if(!$result)
		{
			header("Location: review.php?mid=".$mid."&message=error");
		}

		echo "Thanks for your Review! <br>";
		echo "<a href='moviebrowse.php?mid=".$mid."'> Back to the movie information </a>";

		mysql_close($db_connection);
 	}

?>