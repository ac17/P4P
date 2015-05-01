<?php 
//Get login information 
require('database_connect.php');
require('exchange_api/exchangeAPI.php');

//Get the id of the note to be displayed returned
$currentUserNetId = $_GET["netId"];
$offerId = $_GET["offerId"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
	$offerId = stripslashes(mysql_real_escape_string($offerId));
}

pursueOffer($currentUserNetId, $offerId);

mysql_close($connection);

?>