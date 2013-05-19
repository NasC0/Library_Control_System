<?php
include_once 'includes/functions.php';
if (isset($_GET['submit'])) {
	returnBook($_GET['book-id'], $_GET['date']);
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Book Return</title>
<link href="indexStyles.css" rel="stylesheet" />
</head>
<body>
	<?php
	include_once "menu.html";
	?>
	<section>
		<header>
			<h3>Book Return</h3>
		</header>
		<form mehtod="get" action="<?php $_PHP_SELF ?>">
			<label for="book-id">Book ID: <input type="text" id="book-id"
				name="book-id" />
			</label> <label for="date">Return Date: <input type="date" id="date"
				name="date" />
			</label> <input type="submit" name="submit" value="Submit" />
		</form>
	</section>
</body>
</html>
