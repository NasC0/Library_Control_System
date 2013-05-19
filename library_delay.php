<?php
include_once 'includes/functions.php';

$arrays = libraryDelay();


?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Library Delay</title>
<link href="indexStyles.css" rel="stylesheet" />
</head>
<body>
	<?php
	include_once "menu.html";
	?>

	<section>
		<header>
			<h3>Library delayed books</h3>
		</header>
		<?php

		delayedTable($arrays);

		?>
	</section>
</body>
</html>
