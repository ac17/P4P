<?php 
// Delete 

//Get login information 
require('../php/database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the id of the note to be displayed returned
$offerId = $_GET["offerId"];
$requesterNetId=$_GET["requesterNetId"];

//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$offerId = stripslashes(mysql_real_escape_string($offerId));
	$requesterNetId = stripslashes(mysql_real_escape_string($requesterNetId));
}

deleteOffer($requesterNetId, $offerId);
	
mysql_close($connection);

?>