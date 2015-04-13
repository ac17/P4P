<?php
//Get login information 
require("database_login_info.php");
//Create a generic error message
$error = "An error occoured. Please refresh this page.";
//Connect to to the server 
$connection = @mysql_connect($database_host,$database_username,$database_password) or die($error);
//Select database
$database_select = @mysql_select_db($database_databaseName) or die($error);
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET COLLATION_CONNECTION = 'utf8_unicode_ci'");
?>
