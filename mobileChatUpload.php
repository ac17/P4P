<?php 
header('Content-type: application/json');
// Get login information 
include_once('php/database_connect.php');

// Get information of users in chat (password hash is for from_user)
$from_user = $_GET["from_user"];
$to_user = $_GET["to_user"];
$msg = $_GET["msg"];
$pwHash1 = $_GET["pwHash1"];

// Protect against SQL injection
if(get_magic_quotes_gpc()){
	$from_user = stripslashes(mysql_real_escape_string($from_user));
	$to_user = stripslashes(mysql_real_escape_string($to_user));
	$msg = stripslashes(mysql_real_escape_string(htmlspecialchars($msg)));
	$pwHash1 = stripslashes(mysql_real_escape_string($pwHash1));
}

/* Check if user1 is valid and logged in. If so, insert the chat history between user 1 and user 2 into an array. Else, return an empty array. */
$resultArray = array();
if (isValidUser($from_user, $pwHash1)) {
	$query = "INSERT INTO chat_history (User_From, User_To, Time, Conversation) VALUES ('" . $from_user . "', '" . $to_user . "', now(), '" . $msg . "');";
	$result = mysql_query($query);
	if ($result) {
		$resultArray = array("result" => "TRUE");
	}
	else
		$resultArray = array("result" => "FALSE");
}
// return the results array in JSON form
echo json_encode($resultArray); 
mysql_close($connection);
 
?>