<?php 
//Get login information 
require('../php/database_connect.php');
require('exchange_api/exchangeAPI.php');
//Get the id of the note to be displayed returned
$currentUserNetId = $_GET["netId"];
$passDate = $_GET["passDate"];
$type = $_GET["type"];
$numPasses = $_GET["numPasses"];
$passClub = $_GET["club"];
$comment = $_GET["comment"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$currentUserNetId = stripslashes(mysql_real_escape_string($$currentUserNetId));
	$passDate = stripslashes(mysql_real_escape_string($passDate));
	$type = stripslashes(mysql_real_escape_string($type));
	$numPasses = stripslashes(mysql_real_escape_string($numPasses));
	$passType = stripslashes(mysql_real_escape_string($passType));
	$comment = stripslashes(mysql_real_escape_string($comment));
}

addExchange($currentUserNetId, $passDate, $type, $numPasses, $passClub, $comment);

mysql_close($connection);
 
?>