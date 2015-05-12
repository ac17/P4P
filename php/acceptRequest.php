<?php 
// Delete 

//Get login information 
require('../php/database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the id of offer the request
$offerId = $_GET["offerId"];
$requesterNetId=$_GET["requesterNetId"];
$currentUserNetId=$_GET["currentUserNetId"];

//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$offerId = stripslashes(mysql_real_escape_string($offerId));
	$requesterNetId = stripslashes(mysql_real_escape_string($requesterNetId));
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
}

/* Function to accept a request from $requesterNetId to offer with id $offerId 
owned by $currentUserNetId. All other requests for this offer are deleted. */
acceptRequest($currentUserNetId, $requesterNetId, $offerId);
	
mysql_close($connection);

?>