<?php 
header('Content-type: application/json');
//Get login information 
require('./database_connect.php');
require('exchange_api/exchangeAPI.php');

/* Function to get all requests and offers in the database. Return an array. */
$users = getAllExchanges();

// return the users array index numerically
echo json_encode(array("Users"=>array_values($users))); 
mysql_close($connection);
 
?>