<?php 
// Delete 

//Get login information 
require('../php/database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the id of the note to be displayed returned
$requestId = $_GET["requestId"];
$requesterNetId=$_GET["requesterNetId"];

//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$requestId = stripslashes(mysql_real_escape_string($requestId));
	$requesterNetId = stripslashes(mysql_real_escape_string($requesterNetId));
}

deleteRequest($requesterNetId, $requestId);
	

mysql_close($connection);

?>