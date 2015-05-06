<?php 
header('Content-type: application/json');

// Get login API
include_once('php/database_connect.php');

// Get user authentication information
$un = $_GET["un"];
$pwHash = $_GET["pwHash"];

// Protect against SQL injection
if(get_magic_quotes_gpc()){
	$un = stripslashes(mysql_real_escape_string($un));
	$pwHash = stripslashes(mysql_real_escape_string($pwHash));
}

$authResults = array();
if (isValidUser($un, $pwHash))
	$authResults = array("result" => "TRUE");
else
	$authResults = array("result" => "FALSE");

// return the results array in JSON form
echo json_encode(array("authResults"=>array_values($authResults))); 
mysql_close($connection);
?>