<?php 
//Get login information 
require('../php/database_connect.php');
//Get the id of the note to be displayed returned
$netId = $_GET["netId"];
$passDate = $_GET["passDate"];
$type = $_GET["type"];
$numPasses = $_GET["numPasses"];
$passType = $_GET["club"];
$comment = $_GET["comment"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$netId = stripslashes(mysql_real_escape_string($netId));
	$passDate = stripslashes(mysql_real_escape_string($passDate));
	$type = stripslashes(mysql_real_escape_string($type));
	$numPasses = stripslashes(mysql_real_escape_string($numPasses));
	$passType = stripslashes(mysql_real_escape_string($passType));
	$comment = stripslashes(mysql_real_escape_string($comment));
}

//Build query
$query = 'INSERT INTO Active_exchanges VALUES(NULL,"'. $netId . '","' . $passType . '","' . $numPasses . '","' . date ("Y-m-d", strtotime($passDate)) . '","' . $comment . '","' . $type. '")';
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

mysql_close($connection);
 
?>