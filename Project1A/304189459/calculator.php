<html>
<body>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<h1> Chul Hee's PHP Calculator </h1> <br>
	Type a mathematical expression in the following box <br><br>
	<input type="text" name="fexpr">
	<input type="submit" value="Calculate">
</form>

<?php
$valid = '/[^-\.*+\/0-9\s]/';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$expr = $_REQUEST['fexpr'];
	if (empty($expr)) {return;}
	
	echo "<h2> RESULT </h2>"; 

	if (preg_match($valid, $expr) || preg_match('/[-+*\/][+*\/]+/', $expr) ||
		preg_match('/[\-][\-][\-]+/', $expr)) {
			echo "Invalid Characters";
	}
	else {
		if (preg_match('/[0-9]\/0(?![\.0-9])/', $expr)) {
			echo "Division by zero"; return;
		}

		$nexpr = preg_replace('/[\-][\-]/', '+', $expr);	
		$result = eval("return ($nexpr);");

		if (!$result && $result !== 0) {
			echo "Invalid Expression";
			return;
		}

		$clean = preg_replace('/\s+/','', $expr);
		echo $clean." = ".$result;
	}
}
?>

</body>
</html>