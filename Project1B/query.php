<html>
<body>

<h1>Brendan and Chul Hee's Web Query Interface</h1>
Type an SQL query in the following box: <br>
(Example is given as a default)
<br>
<br>

<form method="GET" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<TEXTAREA NAME="fquery" ROWS=8 COLS=60>SELECT * FROM Actor WHERE id < 100;</TEXTAREA> <br>
		<input type="submit">
</form>

<?php
	$query = $_GET["fquery"];
	if (empty($query)) {return;}

	$squery = preg_replace('/\s+/', ' ', $query);

	$db_connection = mysql_connect("localhost", "cs143", "");
	mysql_select_db("CS143", $db_connection);

	$sanitized = ($squery);
	$result = mysql_query($sanitized, $db_connection);

	//$result = mysql_query($squery,$db_connection);

	if (!$result)
	{
		echo 'Could not run query: ' . mysql_error();
		exit;
	}

	if (is_resource($result)) // SELECT Query
	{

		echo "<table border='1'>";

	    $row = mysql_fetch_object($result);
	    echo "<tr>";
	    foreach($row as $cname => $cvalue) {
				//if (empty($cvalue)) $cvalue = 'N/A';
			print "<td><b>".$cname."</b></td>";
		}
		echo "</tr>";
		
		echo "<tr>";
		foreach($row as $cname => $cvalue) {
			if (empty($cvalue)) $cvalue = 'N/A';
			print "<td>".$cvalue."</td>";
		}
		echo "</tr>";

		while($row = mysql_fetch_object($result)) 
		{
			echo "<tr>";
			foreach($row as $cname => $cvalue) {
				if (empty($cvalue)) $cvalue = 'N/A';
				print "<td>".$cvalue."</td>";
			}
			echo "</tr>";   
		}
		echo "</table";
	}

	mysql_close($db_connection);

?>

</body>
</html>