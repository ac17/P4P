<?php 
require('../php/database_connect.php');

// close session to allow for parallel requests
session_write_close();

// get only messages older then five days 
$date = date_create(date('Y-m-d H:i:s'));
date_sub($date, date_interval_create_from_date_string('10 days'));

/* Delete all chat messages older than 10 days. */
$query =  'DELETE FROM chat_history WHERE Time<="'.date_format($date, 'Y-m-d H:i:s').'"';
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

mysql_close($connection);
 
?>