<?php 
//Get login information 
require('../php/database_connect.php');
require('exchange_api/exchangeAPI.php');

//Get an array of exchange ids to delete and the netId of their owner
$currentUserNetId = $_GET["currentUserNetId"];
$data = file_get_contents( "php://input" );
$exchangesToRemove = json_decode( $data );
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
	$exchangesToRemove = stripslashes(mysql_real_escape_string($exchangesToRemove));
}

/* Remove all exchanges in the array of ids $exchanges to remove 
which belong to $currentUserNetId */
removeExchanges($currentUserNetId, $exchangesToRemove);

mysql_close($connection);


?>