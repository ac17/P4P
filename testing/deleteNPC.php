<?php 
header('Content-type: application/json');
require('../php/database_connect.php');

// close session to allow for parallel requests
session_start();
session_write_close();

$npcId = $_GET["npcId"];

//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$npcId = stripslashes(mysql_real_escape_string($npcId));
}

$user = mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE id='" . $npcId . "';"));

$query = 'DELETE FROM Active_exchanges WHERE requesterNetId="'. $user['netId'] . '" OR associatedExchanges LIKE "%'. $user['netId'].'%"';
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

$query = 'DELETE FROM Users WHERE id="'. $npcId . '"';
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

// get NPC list
$filename = 'NPCs.txt';
$userIds = file($filename, FILE_IGNORE_NEW_LINES);

// clear NPC list
$handle = fopen ($filename, "w+");
fclose($handle);

foreach ($userIds as $userId)
{
	if ($userId != $npcId)
	{
		file_put_contents($filename, $userId . "\n", FILE_APPEND | LOCK_EX);
	}
}

mysql_close($connection);
 
?>