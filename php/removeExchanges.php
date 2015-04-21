<?php 
//Get login information 
require('../php/database_connect.php');
//Get the id of the note to be displayed returned
$netId = $_GET["netId"];
$data = file_get_contents( "php://input" );
$exchangesToRemove = json_decode( $data );
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$netId = stripslashes(mysql_real_escape_string($netId));
	$exchangesToRemove = stripslashes(mysql_real_escape_string($exchangesToRemove));
}

foreach ($exchangesToRemove as $exchange)
{
	if($exchange[1] == "Request")
	{
		deleteRequest($netId, $exchange[0]);
	}
	else 
	{
		deleteOffer($netId, $exchange[0]);
	}	
}

mysql_close($connection);

function deleteRequest($netId, $requestId)
{
	// get correspoding request's offer's id 
	$query = ' SELECT associatedExchanges FROM Active_exchanges WHERE id="' . $requestId . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	$result = mysql_fetch_array(($query_result));
	$offerId = json_decode( $result['associatedExchanges'] );
		
	// get the offer's associatedExchanges
	$query = ' SELECT associatedExchanges FROM Active_exchanges WHERE id="' . $offerId[0] . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	$offer = mysql_fetch_array(($query_result));
	
	// remove user's net id from the offer's associatedExchanges
	$associatedExchanges = json_decode( $offer['associatedExchanges'] );
	$associatedExchanges = array_diff($associatedExchanges, array($netId));
	echo json_encode($associatedExchanges);
	$query = ' UPDATE Active_exchanges SET associatedExchanges=\''.json_encode($associatedExchanges).'\' WHERE id="' . $offerId[0] . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	//Delete the request row
	$query = 'DELETE FROM Active_exchanges WHERE requesterNetId="'. $netId . '" AND id="'.$requestId.'"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}

function deleteOffer($netId, $offerId)
{	
	// get the offer's associatedExchanges
	$query = ' SELECT associatedExchanges FROM Active_exchanges WHERE id="' . $offerId . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	$offer = mysql_fetch_array(($query_result));
		
	if ($offer['associatedExchanges'] != "" && $offer['associatedExchanges'] != "[]")
	{
		// remove requests based on netids in offer's associatedExchanges
		$associatedExchanges = json_decode( $offer['associatedExchanges'] );
		echo json_encode($associatedExchanges);
		$query = 'DELETE FROM Active_exchanges WHERE associatedExchanges LIKE "%'. $offerId . '%" AND requesterNetId IN (' . implode(',', array_map('intval', $associatedExchanges)) . ')';
		//Execute the query
		$query_result = mysql_query($query);
		//Provide an error message if the query failed
		if(!$query_result){
			die("Could not query the database. " . mysql_error());
		}
	}
	
	//Delete the offer row
	$query = 'DELETE FROM Active_exchanges WHERE requesterNetId="'. $netId . '" AND id="'.$offerId.'"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}
?>