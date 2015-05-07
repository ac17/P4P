<?php 
header('Content-type: application/json');

// Get login API
include_once('php/database_connect.php');

// Get user authentication information
$un = $_GET["un"];
$pwHash = $_GET["pwHash"];
$deviceID = $_GET["deviceID"];

// Protect against SQL injection
if(get_magic_quotes_gpc()){
	$un = stripslashes(mysql_real_escape_string($un));
	$pwHash = stripslashes(mysql_real_escape_string($pwHash));
	$deviceID = stripslashes(mysql_real_escape_string($deviceID));
}

// authenticate user and return user's info if user is valid
$resultArray = array();
if (isValidUser($un, $pwHash)) {
	// change device ID
	$query = "UPDATE Users SET deviceID='" . $deviceID . "' WHERE netId='" . $un . "';";
	mysql_query($query);

	// load user data
	$query = "SELECT * FROM Users WHERE netId='" . $un . "';";
	$result = mysql_query($query);
	if ($result) {
		$resultArray[] = mysql_fetch_assoc($result);
		foreach ($resultArray as &$x)
			array_splice($x, 4, 1);

		/* $row = mysql_fetch_assoc($result);
		$resultArray['id'] = $row['id'];
		$resultArray['firstName'] = $row['firstName'];
		$resultArray['lastName'] = $row['lastName'];
		$resultArray['photo'] = $row['photo'];
		$resultArray['netId'] = $row['netId'];
		$resultArray['reputation'] = $row['reputation'];
		$resultArray['password'] = $row['password']; */
	}
}

// return the results array in JSON form
echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
?>