<?php 
header('Content-type: application/json');
//Get login information 
require('./database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the id of the note to be displayed returned
$currentUserNetId = $_GET["currentUserNetId"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
}

$exchanges = array();
$exchanges = userActiveExchanges($currentUserNetId);

echo json_encode(array("Exchanges"=>$exchanges)); 
mysql_close($connection);
 
?>