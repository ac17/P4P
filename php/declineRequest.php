<?php 
// Delete 

//Get login information 
require('../php/database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the id of the note to be displayed returned
$offerId = $_GET["offerId"];
$requesterNetId=$_GET["requesterNetId"];
$currentUserNetId=$_GET["currentUserNetId"];

//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$requestId = stripslashes(mysql_real_escape_string($requestId));
	$requesterNetId = stripslashes(mysql_real_escape_string($requesterNetId));
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
}

deleteRequestByOfferId($currentUserNetId, $requesterNetId, $offerId);
	

mysql_close($connection);

?>