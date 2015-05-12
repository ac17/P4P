<?php 
header('Content-type: application/json');
//Get login information 
require('./database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the query parameters
$date = $_GET["date"];
$passClub = $_GET["club"];
$numPasses = $_GET["numPasses"];
$type = $_GET["type"];

//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$date = stripslashes(mysql_real_escape_string($date));
	$passClub = stripslashes(mysql_real_escape_string($passClub));
	$numPasses = stripslashes(mysql_real_escape_string($numPasses));
	$type = stripslashes(mysql_real_escape_string($type));
}

/*Function returns all offer which match the passes parameters. This function doesn't ignores all offers which 
are part of trades and offers which are owned by user any user. Results are returned in an array. */
$users = searchExchanges($date, $passClub, $numPasses, $type);

// return the users
echo json_encode(array("Users"=>array_values($users))); 
mysql_close($connection);
 
?>