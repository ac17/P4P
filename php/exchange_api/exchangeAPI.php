<?php

/*Function which adds the user to the offer with $offerId list of associated 
exchanges and creates a new reuqest. The offer owner is push notifed of the request.*/
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
	
	// error: no matching offer 
	if (mysql_num_rows($query_result)==0) 
	{ 
		echo "The offer you are pursuing has been deleted.";
		return; 
	}
	
	$offer = mysql_fetch_array(($query_result));
	
	// error: the offer is now part of a trade
	if ($offer['isPartOfTransaction'] == 1) 
	{ 
		echo "The offer you are pursuing is part of a trade.";
		return; 
	}
	
	// send a push notification 
	$deviceToken = getDeviceId($offer['requesterNetId']);
	if ($deviceToken != NULL)
	{ 
		$message = getUserNameByNetId($currentUserNetId) . " requested your pass.";
		shell_exec(PHP_BINDIR . "/php /opt/lampp/htdocs/php/exchange_api/pushNotification.php " . $deviceToken . " '" . $message . "'");
	}
	
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
	$query = ' UPDATE Active_exchanges SET associatedExchanges=\''.json_encode(array_values($associatedExchanges)).'\' WHERE id="' . $offer['id'] . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}

/*Function returns all the active trades of user with $currentUserNetId. An array is returned.*/
function userActiveTrades($currentUserNetId)
{
	//Build a query to get all offer which are part of trades and have the current user's net id 
	$select = ' SELECT '; 
	$column =  ' * ';  
	$from = ' FROM ';  
	$tables = ' Active_exchanges ';
	$where = 'WHERE (requesterNetId="' . $currentUserNetId . '" OR associatedExchanges LIKE "%'.$currentUserNetId.'%") AND isPartOfTransaction="1" AND type="Offer"';
	$query = $select . $column . $from . $tables . $where; 
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	
	$trades = array();

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
			$offerId = $exchange['id'];
			$provider = $exchange['requesterNetId'];
			
			$request = getCorrespodingRequest($offerId); 
			$recipient = $request['requesterNetId'];
			$requestId = $request['id'];
			
			// add trade 
			array_push($trades, array('offerId' =>$offerId,
									  'requestId' =>$requestId,
									  'provider' =>$provider,
									  'providerName' =>getUserNameByNetId($provider),
									  'recipient' =>$recipient,
									  'recipientName' =>getUserNameByNetId($recipient),
									  'club' =>$club,
									  'passNum' =>$passNum,
									  'passDate' =>$passDate,
									  'comments' =>$comments));
		}
	}
return $trades;
}

/* Helper function returns all information on the offer with $offerId in an array. */
function getCorrespodingRequest($offerId)
{
	// get correspoding offer's request's id 
	$query = ' SELECT * FROM Active_exchanges WHERE associatedExchanges LIKE "%'.$offerId.'%"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	$result = mysql_fetch_array(($query_result));
	
	return $result;
}

/* Helper function returns all information on the offer based on id in $requestAssociatedExchanges in an array. */
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

/*Function returns all the active requests and offers of user with $currentUserNetId. An array is returned.*/
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
	
	if ($query_result !== false)
	{
		while($exchange = mysql_fetch_array(($query_result))){
			
			// add each exchange 
			$names = array();
			if($exchange['type'] == "Offer")
			{
			   $netIds = json_decode($exchange['associatedExchanges']);
			   foreach ($netIds as $netId)
			   {
			   	  array_push($names, getUserNameByNetId($netId));
			   }
			}
			
			array_push($exchanges, array('id' =>$exchange['id'], 
										 'club' =>$exchange['passClub'],
										 'passNum' =>$exchange['passNum'],
										 'passDate' =>$exchange['passDate'],
										 'comments' =>$exchange['comments'],
										 'associatedExchanges' =>$exchange['associatedExchanges'],
										 'names' =>$names,
										 'type' =>$exchange['type'])); 
		}
	}
	
return $exchanges;
}

/*Function returns exchange with id $exchangeId as an array.*/
function getExchangeById($exchangeId)
{
	//Build a query
	$select = ' SELECT '; 
	$column =  ' * ';  
	$from = ' FROM ';  
	$tables = ' Active_exchanges ';
	$where = 'WHERE id="' . $exchangeId . '"';
	$query = $select . $column . $from . $tables . $where; 
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
				
	$exchanges = array();
	
	if ($query_result !== false)
	{
		while($exchange = mysql_fetch_array(($query_result))){
			$names = array();
			if($exchange['type'] == "Offer")
			{
			   $netIds = json_decode($exchange['associatedExchanges']);
			   foreach ($netIds as $netId)
			   {
				  array_push($names, getUserNameByNetId($netId));
			   }
			}
	
			array_push($exchanges, array('id' =>$exchange['id'], 
										 'club' =>$exchange['passClub'],
										 'passNum' =>$exchange['passNum'],
										 'passDate' =>$exchange['passDate'],
										 'comments' =>$exchange['comments'],
										 'associatedExchanges' =>$exchange['associatedExchanges'],
										 'names' =>$names,
										 'type' =>$exchange['type'])); 
		}
	}
return $exchanges;
}

/*Function returns all offer which match the passes parameters. This function ignores all offers which 
are part of trades and offers which are owned by user with netId $currentUserNetId. There is a bulit in 
limit of ten users to return. Results are returned in an array. */
function searchExchangesUserSpecific($currentUserNetId, $date, $passClub, $numPasses, $type)
{	
	// number of users to return
	$numOfUsers = 10;
	
	//Build a query
	$select = ' SELECT '; 
	$column =  ' *, x( Users.location ) AS my_point_x, y( Users.location ) AS my_point_y, Active_exchanges.id as exchangeId ';  
	$from = ' FROM ';  
	$tables = ' Active_exchanges LEFT JOIN Users ON Active_exchanges.requesterNetId=Users.netId ';
	
	// Select between searching for all clubs
	if($passClub != "All")
	{
		$where = ' WHERE Active_exchanges.passDate="' . date ("Y-m-d", strtotime($date)) . '" AND Active_exchanges.type="' .$type. '" AND Active_exchanges.passNum >= '.$numPasses.' AND Active_exchanges.passClub="'.$passClub.'" AND requesterNetId !="' .$currentUserNetId. '" AND Active_exchanges.isPartOfTransaction="0"';
	}
	else 
	{
		$where = ' WHERE Active_exchanges.passDate="' . date ("Y-m-d", strtotime($date)) .'" AND Active_exchanges.passNum >= '.$numPasses.' AND Active_exchanges.type="'.$type.'" AND requesterNetId !="' .$currentUserNetId. '" AND Active_exchanges.isPartOfTransaction="0"';
	}
	$order = " ORDER BY Users.reputation DESC ";
	 
	$query = $select . $column . $from . $tables . $where . $order;
	
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	$users = array();
	$requestedByUser = 0;
	 
	if ($query_result !== false)
	{
		// netId of the previous user whose exhanges are being added
		$previousUser = "";
		
		while(($exchange = mysql_fetch_array(($query_result))) && ($numOfUsers > 0))
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
															'photo' =>$exchange['photo'],
															'exchanges' => array(array('id' =>$exchange['exchangeId'], 
																				'club' =>$exchange['passClub'],
																				'passNum' =>$exchange['passNum'],
																				'passDate' =>$exchange['passDate'],
																				'comment' =>$exchange['comments'],
																				'type' =>$exchange['type'],
																				'requested' =>$requestedByUser))
															);
				$currentUser = $exchange['requesterNetId'];
				$numOfUsers = $numOfUsers - 1;
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

/* Helper function which returns the information about user with netId $netId in an array. */
function getUserNameByNetId($netId)
{
	
	//Build a query
	$select = ' SELECT '; 
	$column =  ' firstName ';  
	$from = ' FROM ';  
	$tables = ' Users ';
	$where = 'WHERE netId="' . $netId . '" LIMIT 1';
	$query = $select . $column . $from . $tables . $where; 
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	$query_result = mysql_fetch_array(($query_result));

	return $query_result['firstName'];
}

/* Helper function which returns an array of id of all requests made 
by user with netId $userNetId */
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

/*Function returns all offer which match the passes parameters. This function doesn't ignores all offers which 
are part of trades and offers which are owned by user any user. Results are returned in an array. */
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

/* Remove all exchanges in the array of ids $exchanges to remove 
which belong to $currentUserNetId */
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

/* Function to delete a single request which has the id $requestId and belongs to $netId */
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
	$query = ' UPDATE Active_exchanges SET associatedExchanges=\''.json_encode(array_values($associatedExchanges)).'\' WHERE id="' . $offerId[0] . '"';
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

/* Function to delete a single offer which has the id $requestId and belongs to $netId */
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
		
	if (($offer['associatedExchanges'] != "") && ($offer['associatedExchanges'] != "[]"))
	{
		// remove requests based on netids in offer's associatedExchanges
		$associatedExchanges = json_decode( $offer['associatedExchanges'] );
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

/* Function to delete request of $currentUserNetId from offer with id $offerId. */
function deleteRequestByOfferId($currentUserNetId, $requesterNetId, $offerId)
{	
	// get correspoding offer's request's id 
	$query = ' SELECT id FROM Active_exchanges WHERE requesterNetId="' . $requesterNetId . '" AND associatedExchanges LIKE "%'.$offerId.'%"';
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
	$query = ' UPDATE Active_exchanges SET associatedExchanges=\''.json_encode(array_values($associatedExchanges)).'\' WHERE id="' . $offerId . '"';
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

/* Function to complete a trade betwee $provider (netId) and $recipient ($netId).
Both users are rewarded with 1 reputation point. */
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
	
	// error: no matching offer for trade, the trade no longer exists
	if (mysql_num_rows($query_result)==0) 
	{ 
		echo "The trade is no longer active.";
		return; 
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

/* Function to cancel a trade betwee $provider (netId) and $recipient ($netId).
$currentUserNetId is penalized and loses 1 reputation point. A push notification is sent 
to the other party.*/
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
	
	// error: no matching offer for trade, the trade no longer exists
	if (mysql_num_rows($query_result)==0) 
	{ 
		return; 
	}
	
	// send a push notification
	$deviceToken = NULL; 
	if ($currentUserNetId == $provider)
	{
		$deviceToken = getDeviceId($recipient);
	}
	else 
	{
		$deviceToken = getDeviceId($provider);
	}
	
	if ($deviceToken != NULL)
	{ 
		$message = getUserNameByNetId($currentUserNetId) . " canceled  your trade.";
		shell_exec(PHP_BINDIR . "/php /opt/lampp/htdocs/php/exchange_api/pushNotification.php " . $deviceToken . " '" . $message . "'");
	}
	
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

/* Function to add a new request or offer.*/
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

/* Function to accept a request from $requesterNetId to offer with id $offerId 
owned by $currentUserNetId. All other requests for this offer are deleted. */
function acceptRequest($currentUserNetId, $requesterNetId, $offerId)
{	
	// get correspoding offer's request's id 
	$query = ' SELECT id FROM Active_exchanges WHERE requesterNetId="' . $requesterNetId . '" AND associatedExchanges LIKE "%'.$offerId.'%"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	$result = mysql_fetch_array(($query_result));
	
	// error: no matching request
	if (mysql_num_rows($query_result)==0) 
	{ 
		echo "The request has been cancelled.";
		return; 
	}
	
	
	// send a push notification
	$deviceToken = getDeviceId($requesterNetId);
	if ($deviceToken != NULL)
	{ 
		$message = getUserNameByNetId($currentUserNetId) . " accepted your offer.";
		shell_exec(PHP_BINDIR . "/php /opt/lampp/htdocs/php/exchange_api/pushNotification.php " . $deviceToken . " '" . $message . "'");
	}
	
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
	$associatedExchanges = json_decode( $offer['associatedExchanges'] );

	// temporarly remove $requesterNetId from the offer's associatedExchanges
	$associatedExchanges = array_diff($associatedExchanges, array($requesterNetId));
	
	if(!empty($associatedExchanges))
	{
		// remove all requests based on netids in offer's associatedExchanges
		$query = 'DELETE FROM Active_exchanges WHERE associatedExchanges LIKE "%'. $offerId . '%" AND requesterNetId IN ("' . implode('","', $associatedExchanges) . '")';
		//Execute the query	
		$query_result = mysql_query($query);
		//Provide an error message if the query failed
		if(!$query_result){
			die("Could not query the database. " . mysql_error());
		}
	}

	// remove all netIds but $requesterNetId from the offer's associatedExchanges
	// set that the offer is part of a transaction
	$associatedExchanges = array($requesterNetId);
	$query = ' UPDATE Active_exchanges SET isPartOfTransaction="1", associatedExchanges=\''.json_encode(array_values($associatedExchanges)).'\' WHERE id="' . $offerId . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	//set that the request is part of a transaction
	$query = ' UPDATE Active_exchanges SET isPartOfTransaction="1" WHERE id="' . $requestId . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}

/* Function to get all requests and offers in the database. 
Return an array of the offer */
function getAllExchanges()
{
	//Build a query
	$select = ' SELECT '; 
	$column =  ' *, x( Users.location ) AS my_point_x, y( Users.location ) AS my_point_y, Active_exchanges.id as exchangeId ';  
	$from = ' FROM ';  
	$tables = ' Active_exchanges LEFT JOIN Users ON Active_exchanges.requesterNetId=Users.netId ';
	 
	$query = $select . $column . $from . $tables;
	
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	$users = array();
	
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

/* Helper function for push notifications. Gets the id of the device 
to which the push notification should be send. */
function getDeviceId($netId)
{
	//Build a query
	$select = ' SELECT '; 
	$column =  ' deviceID ';  
	$from = ' FROM ';  
	$tables = ' Users ';
	$where = 'WHERE netId="' . $netId . '" LIMIT 1';
	$query = $select . $column . $from . $tables . $where; 
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	$query_result = mysql_fetch_array(($query_result));

	return $query_result['deviceID'];
}

?>