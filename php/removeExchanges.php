<?php 
//Get login information 
require('../php/database_connect.php');
require('exchange_api/exchangeAPI.php');

//Get the id of the note to be displayed returned
$currentUserNetId = $_GET["currentUserNetId"];
$data = file_get_contents( "php://input" );
$exchangesToRemove = json_decode( $data );
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
	$exchangesToRemove = stripslashes(mysql_real_escape_string($exchangesToRemove));
}

removeExchanges($currentUserNetId, $exchangesToRemove);

mysql_close($connection);


?>