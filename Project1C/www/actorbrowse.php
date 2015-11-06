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

	if(isset($_GET["aid"]))
	{
		$aid = $_GET["aid"];
	}
	else
	{
		$aid = 52794; // default = julia roberts
	}

	$db_connection = mysql_connect("localhost", "cs143", "");
	mysql_select_db("CS143", $db_connection);

	$actor_sql = "SELECT first, last, sex, dob, dod
				  FROM Actor
				  WHERE id = '$aid'";

	$actor_result = mysql_query($actor_sql, $db_connection);
	$row = mysql_fetch_row($actor_result);
	
	echo "<h2> Actor Information </h2>";
	echo "Name: ".$row[0]." ".$row[1]."<br>";
	echo "Sex: ".$row[2]."<br>";
	echo "Date of Birth: ".$row[3]."<br>";
	echo "Date of Death: ";
	if (empty($row[4]))
		echo "N/A <br>";
	else
		echo $row[4]."<br>";

	echo "<h2> Acted in </h2>";

	$acted_sql = "SELECT MA.role, M.title, M.id
				  FROM Movie M, MovieActor MA
				  WHERE MA.aid = '$aid' AND MA.mid = M.id";

	$acted_result = mysql_query($acted_sql, $db_connection);
	while($row = mysql_fetch_row($acted_result))
	{
		echo "Act '$row[0]' in ";
		echo "<a href='moviebrowse.php?mid=".$row[2]."'>".$row[1]."</a> <br>"; 
	}

	mysql_close($db_connection);
?>