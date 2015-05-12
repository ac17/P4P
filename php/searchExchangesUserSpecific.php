<?php 
header('Content-type: application/json');
//Get login information 
require('./database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get get query data
$currentUserNetId = $_GET["netId"];
$date = $_GET["date"];
$passClub = $_GET["club"];
$numPasses = $_GET["numPasses"];
$type = $_GET["type"];

//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
	$date = stripslashes(mysql_real_escape_string($date));
	$passClub = stripslashes(mysql_real_escape_string($passClub));
	$numPasses = stripslashes(mysql_real_escape_string($numPasses));
	$type = stripslashes(mysql_real_escape_string($type));
}

/*Function returns all offer which match the passes parameters. This function ignores all offers which 
are part of trades and offers which are owned by user with netId $currentUserNetId. There is a bulit in 
limit of ten users to return. Results are returned in an array index by users. */
$users = searchExchangesUserSpecific($currentUserNetId, $date, $passClub, $numPasses, $type);

// return the users array
echo json_encode(array("Users"=>array_values($users))); 
mysql_close($connection);

?>