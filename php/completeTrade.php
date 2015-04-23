<?php 
header('Content-type: application/json');
//Get login information 
require('./database_connect.php');
//Get the id of the note to be displayed returned
$currentUserNetId = $_GET["currentUserNetId"];
$provider = $_GET["provider"];
$recipient = $_GET["recipient"];
$offerId = $_GET["offerId"];
$requestId = $_GET["requestId"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
	$provider = stripslashes(mysql_real_escape_string($provider));
	$recipient = stripslashes(mysql_real_escape_string($recipient));
	$offerId = stripslashes(mysql_real_escape_string($offerId));
	$requestId = stripslashes(mysql_real_escape_string($requestId));
}

// get the offer info
$query = ' SELECT * FROM Active_exchanges WHERE id="' . $offerId . '"';
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}
$offer = mysql_fetch_array(($query_result));

// record trade into history table
$query = 'INSERT INTO Exchange_history VALUES(NULL,"'. $recipient . '","' . $offer['passClub'] . '","' . $offer['passNum'] . '","' . $offer['passDate']  . '","' . $provider . '",1,"' . $offer['passClub'] . '","' . $offer['passNum'] . '","' . $offer['passDate']  . '")';
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

// reward recipient for sucessful trade
$query = 'UPDATE Users SET reputation=reputation+1 WHERE netId="'.$recipient.'"';
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

// reward provider for sucessful trade
$query = 'UPDATE Users SET reputation=reputation+1 WHERE netId="'.$provider.'"';
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

// remove the associated offer and request
$query = 'DELETE FROM Active_exchanges WHERE id="'.$offerId.'" OR id="'.$requestId.'"';
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}


mysql_close($connection);
	
?>