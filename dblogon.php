<?php
    //open connection to mysql db
    $connection = mysqli_connect("localhost","","","tvs") or die("Error " . mysqli_error($connection));

	// Use utf8 character set
	mysqli_set_charset ($connection, 'utf8mb4') or die('Could not set charset');
?>
