<?php 
require('../php/database_connect.php');

// close session to allow for parallel requests
session_write_close();

/* Delete requests and offers in the table older than today. */
$query = 'DELETE FROM Active_exchanges WHERE passDate<"'.date('Y-m-d').'"';
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

mysql_close($connection);
 
?>