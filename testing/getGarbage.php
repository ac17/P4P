<?php 
require('../php/database_connect.php');

// close session to allow for parallel requests
session_write_close();

// get all active exchanges older than today
$query = 'SELECT * FROM Active_exchanges WHERE passDate<"'.date('Y-m-d').'"';
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

$resultSize = 0;
$numRows = 0;
while($row = mysql_fetch_row($query_result))
{
    $lengths = mysql_fetch_lengths($query_result);
	
    foreach($lengths as $length) { 
    	$resultSize += $length; 
	}
	$numRows += 1;
}

echo '<div class="col-md-3 tableHeader">Expired Exchanges: </div><div class="col-md-3 tableCell">'.$numRows.'</div><div class="col-md-3 tableCell">'.$resultSize.' bytes</div><div class="col-md-3 tableCell"><input type="submit" value="Clean Exchanges" onClick="cleanExchanges()"></div>';


// get only messages older then five days 
$date = date_create(date('Y-m-d H:i:s'));
date_sub($date, date_interval_create_from_date_string('10 days'));

$query =  'SELECT * FROM chat_history WHERE Time<="'.date_format($date, 'Y-m-d H:i:s').'"';
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}
$result = mysql_fetch_array(($query_result));

$resultSize = 0;
$numRows = 0;
while($row = mysql_fetch_row($query_result))
{
    $lengths = mysql_fetch_lengths($query_result);
	
    foreach($lengths as $length) { 
    	$resultSize += $length; 
	}
	$numRows += 1;
}

echo '<div class="col-md-3 tableHeader">Expired chat messages: </div><div class="col-md-3 tableCell">'.$numRows.'</div><div class="col-md-3 tableCell">'.$resultSize.' bytes</div><div class="col-md-3 tableCell"><input type="submit" value="Clean Chat Messages" onClick="cleanChatMessages()"></div>';

mysql_close($connection);
 
?>