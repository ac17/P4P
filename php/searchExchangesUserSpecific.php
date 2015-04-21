<?php 
header('Content-type: application/json');
//Get login information 
require('./database_connect.php');

//Get the id of the note to be displayed returned
$userNetId = $_GET["netId"];
$date = $_GET["date"];
$passClub = $_GET["club"];
$numPasses = $_GET["numPasses"];
$type = $_GET["type"];

//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$date = stripslashes(mysql_real_escape_string($date));
}

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
		if(strpos($exchange['associatedExchanges'],$userNetId) !== false)
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

// return the users array index numerically
echo json_encode(array("Users"=>array_values($users))); 
mysql_close($connection);

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
?>