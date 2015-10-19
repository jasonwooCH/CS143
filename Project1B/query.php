<html>
<body>

<h1>Our 1B Web Query Interface</h1> <br>
Type an SQL query in the following box: <br>

<br>

<form method="GET" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<TEXTAREA NAME="fquery" ROWS=8 COLS=60>
			SELECT * FROM Actor WHERE id=10;
		</TEXTAREA>
		<input type="submit">
</form>

<?php
	$query = $_GET["fquery"];
	if (empty($query)) {return;}

	$squery = preg_replace('/\s+/', ' ', $query);

	$db_connection = mysql_connect("localhost", "cs143", "");
	mysql_select_db("CS143", $db_connection);

	$sanitized_query = mysql_real_escape_string($squery, $db_connection);

	$result = mysql_query($sanitized_query, $db_connection);

	if (!$result)
	{
		echo 'Could not run query: ' . mysql_error();
		exit;
	}

	// mysql_num_fields(); - number of attributes in a query

	while($row = mysql_fetch_row($result)) 
	{
		$id = $row[0];
	    $last = $row[1];
	    $first = $row[2];
	    $sex = $row[3];
	    $dob = $row[4];
	    $dod = $row[5];
	    print "$id, $last, $first, $sex, $dob, $dod<br />";
	}

	mysql_close($db_connection);

?>

</body>
</html>