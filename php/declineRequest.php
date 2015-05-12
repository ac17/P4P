<?php 
// Delete 

//Get login information 
require('../php/database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the id offer to decline
$offerId = $_GET["offerId"];
$requesterNetId=$_GET["requesterNetId"];
$currentUserNetId=$_GET["currentUserNetId"];

//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$offerId = stripslashes(mysql_real_escape_string($offerId));
	$requesterNetId = stripslashes(mysql_real_escape_string($requesterNetId));
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
}

/* Function to delete request of $currentUserNetId from offer with id $offerId. 
This action is equvalent to declining an request.*/
deleteRequestByOfferId($currentUserNetId, $requesterNetId, $offerId);
	

mysql_close($connection);

?>