<?php 
include_once 'includes/functions.php';
if (isset($_POST['submit'])) {

	if ($_POST["status"] == "available") {
		registerBook($_POST['inv-number'], $_POST['book-title'], $_POST['book-author'], $_POST['edition'], 1);
	}
	else {
		registerBook($_POST['inv-number'], $_POST['book-title'], $_POST['book-author'], $_POST['edition']);
	}
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Reader registration</title>
<link href="indexStyles.css" rel="stylesheet" />
</head>
<?php
include_once "menu.html";
?>

<section>
	<header>
		<h3>Book Registration</h3>
	</header>
	<form method="post" action="<?php $_PHP_SELF ?>">
		<label for="inv-number">Inventory number: <input type="text"
			id="inv-number" name="inv-number" />
		</label> <label for="title">Title: <input type="text" id="title"
			name="book-title" />
		</label> <label for="author">Author: <input type="text" id="author"
			name="book-author" />
		</label> <label for="edition">Year of edition: <input type="datetime"
			id="edition" name="edition" />
		</label> <label for="status">Status: <input type="radio" name="status"
			value="available" id="available" /><label for="available">Available</label>
			<input type="radio" name="status" value="borrowed" id="borrowed" /><label
			for="borrowed">Borrowed</label>
		</label> <input type="submit" value="Submit registration"
			name="submit" />
	</form>
</section>
<?php 
bookTable();
?>
</body>
</html>
