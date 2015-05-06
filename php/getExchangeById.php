<?php 
header('Content-type: application/json');
//Get login information 
require('database_connect.php');
require('exchange_api/exchangeAPI.php');

//Get the id of the note to be displayed returned
$exchangeId = $_GET["exchangeId"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$exchangeId = stripslashes(mysql_real_escape_string($exchangeId));
}

$exchange = getExchangeById($exchangeId);

echo json_encode($exchange); 
mysql_close($connection);

?>