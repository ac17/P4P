<?php 
header('Content-type: application/json');
//Get login information 
require('./database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the netId of user
$currentUserNetId = $_GET["currentUserNetId"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
}

$trades = array();
/*Function returns all the active trades of user with $currentUserNetId.*/
$trades = userActiveTrades($currentUserNetId);

echo json_encode(array("Trades"=>$trades)); 
mysql_close($connection);
	
?>