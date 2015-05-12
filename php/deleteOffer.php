<?php 
// Delete 

//Get login information 
require('../php/database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the id of the offer and the offer's owner
$offerId = $_GET["offerId"];
$requesterNetId=$_GET["requesterNetId"];

//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$offerId = stripslashes(mysql_real_escape_string($offerId));
	$requesterNetId = stripslashes(mysql_real_escape_string($requesterNetId));
}

/* Function to delete a single offer which has the id $requestId and belongs to $netId */
deleteOffer($requesterNetId, $offerId);
	
mysql_close($connection);

?>