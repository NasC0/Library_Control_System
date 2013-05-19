<?php
include_once 'includes/functions.php';
if (isset($_POST['submit'])) {
	borrowBook($_POST['book-id'], $_POST['reader-id'], $_POST['borrowed'], $_POST['return']);
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Book borrowing</title>
<link href="indexStyles.css" rel="stylesheet" />
</head>
<body>
	<?php 
	include_once "menu.html";
	?>
	<section id="book-borrowing">
		<header>
			<h3>Book Borrowing</h3>
		</header>

		<form method="post" action="<?php $_PHP_SELF ?>">
			<label for="book-id">Book ID: <input type="text" id="book-id"
				name="book-id" />
			</label> <label for="reader-id">Reader ID: <input type="text"
				id="reader-id" name="reader-id" />
			</label> <label for="borrowed">Date of borrowing: <input type="date"
				id="borrowed" name="borrowed" />
			</label> <label for="return">Date of return: <input type="date"
				id="return" name="return" />
			</label> <input type="submit" value="Submit" name="submit" />
		</form>
	</section>
	<?php 

	borrowedTable();

	?>
</body>
</html>
