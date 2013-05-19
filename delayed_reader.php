<?php

include_once 'includes/functions.php';
if (isset($_GET['submit'])) {
	$books = delayedReader($_GET['reader-id']);
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Reader Delay</title>
<link href="indexStyles.css" rel="stylesheet" />
</head>
<body>
	<?php
	include_once "menu.html";
	?>

	<section>
		<header>
			<h3>Reader delayed books</h3>
		</header>
		<form method="get" action="<?php $_PHP_SELF ?>">
			<label for="reader-id">Reader ID: <input type="text" id="reader-id"
				name="reader-id" />
			</label> <input type="submit" value="Submit" name="submit" />
		</form>
	</section>
	<?php
	if (isset($_GET['submit'])) {
					delayedTable($books);
		}
		?>
</body>
</html>
