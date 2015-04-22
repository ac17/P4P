<?php 
header('Content-type: application/json');
//Get login information 
require('./database_connect.php');
//Get the id of the note to be displayed returned
$currentUserNetId = $_GET["currentUserNetId"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$currentUserNetId = stripslashes(mysql_real_escape_string($currentUserNetId));
}

//Build a query
$select = ' SELECT '; 
$column =  ' * ';  
$from = ' FROM ';  
$tables = ' Active_exchanges ';
$where = 'WHERE requesterNetId="' . $currentUserNetId . '" AND isPartOfTransaction="1" ';
$query = $select . $column . $from . $tables . $where; 
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}


$trades = array();

//echo "Results for ".$queryTerms.": <br/><br/>";
//Display the results from the query
if ($query_result !== false)
{
	while($exchange = mysql_fetch_array(($query_result))){
		$offerId = "";
		$requestId = "";
		$recipient = "";
		$club = $exchange['passClub'];
		$passNum = $exchange['passNum'];
		$passDate = $exchange['passDate'];
		$comments = $exchange['comments'];
		
		// check if the trade is based on the current user's reuqest or offer
		if ($exchange['type'] == "Request")
		{
			$requestId = $exchange['id'];
			$offer = getCorrespodingOffer(json_decode($exchange['associatedExchanges']));
			$recipient = $offer['requesterNetId'];
			$offerId = $offer['id'];
		}
		else 
		{
			$offerId = $exchange['id'];
			$request = getCorrespodingRequest($currentUserNetId, $offerId);
			$recipient = $request['requesterNetId'];
			$requestId = $request['id'];
		}
		
		array_push($trades, array('offerId' =>$offerId,
								  'requestId' =>$requestId,
								  'recipient' =>$recipient, 
								  'club' =>$club,
								  'passNum' =>$passNum,
								  'passDate' =>$passDate,
								  'comments' =>$comments));
	}
}
echo json_encode(array("Trades"=>$trades)); 
mysql_close($connection);

function getCorrespodingRequest($currentUserNetId, $offerId)
{
	// get correspoding offer's request's id 
	$query = ' SELECT * FROM Active_exchanges WHERE requesterNetId="' . $currentUserNetId . '" AND associatedExchanges LIKE "%'.$offerId.'%"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	$result = mysql_fetch_array(($query_result));
	
	return $result;
}

function getCorrespodingOffer($requestAssociatedExchanges)
{
	// get the offer's associatedExchanges
	$query = ' SELECT * FROM Active_exchanges WHERE id="' . $requestAssociatedExchanges[0] . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	$offer = mysql_fetch_array(($query_result));
	
	return $offer;
}
	
?>