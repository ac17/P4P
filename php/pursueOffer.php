<?php 
//Get login information 
require('database_connect.php');
require('exchange_api/exchangeAPI.php');

//Get the id of the offer and the netId of the user making the request
$currentUserNetId = $_GET["netId"];
$offerId = $_GET["offerId"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
	$offerId = stripslashes(mysql_real_escape_string($offerId));
}

/*Function which adds the user to the offer with $offerId list of associated 
exchanges and creates a new reuqest. The offer owner is push notifed of the request.*/
pursueOffer($currentUserNetId, $offerId);

mysql_close($connection);

?>