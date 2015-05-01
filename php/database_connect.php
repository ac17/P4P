<?php
// start session
session_start();
//Get login information 
require("database_login_info.php");
//Create a generic error message
$error = "An error occurred. Please refresh this page.";
//Connect to to the server 
$connection = @mysql_connect($database_host,$database_username,$database_password) or die($error);
//Select database
$database_select = @mysql_select_db($database_databaseName) or die($error);
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET COLLATION_CONNECTION = 'utf8_unicode_ci'");
// include Google PHP API
set_include_path(get_include_path() . PATH_SEPARATOR . 'google_php_api/src');

// Checks if the current user is logged in and returns TRUE or FALSE accordingly. If yes, then store the user's info in $_SESSION['user'].
function isUserLoggedIn() {
	$query = 'SELECT * FROM Users WHERE session_id="' . session_id() . '" LIMIT 1;';
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 1) {
		$_SESSION['user'] = mysql_fetch_assoc($result);
		return TRUE;
	}
	return FALSE;
}

// Use to verify email of new user.
function verify($idNum, $vc, &$connection) {
	$verifyQ = "UPDATE Users SET verified='1' WHERE id='" . $idNum. "' AND verifCode='" . $vc . "';";
	$verifyR = mysql_query($verifyQ);
	if (!$verifyR) {
		echo mysql_errno($connection) . ": " . mysql_error($connection) . "\n";
		return FALSE;
	}
	return TRUE;
}
?>
