<?php 
//Get login information 
require('database_connect.php');
//Get the id of the note to be displayed returned
$netId = $_GET["netId"];
$offerId = $_GET["offerId"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$netId = stripslashes(mysql_real_escape_string($netId));
	$offerId = stripslashes(mysql_real_escape_string($offerId));
}

// get correspoding offer 
$query = ' SELECT * FROM Active_exchanges WHERE id="' . $offerId . '"';
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}
$offer = mysql_fetch_array(($query_result));

// add a request
$query = 'INSERT INTO Active_exchanges VALUES(NULL,"'. $netId . '","' . $offer['passClub'] . '","' . $offer['passNum'] . '","' . date ("Y-m-d", strtotime($offer['passDate'])) . '","","Request", "0", \''. json_encode(array($offer['id'])) .'\')';
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

// look up request's id 
$query = ' SELECT id FROM Active_exchanges WHERE requesterNetId="' . $netId . '" AND associatedExchanges LIKE "%' . $offer['id'] .'%"';
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}
$request = mysql_fetch_array(($query_result));

// add request's id to the offer's associatedExchanges 
$associatedExchanges = json_decode( $offer['associatedExchanges'] );
$associatedExchanges[] =  $request['id'];
echo json_encode($associatedExchanges);
$query = ' UPDATE Active_exchanges SET associatedExchanges=\''.json_encode($associatedExchanges).'\' WHERE id="' . $offer['id'] . '"';
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

mysql_close($connection);
 
?>