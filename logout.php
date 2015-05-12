<?php
	 /* Connect to the database. */
	 include_once('php/database_connect.php');

	 /* Identify who is currently logged in. Log out by setting session ID to NULL. */
	 if (isUserLoggedIn()) {
	 	$logoutQ = 'UPDATE Users SET session_id="NULL" WHERE netId="' . $_SESSION['user']['netId'] . '";';
	 	mysql_query($logoutQ);
	 }

	 /* Redirect to home page. */
	 header('Location: index.php');
?>