<?php 
header('Content-type: application/json');
require('../php/database_connect.php');

// close session to allow for parallel requests
session_start();
session_write_close();

$filename = 'NPCs.txt';
$userIds = file($filename, FILE_IGNORE_NEW_LINES);

foreach ($userIds as $userId)
{
	$user = mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE id='" . $userId . "';"));
	
	$query = 'DELETE FROM Active_exchanges WHERE requesterNetId="'. $user['netId'] . '" OR associatedExchanges LIKE "%'. $user['netId'].'%"';
	$query_result = mysql_query($query);
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	$query = 'DELETE FROM Users WHERE id="'. $userId . '"';
	$query_result = mysql_query($query);
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}

// clear NPC list
$handle = fopen ($filename, "w+");
fclose($handle);

mysql_close($connection);
 
?>