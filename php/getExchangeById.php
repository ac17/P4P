<?php 
header('Content-type: application/json');
//Get login information 
require('database_connect.php');
require('exchange_api/exchangeAPI.php');

//Get the id the exchage to look up
$exchangeId = $_GET["exchangeId"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$exchangeId = stripslashes(mysql_real_escape_string($exchangeId));
}

/*Function returns exchange with id $exchangeId as an array.*/
$exchange = getExchangeById($exchangeId);

echo json_encode($exchange); 
mysql_close($connection);

?>