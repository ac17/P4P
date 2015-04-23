<?php 
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

// punish the cancelling user 
$query = 'UPDATE Users SET reputation=reputation-1 WHERE netId="'.$currentUserNetId.'"';
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

if ($currentUserNetId == $recipient)
{
	
	// upadate the offer 
	$query = ' UPDATE Active_exchanges SET associatedExchanges="[]", isPartOfTransaction="0" WHERE id="' . $offerId . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	// delete the request
	$query = 'DELETE FROM Active_exchanges WHERE id="'.$requestId.'"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}
else 
{
	// delete offer and request
	$query = 'DELETE FROM Active_exchanges WHERE id="'.$offerId.'" OR id="'.$requestId.'"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}

mysql_close($connection);

?>