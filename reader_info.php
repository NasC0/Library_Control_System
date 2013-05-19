<?php
include_once 'includes/functions.php';
if (isset($_POST['submit'])) {

	$values = readerInfo($_POST['reader-id'], $_POST['from'], $_POST['to']);
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Reader Info</title>
<link href="indexStyles.css" rel="stylesheet" />
</head>
<body>
	<?php
	include_once "menu.html";
	?>
	<section>
		<header>
			<h3>Reader Info</h3>
		</header>

		<form method="post" action="<?php $_PHP_SELF ?>">
			<label for="reader-id">Reader ID: <input type="text" id="reader-id"
				name="reader-id" />
			</label> <label for="borrowed">From date: <input type="date"
				id="borrowed" name="from" />
			</label> <label for="return">To date: <input type="date" id="return"
				name="to" />
			</label> <input type="submit" value="Submit" name="submit" />
		</form>
	</section>
	<?php
	if(isset($_POST['submit'])) {
		readerInfoTable($values);
    }
    ?>
</body>
</html>
