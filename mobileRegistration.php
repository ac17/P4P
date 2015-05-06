<?php 
header('Content-type: application/json');
// Get login information 
include_once('php/database_connect.php');
include_once('mailer.php');
include_once('php/signup.php');

// Get information of new registrant
$fName = $_GET["fName"];
$lName = $_GET["lName"];
$netId = $_GET["netId"];
$pwHash = $_GET["pwHash"];

// Protect against SQL injection
if(get_magic_quotes_gpc()){
	$fName = stripslashes(mysql_real_escape_string($fName));
	$lName = stripslashes(mysql_real_escape_string($lName));
	$netId = stripslashes(mysql_real_escape_string($netId));
}

$err = 0;
if (signup($netId, $fName, $lName, $pwHash, $err))
	$regResults = array("result" => "TRUE");
else
	$regResults = array("result" => "FALSE");

// return the results array in JSON form
echo json_encode(array("regResults"=>array_values($regResults))); 
mysql_close($connection);
 
?>