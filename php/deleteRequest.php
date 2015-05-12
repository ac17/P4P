<?php 
// Delete 

//Get login information 
require('../php/database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the id of the request and the request's owner
$requestId = $_GET["requestId"];
$requesterNetId=$_GET["requesterNetId"];

//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$requestId = stripslashes(mysql_real_escape_string($requestId));
	$requesterNetId = stripslashes(mysql_real_escape_string($requesterNetId));
}

/* Function to delete a single request which has the id $requestId and belongs to $netId */
deleteRequest($requesterNetId, $requestId);
	

mysql_close($connection);

?>