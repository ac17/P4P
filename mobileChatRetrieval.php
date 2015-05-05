<?php 
header('Content-type: application/json');
// Get login information 
include_once('php/database_connect.php');

// Get information of new registrant
$user1 = $_GET["user1"];
$user2 = $_GET["user2"];
$pwHash1 = $_GET["pwHash1"];

// Protect against SQL injection
if(get_magic_quotes_gpc()){
	$user1 = stripslashes(mysql_real_escape_string($user1));
	$user2 = stripslashes(mysql_real_escape_string($user2));
	$pwHash1 = stripslashes(mysql_real_escape_string($pwHash1));
}

/* Check if user1 is valid and logged in. If so, insert the chat history between user 1 and user 2 into an array. Else, return an empty array. */
$resultArray = array();
if (isValidUser($user1, $pwHash1)) {
	$query = "SELECT * FROM chat_history WHERE User_From='" . $user1 . "' OR User_To='" . $user1 . "';";
	$result = mysql_query($query);
	if ($result) {
		while ($row = mysql_fetch_assoc($result)) {
			if ($row['User_From'] == $user2 || $row['User_To'] == $user2)
				$resultArray[] = $row;
		}
	}
}

// return the results array in JSON form
echo json_encode($resultArray); 
mysql_close($connection);
 
?>