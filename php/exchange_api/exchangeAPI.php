<?php

function pursueOffer($currentUserNetId, $offerId)
{
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
	$query = 'INSERT INTO Active_exchanges VALUES(NULL,"'. $currentUserNetId . '","' . $offer['passClub'] . '","' . $offer['passNum'] . '","' . date ("Y-m-d", strtotime($offer['passDate'])) . '","","Request", "0", \''. json_encode(array($offer['id'])) .'\')';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	// add request's id to the offer's associatedExchanges 
	$associatedExchanges = json_decode( $offer['associatedExchanges'] );
	$associatedExchanges[] = $currentUserNetId;
	echo json_encode($associatedExchanges);
	$query = ' UPDATE Active_exchanges SET associatedExchanges=\''.json_encode($associatedExchanges).'\' WHERE id="' . $offer['id'] . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}


function userActiveTrades($currentUserNetId)
{
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
			$provider = "";
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
				$provider = $offer['requesterNetId'];
				$recipient = $currentUserNetId;
				$offerId = $offer['id'];
			}
			else 
			{
				$offerId = $exchange['id'];
				$request = getCorrespodingRequest($currentUserNetId, $offerId);
				$recipient = $request['requesterNetId'];
				$provider = $currentUserNetId;
				$requestId = $request['id'];
			}
			
			array_push($trades, array('offerId' =>$offerId,
									  'requestId' =>$requestId,
									  'provider' =>$provider,
									  'recipient' =>$recipient, 
									  'club' =>$club,
									  'passNum' =>$passNum,
									  'passDate' =>$passDate,
									  'comments' =>$comments));
		}
	}
return $trades;
}


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

function userActiveExchanges($currentUserNetId)
{
	//Build a query
	$select = ' SELECT '; 
	$column =  ' * ';  
	$from = ' FROM ';  
	$tables = ' Active_exchanges ';
	$where = 'WHERE requesterNetId="' . $currentUserNetId . '" AND isPartOfTransaction="0" ';
	$query = $select . $column . $from . $tables . $where; 
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	
	$exchanges = array();
	
	//echo "Results for ".$queryTerms.": <br/><br/>";
	//Display the results from the query
	if ($query_result !== false)
	{
		while($exchange = mysql_fetch_array(($query_result))){	
			array_push($exchanges, array('id' =>$exchange['id'], 
										 'club' =>$exchange['passClub'],
										 'passNum' =>$exchange['passNum'],
										 'passDate' =>$exchange['passDate'],
										 'comments' =>$exchange['comments'],
										 'associatedExchanges' =>$exchange['associatedExchanges'],
										 'type' =>$exchange['type'])); 
		}
	}
return $exchanges;
}


function searchExchangesUserSpecific($currentUserNetId, $date, $passClub, $numPasses, $type)
{
	//Build a query
	$select = ' SELECT '; 
	$column =  ' *, x( Users.location ) AS my_point_x, y( Users.location ) AS my_point_y, Active_exchanges.id as exchangeId ';  
	$from = ' FROM ';  
	$tables = ' Active_exchanges LEFT JOIN Users ON Active_exchanges.requesterNetId=Users.netId ';
	
	// Select between searching for all clubs
	if($passClub != "All")
	{
		$where = ' WHERE Active_exchanges.passDate="' . date ("Y-m-d", strtotime($date)) . '" AND Active_exchanges.type="' .$type. '" AND Active_exchanges.passNum >= '.$numPasses.' AND Active_exchanges.passClub="'.$passClub.'"';
	}
	else 
	{
		$where = ' WHERE Active_exchanges.passDate="' . date ("Y-m-d", strtotime($date)) .'" AND Active_exchanges.passNum >= '.$numPasses.' AND Active_exchanges.type="'.$type.'"';
	}
	$order = " ORDER BY requesterNetId DESC";
	 
	$query = $select . $column . $from . $tables . $where . $order;
	
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	$users = array();
	$requestedByUser = 0;
	 
	//echo "Results for ".$queryTerms.": <br/><br/>";
	//Display the results from the query
	if ($query_result !== false)
	{
		// netId of the previous user whose exhanges are being added
		$previousUser = "";
		
		while($exchange = mysql_fetch_array(($query_result)))
		{
			// has the user requested this $exchange
			if(strpos($exchange['associatedExchanges'],$currentUserNetId) !== false)
			{
				$requestedByUser = 1;
			}
			else
			{
				$requestedByUser = 0;
			}
			
			// add a new user to the $users array
			if ($previousUser != $exchange['requesterNetId'])
			{			
				$users[$exchange['requesterNetId']] = array('netId' =>$exchange['requesterNetId'],
															'name' =>$exchange['firstName'],
															'lat' =>$exchange['my_point_x'], 
															'lng' =>$exchange['my_point_y'],
															'exchanges' => array(array('id' =>$exchange['exchangeId'], 
																				'club' =>$exchange['passClub'],
																				'passNum' =>$exchange['passNum'],
																				'passDate' =>$exchange['passDate'],
																				'comment' =>$exchange['comments'],
																				'type' =>$exchange['type'],
																				'requested' =>$requestedByUser))
															);
				$currentUser = $exchange['requesterNetId'];
			}
			// add an exhange for a user already in the $users array
			else
			{
				$users[$exchange['requesterNetId']]['exchanges'][] = array('id' =>$exchange['exchangeId'], 
																		 'club' =>$exchange['passClub'],
																		 'passNum' =>$exchange['passNum'],
																		 'passDate' =>$exchange['passDate'],
																		 'comment' =>$exchange['comments'],
																		 'type' =>$exchange['type'],
																		 'requested' =>$requestedByUser);
			}
			
			$previousUser = $exchange['requesterNetId'];
		}
	}
	
	return $users;
}


function getArrayOfUserRequestIds($userNetId)
{
	require('./database_connect.php');
	
	$requestIds = array();
	
	//Build a query
	$select = ' SELECT '; 
	$column =  ' id ';  
	$from = ' FROM ';  
	$tables = ' Active_exchanges ';
    $where = ' WHERE requesterNetId="' . $userNetId .'" AND type="Request"';
	$query = $select . $column . $from . $tables . $where;
	
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	while($id = mysql_fetch_array(($query_result)))
	{
		$requestIds[] = $id['id'];
	}
	
	return $requestIds;
}

function searchExchanges($date, $passClub, $numPasses, $type)
{
	//Build a query
	$select = ' SELECT '; 
	$column =  ' *, x( Users.location ) AS my_point_x, y( Users.location ) AS my_point_y, Active_exchanges.id as exchangeId ';  
	$from = ' FROM ';  
	$tables = ' Active_exchanges LEFT JOIN Users ON Active_exchanges.requesterNetId=Users.netId ';
	
	// Select between searching for all clubs
	if($passClub != "All")
	{
		$where = ' WHERE Active_exchanges.passDate="' . date ("Y-m-d", strtotime($date)) . '" AND Active_exchanges.type="' .$type. '" AND Active_exchanges.passNum >= '.$numPasses.' AND Active_exchanges.passClub="'.$passClub.'"';
	}
	else 
	{
		$where = ' WHERE Active_exchanges.passDate="' . date ("Y-m-d", strtotime($date)) .'" AND Active_exchanges.passNum >= '.$numPasses.' AND Active_exchanges.type="'.$type.'"';
	}
	$order = " ORDER BY requesterNetId DESC";
	 
	$query = $select . $column . $from . $tables . $where . $order;
	
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	$users = array();
	
	//echo "Results for ".$queryTerms.": <br/><br/>";
	//Display the results from the query
	if ($query_result !== false)
	{
		// netId of the previous user whose exhanges are being added
		$previousUser = "";
		
		while($exchange = mysql_fetch_array(($query_result)))
		{
			// add a new user to the $users array
			if ($previousUser != $exchange['requesterNetId'])
			{
				$users[$exchange['requesterNetId']] = array('netId' =>$exchange['requesterNetId'],
															'name' =>$exchange['firstName'],
															'lat' =>$exchange['my_point_x'], 
															'lng' =>$exchange['my_point_y'],
															'exchanges' => array(array('id' =>$exchange['exchangeId'], 
																				'club' =>$exchange['passClub'],
																				'passNum' =>$exchange['passNum'],
																				'passDate' =>$exchange['passDate'],
																				'comment' =>$exchange['comments'],
																				'associatedExchanges' =>$exchange['associatedExchanges'],
																				'type' =>$exchange['type']))
															);
				$currentUser = $exchange['requesterNetId'];
			}
			// add an exhange for a user already in the $users array
			else
			{
				$users[$exchange['requesterNetId']]['exchanges'][] = array('id' =>$exchange['exchangeId'], 
																		 'club' =>$exchange['passClub'],
																		 'passNum' =>$exchange['passNum'],
																		 'passDate' =>$exchange['passDate'],
																		 'comment' =>$exchange['comments'],
																		 'associatedExchanges' =>$exchange['associatedExchanges'],
																		 'type' =>$exchange['type']);
			}
			
			$previousUser = $exchange['requesterNetId'];
		}
	}

	return $users;
}

function removeExchanges($currentUserNetId, $exchangesToRemove)
{
	foreach ($exchangesToRemove as $exchange)
	{
		if($exchange[1] == "Request")
		{
			deleteRequest($currentUserNetId, $exchange[0]);
		}
		else 
		{
			deleteOffer($currentUserNetId, $exchange[0]);
		}	
	}
}

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


function deleteRequestByOfferId($currentUserNetId, $requesterNetId, $offerId)
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


function completeTrade($currentUserNetId, $provider, $recipient, $offerId, $requestId)
{
	// get the offer info
	$query = ' SELECT * FROM Active_exchanges WHERE id="' . $offerId . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	$offer = mysql_fetch_array(($query_result));
	
	// record trade into history table
	$query = 'INSERT INTO Exchange_history VALUES(NULL,"'. $recipient . '","' . $offer['passClub'] . '","' . $offer['passNum'] . '","' . $offer['passDate']  . '","' . $provider . '",1,"' . $offer['passClub'] . '","' . $offer['passNum'] . '","' . $offer['passDate']  . '")';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	// reward recipient for sucessful trade
	$query = 'UPDATE Users SET reputation=reputation+1 WHERE netId="'.$recipient.'"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	// reward provider for sucessful trade
	$query = 'UPDATE Users SET reputation=reputation+1 WHERE netId="'.$provider.'"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	// remove the associated offer and request
	$query = 'DELETE FROM Active_exchanges WHERE id="'.$offerId.'" OR id="'.$requestId.'"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}

function cancelTrade($currentUserNetId, $provider, $recipient, $offerId, $requestId)
{
	// get the offer info
	$query = ' SELECT * FROM Active_exchanges WHERE id="' . $offerId . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	$offer = mysql_fetch_array(($query_result));
	
	// punish the cancelling user 
	$query = 'UPDATE Users SET reputation=reputation-1 WHERE netId="'.$currentUserNetId.'"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	if ($currentUserNetId == $recipient)
	{
		
		// upadate the offer 
		$query = ' UPDATE Active_exchanges SET associatedExchanges="[]", isPartOfTransaction="0" WHERE id="' . $offerId . '"';
		//Execute the query
		$query_result = mysql_query($query);
		//Provide an error message if the query failed
		if(!$query_result){
			die("Could not query the database. " . mysql_error());
		}
		
		// delete the request
		$query = 'DELETE FROM Active_exchanges WHERE id="'.$requestId.'"';
		//Execute the query
		$query_result = mysql_query($query);
		//Provide an error message if the query failed
		if(!$query_result){
			die("Could not query the database. " . mysql_error());
		}
	}
	else 
	{
		// delete offer and request
		$query = 'DELETE FROM Active_exchanges WHERE id="'.$offerId.'" OR id="'.$requestId.'"';
		//Execute the query
		$query_result = mysql_query($query);
		//Provide an error message if the query failed
		if(!$query_result){
			die("Could not query the database. " . mysql_error());
		}
	}
}

function addExchange($currentUserNetId, $passDate, $type, $numPasses, $passClub, $comment)
{
	//Build query
	$query = 'INSERT INTO Active_exchanges VALUES(NULL,"'. $currentUserNetId . '","' . $passClub . '","' . $numPasses . '","' . date ("Y-m-d", strtotime($passDate)) . '","' . $comment . '","' . $type. '",0,"[]")';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}

?>