<?php
include_once 'includes/functions.php';
if (isset($_GET['submit'])) {
	 
	$values = periodPenalty($_GET['from'], $_GET['to']);
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Period Penalty</title>
<link href="indexStyles.css" rel="stylesheet" />
</head>
<body>
	<?php
	include_once "menu.html";
	?>
	<section>
		<header>
			<h3>
				Period penalty
				<?php 
				if (isset($_GET['submit'])) {
                        echo ": " . $values[0];

                        mysqli_close($values[1]);
                    }
                    ?>
			</h3>
		</header>
		<form method="get" action="<?php $_PHP_SELF ?>">
			<label for="from">From: <input type="date" id="from" name="from" />
			</label> <label for="to">To: <input type="date" id="to" name="to" />
			</label> <input type="submit" value="Submit" name="submit" />
		</form>
	</section>
</body>
</html>
