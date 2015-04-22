<?php 
// Delete 

//Get login information 
require('../php/database_connect.php');
//Get the id of the note to be displayed returned
$offerId = $_GET["offerId"];
$requesterNetId=$_GET["requesterNetId"];
$currentUserNetId=$_GET["currentUserNetId"];

//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$requestId = stripslashes(mysql_real_escape_string($requestId));
	$requesterNetId = stripslashes(mysql_real_escape_string($requesterNetId));
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
}

deleteRequest($currentUserNetId, $requesterNetId, $offerId);
	

mysql_close($connection);

function deleteRequest($currentUserNetId, $requesterNetId, $offerId)
{	
	// get correspoding offer's request's id 
	$query = ' SELECT id FROM Active_exchanges WHERE requesterNetId="' . $currentUserNetId . '" AND associatedExchanges LIKE "%'.$offerId.'%"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	$result = mysql_fetch_array(($query_result));
	$requestId = $result['id'];
		
	// get the offer's associatedExchanges
	$query = ' SELECT associatedExchanges FROM Active_exchanges WHERE id="' . $offerId . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	$offer = mysql_fetch_array(($query_result));
	
	// remove $requesterNetId from the offer's associatedExchanges
	$associatedExchanges = json_decode( $offer['associatedExchanges'] );
	$associatedExchanges = array_diff($associatedExchanges, array($requesterNetId));
	echo json_encode($associatedExchanges);
	$query = ' UPDATE Active_exchanges SET associatedExchanges=\''.json_encode($associatedExchanges).'\' WHERE id="' . $offerId . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	//Delete the request row
	$query = 'DELETE FROM Active_exchanges WHERE requesterNetId="'. $requesterNetId . '" AND id="'.$requestId.'"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}
?>