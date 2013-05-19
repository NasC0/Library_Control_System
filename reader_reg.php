<?php 
	include_once 'includes/functions.php';
    if (isset($_POST['submit'])) {
    	registerReader($_POST['reader-number'], $_POST['username'], $_POST['address'], $_POST['tel-number']);
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

    <section id="reader-registration">
        <header>
            <h3>Reader Registration</h3>
        </header>
        <form method="post" action="<?php $_PHP_SELF ?>">
            <label for="number">Reader number: <input type="text" id="number" name="reader-number"/></label>
            <label for="username">Username: <input type="text" id="username" name="username"/></label>
            <label for="address">Address: <input type="text" id="address" name="address"/></label>
            <label for="phone">Phone number: <input type="tel" id="phone" name="tel-number"/></label>
            <input type="submit" value="Submit registration" name="submit"/>
        </form>
    </section>
<?php 
	readerTable();
?>
</body>
</html>
