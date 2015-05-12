<?php
//Get login information 
require('../php/database_connect.php');
require('../php/exchange_api/exchangeAPI.php');

$heatbeats = $_GET['hb'];
$actions = file_get_contents( "php://input" );
$actions = json_decode( $actions );
$numActions = count($actions) - 1; 

if (empty($actions))
{
	exit(); 
}
// close session to allow for parallel requests
session_write_close();

$filename = 'NPCs.txt';
$userIds = file($filename, FILE_IGNORE_NEW_LINES);

set_time_limit (60000);

// For all NPCs perform $heatbeats random actions specified by $actions array 
foreach ($userIds as $userId)
{
	$user = loadUserInfo($userId);
	for ($h = 0; $h < $heatbeats; $h++)
	{		
		$choice = $actions[mt_rand(0, $numActions)];
		
		switch ($choice) {
			case "moveUser":
				moveUser($user['netId']);
				break; 
			case "randomOffer":
				randomOffer($user['netId']);
				break;
			case "seekAndPursueOffer":
				seekAndPursueOffer($user['netId']);
				break;
			case "manageOffersAndRequests":
				manageOffersAndRequests($user['netId']);
				break;
			case "trade":
				trade($user['netId']);
				break;
		}
		sleep(0.1);
	}
	
	echo "----------------------------------------------------------------------------<br /><br />";
}

mysql_close($connection);

//--------------------------------------------------------

function loadUserInfo($userId)
{
	return mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE id='" . $userId . "';"));
}

function sendRequest()
{
	userActiveTrades($currentUserNetId);
	userActiveExchanges($currentUserNetId);
	searchExchangesUserSpecific($currentUserNetId, $date, $passClub, $numPasses, $type);
	removeExchanges($currentUserNetId, $exchangesToRemove);
	completeTrade($currentUserNetId, $provider, $recipient, $offerId, $requestId);
	cancelTrade($currentUserNetId, $provider, $recipient, $offerId, $requestId);
}

//----------------------------------------------------------------------------------------------------------------------
// Creating offers
function randomOffer($netId)
{
	$passDate = "05/" . mt_rand(3,9) . "/2015";
	$numPasses = mt_rand(1,3);
	$clubs = ["Ivy Club", "Tower Club", "Tiger Inn", "Colonial", "Cottage", "Cap and Gown", "Cannon"];
	$passClub = $clubs[mt_rand(0,4)];
	$comment = "";
	echo "Adding offer for " . $netId . " Date " . $passDate . " Num " . $numPasses . " Club " . $passClub . "<br />";
	
	$question_start = getNumQuestions();
	$time_start = microtime(true);
		$output = addExchange($netId, $passDate, "Offer", $numPasses, $passClub, $comment);
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	$question_end = getNumQuestions();
	$delta_q = $question_end - $question_start;
	file_put_contents('./runtimeData/addExchange.txt', $time . " " . $delta_q . " " . getTableNumRow("Active_exchanges") . "\n", FILE_APPEND | LOCK_EX);
	echo "Function runtime: " . $time . " seconds<br />";
	echo "Function output: <br />";
	var_dump($output);
	echo"<br />";
}

//----------------------------------------------------------------------------------------------------------------------
// seekAndPursueOffer 

// create a random search and pursue up to 3 offers which satisfy it 
function seekAndPursueOffer($netId)
{
	$passDate = "04/" . mt_rand(26,30) . "/2015";
	$numPasses = mt_rand(1,3);
	$clubs = ["Ivy Club", "Tower Club", "Tiger Inn", "Colonial", "Cottage", "Cap and Gown", "Cannon", "All"];
	$passClub = $clubs[mt_rand(0,4)];
	
	echo "Searching for offer for  Date " . $passDate . " Num " . $numPasses . " Club " . $passClub . "<br />";	
	$question_start = getNumQuestions();
	$time_start = microtime(true);
		$users = searchExchangesUserSpecific($netId, $passDate, $passClub, $numPasses, "Offer");
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	$question_end = getNumQuestions();
	$delta_q = $question_end - $question_start;
	file_put_contents('./runtimeData/searchExchangesUserSpecific.txt', $time . " " . $delta_q . " " . getTableNumRow("Active_exchanges") . "\n", FILE_APPEND | LOCK_EX);
	echo "Function runtime: " . $time . " seconds<br />";
	echo "Function output: <br />";
	var_dump($users);
	echo"<br />";
	
	$numUsers = count($users);
	$selectedOffers = array(); 
	
	if($numUsers > 0)
	{
		$keys = array_keys($users);
		
		// try to pursue up to three random offers
		for ($x = 0; $x < 3; $x++) 
		{
			$randUser = mt_rand(0,$numUsers-1);
			
			foreach ( $users[$keys[$randUser]]['exchanges'] as $exchange )
			{
				if(($exchange['requested'] != 1) && (! in_array($exchange['id'], $selectedOffers)))
				{
					echo "Pursuing offer form " . $users[$keys[$randUser]]['netId'] . "<br />";	
					$question_start = getNumQuestions();
					$time_start = microtime(true);
						$output = pursueOffer($netId, $exchange['id']);
					$time_end = microtime(true);
					$time = $time_end - $time_start;
					$question_end = getNumQuestions();
					$delta_q = $question_end - $question_start;
					file_put_contents('./runtimeData/pursueOffer.txt', $time . " " . $delta_q . " " . getTableNumRow("Active_exchanges") . "\n", FILE_APPEND | LOCK_EX);
					echo "Function runtime: " . $time . " seconds<br />";
					echo "Function output: <br />";
					var_dump($output);
					echo"<br />";
					
					if($output == NULL)
					{
						array_push($selectedOffers, $exchange['id']);
					}
					break;
				}
			}
		} 
	}
}


//----------------------------------------------------------------------------------------------------------------------
// manage Offers And Requests
 
function manageOffersAndRequests($netId)
{	
	echo "Getting active exchanges for ".$netId." <br />";	
	$question_start = getNumQuestions();
	$time_start = microtime(true);
		$exchanges = userActiveExchanges($netId);
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	$question_end = getNumQuestions();
	$delta_q = $question_end - $question_start;
	file_put_contents('./runtimeData/userActiveExchanges.txt', $time . " " . $delta_q . " " . getTableNumRow("Active_exchanges") . "\n", FILE_APPEND | LOCK_EX);
	echo "Function runtime: " . $time . " seconds<br />";
	echo "Function output: <br />";
	var_dump($exchanges);
	echo"<br />";
	
	
	foreach ( $exchanges as $exchange )
	{
		if($exchange['type'] == "Request")
		{
			// decide to delete the request or do nothing
			$choice = mt_rand(0,1);			
			if($choice == 0)
			{
				echo "Deleting request: " . $exchange['passDate'] . " " . $exchange['club'] . " " . $exchange['passNum'] .  "<br />";	
				$question_start = getNumQuestions();
				$time_start = microtime(true);
					$output = deleteRequest($netId, $exchange['id']);
				$time_end = microtime(true);
				$time = $time_end - $time_start;
				$question_end = getNumQuestions();
				$delta_q = $question_end - $question_start;
				file_put_contents('./runtimeData/deleteRequest.txt', $time . " " . $delta_q . " " . getTableNumRow("Active_exchanges") . "\n", FILE_APPEND | LOCK_EX);
				echo "Function runtime: " . $time . " seconds<br />";
				echo "Function output: <br />";
				var_dump($output);
				echo"<br />";
			}
		}
		else 
		{
			// decide to delete the offer, accept a request for it or do nothing
			$choice = mt_rand(0,2);			
			if($choice == 0)
			{
				echo "Deleting offer: " . $exchange['passDate'] . " " . $exchange['club'] . " " . $exchange['passNum'] .  "<br />";	
				$question_start = getNumQuestions();
				$time_start = microtime(true);
					$output = deleteOffer($netId, $exchange['id']);
				$time_end = microtime(true);
				$time = $time_end - $time_start;
				$question_end = getNumQuestions();
				$delta_q = $question_end - $question_start;
				file_put_contents('./runtimeData/deleteOffer.txt', $time . " " . $delta_q . " " . getTableNumRow("Active_exchanges") . "\n", FILE_APPEND | LOCK_EX);
				echo "Function runtime: " . $time . " seconds<br />";
				echo "Function output: <br />";
				var_dump($output);
				echo"<br />";
			}
			elseif($choice == 1)
			{
				$requestsForOffer = json_decode($exchange['associatedExchanges']);
				$numRequests = count($requestsForOffer); 
				
				if ($numRequests > 0)
				{
					$acceptedRequest = mt_rand(0,$numRequests-1);
					
					echo "Accepting request from ". $requestsForOffer[$acceptedRequest] ." for offer: " . $exchange['passDate'] . " " . $exchange['club'] . " " . $exchange['passNum'] .  "<br />";	
					$question_start = getNumQuestions();
					$time_start = microtime(true);
						$output = acceptRequest($netId, $requestsForOffer[$acceptedRequest], $exchange['id']);
					$time_end = microtime(true);
					$time = $time_end - $time_start;
					$question_end = getNumQuestions();
					$delta_q = $question_end - $question_start;
					file_put_contents('./runtimeData/acceptRequest.txt', $time . " " . $delta_q . " " . getTableNumRow("Active_exchanges") . "\n", FILE_APPEND | LOCK_EX);
					echo "Function runtime: " . $time . " seconds<br />";
					echo "Function output: <br />";
					var_dump($output);
					echo"<br />";
				}
			}
		} 
	}
	
}

//----------------------------------------------------------------------------------------------------------------------
//  trade
 
function trade($netId)
{
	echo "Getting trades for ".$netId." <br />";
	$question_start = getNumQuestions();
	$time_start = microtime(true);
		$trades = userActiveTrades($netId);
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	$question_end = getNumQuestions();
	$delta_q = $question_end - $question_start;
	file_put_contents('./runtimeData/userActiveTrades.txt', $time . " " . $delta_q . " " . getTableNumRow("Active_exchanges") . "\n", FILE_APPEND | LOCK_EX);
	echo "Function runtime: " . $time . " seconds<br />";
	echo "Function output: <br />";
	var_dump($trades);
	echo"<br />";
	
	foreach ( $trades as $trade )
	{
		// decide to cancel or accept the trade or do nothing
		$choice = mt_rand(0,2);			
		if($choice == 0)
		{
			echo "Cancelling trade with ". $trade['provider'] . "/" . $trade['recipient'] ." for: " . $trade['passDate'] . " " . $trade['club'] . " " . $trade['passNum'] .  "<br />";	
			$question_start = getNumQuestions();
			$time_start = microtime(true);
				$output = cancelTrade($netId, $trade['provider'], $trade['recipient'], $trade['offerId'], $trade['requestId']);
			$time_end = microtime(true);
			$time = $time_end - $time_start;
			$question_end = getNumQuestions();
			$delta_q = $question_end - $question_start;
			file_put_contents('./runtimeData/cancelTrade.txt', $time . " " . $delta_q . " " . getTableNumRow("Active_exchanges") . "\n", FILE_APPEND | LOCK_EX);
			echo "Function runtime: " . $time . " seconds<br />";
			echo "Function output: <br />";
			var_dump($output);
			echo"<br />";
		}
		elseif($choice == 1)
		{
			echo "Completing trade with ". $trade['provider'] . "/" . $trade['recipient'] ." for: " . $trade['passDate'] . " " . $trade['club'] . " " . $trade['passNum'] .  "<br />";
			$question_start = getNumQuestions();
			$time_start = microtime(true);
				$output = completeTrade($netId, $trade['provider'], $trade['recipient'], $trade['offerId'], $trade['requestId']);
			$time_end = microtime(true);
			$time = $time_end - $time_start;
			$question_end = getNumQuestions();
			$delta_q = $question_end - $question_start;
			file_put_contents('./runtimeData/completeTrade.txt', $time . " " . $delta_q . " " . getTableNumRow("Active_exchanges") . "\n", FILE_APPEND | LOCK_EX);
			echo "Function runtime: " . $time . " seconds<br />";
			echo "Function output: <br />";
			var_dump($output);
			echo"<br />";
		}
	}
}

//----------------------------------------------------------------------------------------------------------------------
// Moving users 
function moveUser($netId)
{
	$maxLat = 40.35266;
	$minLat = 40.342521;
	$maxLng = -74.658773;
	$minLng = -74.649675;
	
	echo "Getting ".$netId."'s loction<br />";
	$question_start = getNumQuestions(); 
	$time_start = microtime(true);
		$loc = getUserLocation($netId);
	$time_end = microtime(true);
	$question_end = getNumQuestions();
	$time = $time_end - $time_start;
	$delta_q = $question_end - $question_start;
	file_put_contents('./runtimeData/getUserLocation.txt', $time . " " . $delta_q . " " . getTableNumRow("Users") . "\n", FILE_APPEND | LOCK_EX);
	echo "Function runtime: " . $time . " seconds<br />";
	echo "Function output: <br />";
	var_dump($loc);
	echo"<br />";
	
	$lat = (((mt_rand( 0, 9))*2-9)/100000);
	$lng = (((mt_rand( 0, 9))*2-9)/100000);
	
	echo "Changing location of " . $netId . " from " . $loc['point_x'] . " " . $loc['point_y'] . " by " . $lat . " " . $lng . "<br />";
	
	if ( $loc['point_x'] + $lat < $maxLat && $minLat < $loc['point_x'] + $lat && $loc['point_y'] > $maxLng  + $lng && $loc['point_y'] + $lng < $minLng )
	{
		$time_start = microtime(true);
		$question_start = getNumQuestions();
			$output = updateLocation($netId, $loc['point_x'] + $lat, $loc['point_y'] + $lng);
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$question_end = getNumQuestions();
		$delta_q = $question_end - $question_start;
		file_put_contents('./runtimeData/updateLocation.txt', $time . " " . $delta_q . " " . getTableNumRow("Users") . "\n", FILE_APPEND | LOCK_EX);
		echo "Function runtime: " . $time . " seconds<br />";
		echo "Function output: <br />";
		var_dump($output);
		echo"<br />";
	}
	else 
	{
		$lat = ($maxLat - $minLat)*(mt_rand( 1, 100)/100) + $minLat;
		$lng = ($maxLng - $minLng)*(mt_rand( 1, 100)/100) + $minLng;
		
		$time_start = microtime(true);
		$question_start = getNumQuestions();
			$output = updateLocation($netId, $lat, $lng);
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$question_end = getNumQuestions();
		$delta_q = $question_end - $question_start;
	file_put_contents('./runtimeData/updateLocation.txt', $time . " " . $delta_q . " " . getTableNumRow("Users") . "\n", FILE_APPEND | LOCK_EX);
		echo "Function runtime: " . $time . " seconds<br />";
		echo "Function output: <br />";
		var_dump($output);
		echo"<br />";
	}
}

function getUserLocation($netId)
{
//Build a query
	$query = ' SELECT x( Users.location ) AS point_x, y( Users.location ) AS point_y FROM Users WHERE netId="' . $netId . '"';
	//Execute the query
	$query_result = mysql_query($query);
	//Provide an error message if the query failed
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	
	return mysql_fetch_array($query_result);
}

function updateLocation($currentUserNetId, $lat, $lng)
{
	//Build a query
	$update = ' UPDATE ';   
	$tables = ' Users ';
	$where = ' WHERE netId = "' . $currentUserNetId . '"';
	$set = ' SET location = GeomFromText("POINT('.$lat.' '.$lng.')")';
	$query = $update . $tables . $set . $where; 
	
	//Execute the query
	$query_result = mysql_query($query);
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}

//----------------------------------------------------------------------------------------------------------------------
// Stat Functions 
function getNumQuestions()
{
	$query_result = mysql_query("SHOW GLOBAL STATUS WHERE `variable_name` = 'Questions'");
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	$num = mysql_fetch_array(($query_result));	
	return $num[1];
}

function getTableNumRow($table)
{
	$query =  'SELECT COUNT(*) FROM ' . $table;
	$query_result = mysql_query($query);
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
	$num = mysql_fetch_array(($query_result));

	return $num[0];	
}
?>