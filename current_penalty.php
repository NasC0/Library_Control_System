<?php
include_once 'includes/functions.php';

if (isset($_GET['submit'])) {

	$values = currentPenalty($_GET['day']);

}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Current Penalty</title>
<link href="indexStyles.css" rel="stylesheet" />
</head>
<body>
	<?php
	include_once "menu.html";
	?>
	<section>
		<header>
			<h3>
				Day penalty
				<?php 
				if (isset($_GET['submit'])) {
                        echo ": " . $values[0];

                        mysqli_close($values[1]);
                    }
                    ?>
			</h3>
		</header>
		<form method="get" action="<?php $_PHP_SELF ?>">
			<label for="day">Select date: <input type="date" id="day" name="day" />
			</label> <input type="submit" value="Submit" name="submit" />
		</form>
	</section>
</body>
</html>
