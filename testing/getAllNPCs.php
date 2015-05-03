<?php 
header('Content-type: application/json');
require('../php/database_connect.php');

// close session to allow for parallel requests
session_write_close();

$filename = 'NPCs.txt';
$userIds = file($filename, FILE_IGNORE_NEW_LINES);

foreach ($userIds as $userId)
{
	$user = mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE id='" . $userId . "';"));
	
	echo '<div class="col-md-3">' . $user['netId'] . '</div>';
	echo '<div class="col-md-3">' . $user['firstName'] . '</div>';
	echo '<div class="col-md-3">' . $user['lastName'] . '</div>';
	echo '<div class="col-md-3">' . $user['reputation'] . '</div>';
}

mysql_close($connection);
 
?>