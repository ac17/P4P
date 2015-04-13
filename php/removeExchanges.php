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

//Build query
$query = 'DELETE FROM Active_exchanges WHERE requesterNetId="'. $netId . '" AND id IN (' . implode(',', array_map('intval', $exchangesToRemove)) . ')';
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

mysql_close($connection);
 
?>