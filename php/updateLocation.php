<?php 
//Get login information 
require('./database_connect.php');
//Get the id of the note to be displayed returned
$name = $_GET["name"];
$lat = $_GET["lat"];
$lng = $_GET["lng"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$name = mysql_real_escape_string($name);
	$name = stripslashes($name);
	$lat = mysql_real_escape_string($lat);
	$lat = stripslashes($lat);
	$lng = mysql_real_escape_string($lng);
	$lng = stripslashes($lng);
}

echo $name . " " . $lat . " " . $lng;

//Build a query
$update = ' UPDATE ';   
$tables = ' Users ';
$where = ' WHERE firstName = "' . $name . '"';
$set = ' SET location = GeomFromText("POINT('.$lat.' '.$lng.')")';
$query = $update . $tables . $set . $where; 

//Execute the query
$query_result = mysql_query($query);
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}

mysql_close($connection);
?>