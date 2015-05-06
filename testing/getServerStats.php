<?php 
require('../php/database_connect.php');

ini_set('memory_limit','1000M');

// close session to allow for parallel requests
session_write_close();

$query =  'SELECT COUNT(*) FROM Users';
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}
$num = mysql_fetch_array(($query_result));

echo '<div class="col-md-6 tableHeader">Users: </div><div class="col-md-6 tableCell">'.$num[0].'</div>';

$query = 'SELECT COUNT(*) FROM Active_exchanges';
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}
$num = mysql_fetch_array(($query_result));

echo '<div class="col-md-6 tableHeader">Active Exchanges: </div><div class="col-md-6 tableCell">'.$num[0].'</div>';

$query = 'SELECT COUNT(*) FROM Exchange_history';
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}
$num = mysql_fetch_array(($query_result));

echo '<div class="col-md-6 tableHeader">Trades in Exchange_history: </div><div class="col-md-6 tableCell">'.$num[0].'</div>';

$query =  'SELECT COUNT(*) FROM chat_history';
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}
$num = mysql_fetch_array(($query_result));

echo '<div class="col-md-6 tableHeader">Chat messages: </div><div class="col-md-6 tableCell">'.$num[0].'</div>';


$query =  "SHOW GLOBAL STATUS WHERE `variable_name` = 'Threads_connected'";
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}
$num = mysql_fetch_array(($query_result));

echo '<div class="col-md-6 tableHeader">Current Connections: </div><div class="col-md-6 tableCell">'.$num[1].'</div>';

$query =  "SHOW GLOBAL STATUS WHERE `variable_name` = 'Questions'";
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}
$num = mysql_fetch_array(($query_result));

echo '<div class="col-md-6 tableHeader">Questions: </div><div class="col-md-6 tableCell">'.$num[1].'</div>';

mysql_close($connection);


echo '<div class="col-md-6 tableHeader">addExchange() Average Time: </div><div class="col-md-6 tableCell">'.avgTime('./runtimeData/addExchange.txt').'</div>';
echo '<div class="col-md-6 tableHeader">getUserLocation() Average Time: </div><div class="col-md-6 tableCell">'.avgTime('./runtimeData/getUserLocation.txt').'</div>';
echo '<div class="col-md-6 tableHeader">updateLocation() Average Time: </div><div class="col-md-6 tableCell">'.avgTime('./runtimeData/updateLocation.txt').'</div>';
echo '<div class="col-md-6 tableHeader">searchExchangesUserSpecific() Average Time: </div><div class="col-md-6 tableCell">'.avgTime('./runtimeData/searchExchangesUserSpecific.txt').'</div>';
echo '<div class="col-md-6 tableHeader">pursueOffer() Average Time: </div><div class="col-md-6 tableCell">'.avgTime('./runtimeData/pursueOffer.txt').'</div>';
echo '<div class="col-md-6 tableHeader">userActiveExchanges() Average Time: </div><div class="col-md-6 tableCell">'.avgTime('./runtimeData/userActiveExchanges.txt').'</div>';
echo '<div class="col-md-6 tableHeader">deleteRequest() Average Time: </div><div class="col-md-6 tableCell">'.avgTime('./runtimeData/deleteRequest.txt').'</div>';
echo '<div class="col-md-6 tableHeader">deleteOffer() Average Time: </div><div class="col-md-6 tableCell">'.avgTime('./runtimeData/deleteOffer.txt').'</div>';
echo '<div class="col-md-6 tableHeader">acceptRequest() Average Time: </div><div class="col-md-6 tableCell">'.avgTime('./runtimeData/acceptRequest.txt').'</div>';
echo '<div class="col-md-6 tableHeader">userActiveTrades() Average Time: </div><div class="col-md-6 tableCell">'.avgTime('./runtimeData/userActiveTrades.txt').'</div>';
echo '<div class="col-md-6 tableHeader">cancelTrade() Average Time: </div><div class="col-md-6 tableCell">'.avgTime('./runtimeData/cancelTrade.txt').'</div>';
echo '<div class="col-md-6 tableHeader">completeTrade() Average Time: </div><div class="col-md-6 tableCell">'.avgTime('./runtimeData/completeTrade.txt').'</div>';

function avgTime($file)
{
	$lines = file($file, FILE_IGNORE_NEW_LINES);
	$total = 0;
	$dataPoints = 0; 
	
	foreach ($lines as $line)
	{
		$temp = explode(" ", $line);
		$total = $total + $temp[0];
		$dataPoints = $dataPoints + 1;
	}
	
	return $total/$dataPoints;
}
?>