<?php 
header('Content-type: application/json');
//Get login information 
require('./database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the parameters
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

/* Function to complete a trade betwee $provider (netId) and $recipient ($netId).
Both users are rewarded with 1 reputation point. */
completeTrade($currentUserNetId, $provider, $recipient, $offerId, $requestId);

mysql_close($connection);
	
?>