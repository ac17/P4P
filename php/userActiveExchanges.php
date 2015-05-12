<?php 
header('Content-type: application/json');
//Get login information 
require('./database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the netId of the user
$currentUserNetId = $_GET["currentUserNetId"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
}

$exchanges = array();
/*Function returns all the active requests and offers of user with $currentUserNetId. An array is returned.*/
$exchanges = userActiveExchanges($currentUserNetId);

// return the array as JSON string
echo json_encode(array("Exchanges"=>$exchanges)); 
mysql_close($connection);
 
?>