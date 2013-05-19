<?php

// Connects to the MySQL database with default credentials and returns the connection
// for use in further functions
function dbConnect($server = "localhost", $user = "Admin", $pass = "314159qqww", $db = "library") {
	$connection = mysqli_connect($server, $user, $pass, $db) or trigger_error("Could not connect to database!" . mysqli_error());
	return $connection;
}

// Registers a reader
function registerReader($id, $username, $address, $phone) {

	$connection = dbConnect();

	// Checks to see if the username, email and phone are empty.
	// If they are, triggers an error and doesn't enter the data
	if (empty($username) || empty($address) || empty($phone)) {
		die("Could not enter data!" . mysqli_errno());
	}

	$sql = 'INSERT INTO reader' .
			'(ID, Name, Address, PhoneNum)' .
			"VALUES ('$id', '$username', '$address', '$phone')";


	$retval = mysqli_query($connection, $sql) or trigger_error("Could not enter data!" . mysqli_error());

	echo '<div id="message"><p>Entered data succesfully!</p></div>';
	mysqli_close($connection);
}

// Registers a book
function registerBook($id, $title, $author, $edition, $status = 0) {

	$connection = dbConnect();

	// Checks if the title, author or edition are empty.
	// If they are, don't enter the data
	if (empty($title) || empty($author) || empty($edition)) {
		die("You must enter valid data!");
	}

	$sql = 'INSERT INTO books' .
			'(ID, title, author, edition, status)' .
			"VALUES ('$id', '$title', '$author', '$edition', '$status')";

	$retval = mysqli_query($connection, $sql);

	if (! $retval) {
		die("Could not enter data: " . mysqli_error());
	}

	echo '<div id="message"><p>Entered data succesfully!</p></div>';

	mysqli_close($connection);
}

// Returns a borrowed book
function returnBook($id, $date) {
	$connect = dbConnect();

	$book_id = trim( (string) $id);
	$query = "SELECT status FROM books where ID = '$book_id'";
	$result = mysqli_query($connect, $query);
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$status = $line['status'];

	// Checks if the books isn't already available
	if ($status == 1) {
		die("This book is already available!");
	}
	// Otherwise sets the book status to available
	else {
		$query = "UPDATE books
		SET status = 1
		WHERE ID = '$book_id'";

		mysqli_query($connect, $query);
	}

	// The query used to check for the latest entry of the borrowed book
	// Latest = latest entry for the borrowed book in the table holding
	// the borrowed books data
	$query = "SELECT returned FROM borrowed_books where book_id = '$book_id' ORDER BY ID DESC LIMIT 1";

	// Enters the return book data.
	// If no date specified, uses the current date
	if (empty($date)) {
		$now = strtotime("now");
		$result = mysqli_query($connect, $query);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$returned = strtotime($line['returned']);

		// Checks if the book is returned before the scheduled return date
		if ($returned >= $now) {
			echo '<div id="message"><p>No penalty for this book!</p>';
		}
		// If it's not, calculates the penalty
		else {
			$datediff = $now - $returned;
			$days = ceil($datediff/(60*60*24));
			$days *= 1;
			echo '<div id="message"><p>The penalty for this book is: ' . $days . '</p>';
		}

		// Updates the borrowed books table with
		// the return of the book
		$query = "UPDATE borrowed_books
		SET actual_return = CURDATE()
		where book_id = '$book_id'
		ORDER BY ID DESC
		LIMIT 1";

		mysqli_query($connect, $query);
	}
	// Uses the specified return date, otherwise the same as the above IF statement
	else {
		$result = mysqli_query($connect, $query);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$returned = $line['returned'];

		if (strtotime($date) <= strtotime($returned)) {
			echo '<div id="message"><p>No penalty for this book!</p>';
		}

		else {
			$datestr = strtotime($date);
			$returned = strtotime($returned);
			$datediff = $datestr - $returned;
			$days = ceil($datediff / (60*60*24));
			echo '<div id="message"><p>The penalty for this book is: ' . $days . '</p>';
		}

		$query = "UPDATE borrowed_books
		SET actual_return = '$date'
		where book_id = '$book_id'
		ORDER BY ID DESC
		LIMIT 1";

		mysqli_query($connect, $query);

	}


	echo '<p>Entered data succesfully!</p></div>';
	mysqli_close($connect);
}

// Borrows a book
function borrowBook($bookID, $readerID, $borrowed, $return) {
	$connection = dbConnect();

	$book_id = trim( (string) $bookID);

	$query = "SELECT ID, status FROM books";
	$result = mysqli_query($connection, $query);
	$flag = FALSE;

	//Checks to see if the book exists and its status
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$id = $row["ID"];
		$status = $row["status"];

		if ($book_id == $id) {
			
			if ($status == 0) {
				die("This book is not available!");
			}
			
			$flag = TRUE;
		}
	}

	// If book doesn't exist, terminate the operation
	if ($flag == FALSE) {
		die("No book by this ID!");
	}

	// Checks to see if the specified reader exists
	$reader_id = trim( (string) $readerID);

	$query = 'SELECT ID FROM reader';
	$result = mysqli_query($connection, $query);
	$flag = FALSE;

	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)){
		
		$id = $row[0];
		
		if ($reader_id == $id) {
			$flag = TRUE;
		}
	}

	// If reader doesn't exist, terminates the operation
	if ($flag == FALSE) {
		die("No reader by this ID!");
	}

	// Validates the date (the date of borrowing must be lower than the date of return)
	if (strtotime($return) < strtotime($borrowed)) {
		die("The return date must be higher than the borrow date!");
	}
	// Otherwise check to see if the new date of borrowing is higher than the old date of return
	// ID of the borrowed_books table is used to indicate the latest borrowing event of the specified book
	else {
		$query = "SELECT actual_return
		FROM borrowed_books
		WHERE book_id = '$book_id'
		ORDER BY ID desc
		LIMIT 1";

		$result = mysqli_query($connection, $query);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Terminates the operation if the dates are invalid
		if (strtotime($borrowed) < strtotime($line['actual_return'])) {
			die("The new borrow date must not be lower than the old return date!");
		}

		// otherwise enters the borrowing data in the borrowed_books table
		// and updates the status of the book to unavailable
		$sql = 'INSERT INTO borrowed_books' .
				'(book_id, reader_id, borrowed, returned)' .
				"VALUES ('$book_id', '$reader_id', '$borrowed', '$return')";

		$return_val = mysqli_query($connection, $sql);

		if (! $return_val) {
			die("Could not enter data!" . mysqli_error($connection));
		}

		$query = "UPDATE books
		SET status = 0
		WHERE ID = '$book_id'";

		mysqli_query($connection, $query);

		echo '<div id="message"><p>Entered data succesfully!</p></div>';

		mysqli_close($connection);
	}
}

// Prints all the registered users in table format
function readerTable() {
	$connection = dbConnect();

	echo '<div id="php-table"><table><thead><th><td>ID</td><td>Name</td><td>Address</td><td>Phone</td></th></thead>';
	echo "<tbody>";

	$query = "SELECT * FROM reader";

	$result = mysqli_query($connection, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM))
	{
		$id = $row[0];
		$name = $row[1];
		$address = $row[2];
		$number = $row[3];

		echo "<tr><td></td><td>$id</td><td>$name</td><td>$address</td><td>$number</td></tr>";
	}

	echo "</tbody></table></div>";

	mysqli_close($connection);
}

// Prints all books in table format
function bookTable() {
	$connection = dbConnect();

	echo '<div id="php-table"><table><thead><th><td>ID</td><td>Title</td><td>Author</td><td>Edition</td><td>Status</td></th></thead>';
	echo "<tbody>";

	$query = "SELECT * FROM books";

	$result = mysqli_query($connection, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM))
	{
		$id = $row[0];
		$title = $row[1];
		$author = $row[2];
		$edition = $row[3];
		$status = $row[4];

		echo "<tr><td></td><td>$id</td><td>$title</td><td>$author</td><td>$edition</td><td>$status</td></tr>";
	}

	echo "</tbody></table></div>";

	mysqli_close($connection);
}

// Prints the borrowed books information in table format
function borrowedTable() {
	$connection = dbConnect();

	echo '<div id="borrowed-table"><table><thead><th><td>ID</td><td>Book ID</td><td>Reader ID</td><td>Borrowed</td><td>Return</td><td>Actual Return</td></th></thead>';
	echo "<tbody>";

	$query = "SELECT * FROM borrowed_books";

	$result = mysqli_query($connection, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM))
	{
		$id = $row[0];
		$book_id = $row[1];
		$reader_id = $row[2];
		$borrowed = $row[3];
		$return = $row[4];
		$actual_return = $row[5];

		echo "<tr><td></td><td>$id</td><td>$book_id</td><td>$reader_id</td><td>$borrowed</td><td>$return</td><td>$actual_return</td></tr>";
	}

	echo "</tbody></table></div>";

	mysqli_close($connection);
}

// Keeps track of the overdue books for a specific reader
function delayedReader($reader_id) {
	$connect = dbConnect();

	$query = "SELECT book_id, returned, actual_return
	from borrowed_books
	where reader_id = $reader_id";
	$books = array();
	$return = array();
	$count = 0;

	// Checks which of the books for the specific reader are not returned
	// Saves the return and book_id information in seperate arrays
	// Used later for representation of the data
	$result = mysqli_query($connect, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		if (strtotime($row['returned']) < strtotime('now') && is_null($row['actual_return'])) {
			$return[$count] = $row['returned'];
			$books[$count] = $row['book_id'];
			$count++;
		}
	}

	mysqli_close($connect);

	// Returns the books and return information for data representation
	$arrays = array($books,
			$return);

	return $arrays;

}

// Prints information for the delayed books for the whole library
// And for a specific user
// Accepts an array of arrays (book-id and return date arrays for each book)
function delayedTable($array) {
	$connect = dbConnect();

	echo '<div id="borrowed-table"><table><thead<tr><th>ID</th><th>Title</th><th>Author</th><th>edition</th><th>Delay in days</th></tr></thead>';
	echo "<tbody>";
	for ($i=0; $i < count($array[0]); $i++) {
		$book_id = $array[0][$i];
		$query = "SELECT *
		FROM books
		WHERE ID = '$book_id'";

		$result = mysqli_query($connect, $query);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);

		$datediff = strtotime('now') - strtotime($array[1][$i]);
		$days = ceil($datediff/(60*60*24));
		$id = $line['ID'];
		$title = $line['title'];
		$author = $line['author'];
		$edition = $line['edition'];

		echo "<tr><td>$id</td><td>$title</td><td>$author</td><td>$edition</td><td>$days</td></tr>";
	}

	echo "</tbody></table></div>";

	mysqli_close($connect);
}

// Keeps track of the overdue books for the whole library
function libraryDelay() {
	$connect = dbConnect();

	$query = "SELECT book_id, returned, actual_return
			from borrowed_books";
	$books = array();
	$return = array();
	$count = 0;

	// Same as the delayedReader function, except that it doesn't target a specific reader
	$result = mysqli_query($connect, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		if (strtotime($row['returned']) < strtotime('now') && is_null($row['actual_return'])) {
			$return[$count] = $row['returned'];
			$books[$count] = $row['book_id'];
			$count++;
		}
	}

	$arrays = array($books,
			$return);

	return $arrays;
}

//Calculates the penalty for the given day, and then returns the value and MySQL db connection
//in a two-dimensional array for use in the table representation of the data.
function currentPenalty($date) {	
	$connect = dbConnect();

	$count = 0;

	//First check for the penalty of the already returned books
	$query = "SELECT *
	FROM borrowed_books
	WHERE returned < '$date' AND '$date' <= actual_return";
	$result = mysqli_query($connect, $query);

	// Gets the amount of delayed books, which is used for basis of the penalty fee
	// Each row represents a delayed book. The basis for the fee is 1 lev per book per day
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$count++;
	}

	//Then see if there are books overdue for returning, but are not yet returned
	$query = "SELECT *
	FROM borrowed_books
	WHERE returned < '$date' AND isnull(actual_return)";

	$result = mysqli_query($connect, $query);

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$count++;
	}


	// Then return the penalty and the MySQL connection for use in the data representation
	$returns = array($count,
			$connect);

	return $returns;
}

//Calculates the penalty for a time period
function periodPenalty($from, $to) {
	
	// If $from and $to are equal, call the currentPenalty function
	if ($from == $to) {
		$values = currentPenalty($from);
		return $values;
	}
	// Otherwise check for the period penalty
	else {
		$connect = dbConnect();

		$count = 0;

		$date = $from;

		// Keeps track of the $date increment
		while ($date <= $to) {
			
			// First check for the penalty of the already returned books
			$query = "SELECT *
			FROM borrowed_books
			WHERE returned < '$date' AND '$date' <= actual_return";
			$result = mysqli_query($connect, $query);

			// Gets the amount of delayed books, which is used for basis of the penalty fee
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$count++;
			}

			// Then check for the penalty of the yet unreturned books
			$query = "SELECT *
			FROM borrowed_books
			WHERE returned < '$date' AND isnull(actual_return)";
			$result = mysqli_query($connect, $query);

			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$count++;
			}

			// Increment the date by 1 day until it reaches the $to date
			$date = strtotime('+1 day', strtotime($date));
			$date = date("Y-m-d", $date);
		}

		// Returns the penalty ammount and MySQL connection for use in the data representation
		$returns = array($count,
				$connect);

		return $returns;
	}
}

// Displays the read books for a specific reader for a period of time
function readerInfo($id, $from, $to) {
	$connect = dbConnect();

	$readerID = trim((string)$id);

	// Checks to see if the entered period is negative ($to date lower than the $from date)
	if (strtotime($to) < strtotime($from)) {
		die("The period must not be negative!");
	}

	// Then gets the book_id for the borrowed books in the
	// given time period
	$query = sprintf("SELECT book_id
			FROM borrowed_books
			WHERE reader_id = '%s'
			AND DATE(borrowed) between '%s' and '%s'",
			mysql_real_escape_string($readerID),
			mysql_real_escape_string($from),
			mysql_real_escape_string($to));

	$result = mysqli_query($connect, $query);
	
	// Save the book_ids in a separate array
	$books_id = array();
	$count = 0;
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$books_id[$count] = $row['book_id'];
		$count++;
	}

	// Returns the book_id array and the MySQL connection for later use in the
	// data representation
	$returns = array($books_id,
			$connect);

	return $returns;
}

// Prints the reader information in a table form
// Accepts an array of the book_id and the connection returned
// from the readerInfo() function
function readerInfoTable($array) {
	echo '<div id="borrowed-table"><table><thead><tr><th>ID</th><th>Title</th><th>Author</th><th>Edition</th><th>Status</th></tr></thead>';
	echo "<tbody>";

	$books_id = $array[0];
	$connect = $array[1];

	// Gets information for each book based on the book_id parsed in
	// from the readerInfo() return array.
	for ($i=0; $i < count($books_id); $i++) {
		$query = "SELECT *
		FROM books
		WHERE ID = '$books_id[$i]'";
		$result = mysqli_query($connect, $query);
		$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$id = $line['ID'];
		$title = $line['title'];
		$author = $line['author'];
		$edition = $line['edition'];
		$status = $line['status'];

		echo "<tr><td>$id</td><td>$title</td><td>$author</td><td>$edition</td><td>$status</td></tr>";
	}

	echo "</tbody></table></div>";

	mysqli_close($connect);
}

?>