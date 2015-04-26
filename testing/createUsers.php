<?php

//Get login information 
require('../php/database_connect.php');
require('./randName.php');

$fullName = getRandomName();
$fullName = explode(" ", $fullName);

	$netId = $fullName[1] . substr($fullName[0], 0, 2);
	$fName = $fullName[0];
	$lName = $fullName[1];
	$pw = "fred";
	
	echo $netId . " " . $fName . " " . $lName . " " . $pw . "<br />";
	
	/* check for duplicate */
	$checkForDupQ = "SELECT * FROM Users WHERE netId='{$netId}';";
	$dupResults = mysql_query($checkForDupQ);
	if ($dupResults) {
		$numDup = mysql_num_rows($dupResults);
		if ($numDup > 0) {
			$err = -1;
			return FALSE;
		}
	}
	else {
		echo mysql_errno($connection) . ": " . mysql_error($connection) . "\n";
		return FALSE;
	}
	
	/* generate random verification code and insert non-verified user info into table */
	$vc = randString(12);
	$query = "INSERT INTO Users (firstName, lastName, netId, password, verified, verifCode) VALUES ('" . $fName . "', '" . $lName . "', '" . $netId . "', md5('" . $pw . "'), '1', '{$vc}');";
	$result = mysql_query($query);
	if (!$result) {
		echo mysql_errno($connection) . ": " . mysql_error($connection) . "\n";
		return FALSE;
	}

	/* get ID */
	$uIDRow = mysql_fetch_assoc(mysql_query("SELECT id FROM Users WHERE netId='" . $netId . "';"));
	$uID = $uIDRow['id'];
	
	$file = 'NPCs.txt';
	file_put_contents($file, $uID . "\n", FILE_APPEND | LOCK_EX);
	

	// give user an initial location
	$maxLat = 40.35266;
	$minLat = 40.342521;
	$maxLng = -74.658773;
	$minLng = -74.649675;
	
	$lat = ($maxLat - $minLat)*(mt_rand( 1, 100)/100) + $minLat;
	$lng = ($maxLng - $minLng)*(mt_rand( 1, 100)/100) + $minLng;
	
	updateLocation($netId, $lat, $lng);
	
/* generate a random alphanumeric string */
function randString($length)
{
	$charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $str = '';
    $count = strlen($charset);
    while ($length--) {
        $str .= $charset[mt_rand(0, $count-1)];
    }
    return $str;
}

function updateLocation($currentUserNetId, $lat, $lng)
{
	//Build a query
	$update = ' UPDATE ';   
	$tables = ' Users ';
	$where = ' WHERE netId = "' . $currentUserNetId . '"';
	$set = ' SET location = GeomFromText("POINT('.$lat.' '.$lng.')")';
	$query = $update . $tables . $set . $where; 
	
	//Execute the query
	$query_result = mysql_query($query);
	if(!$query_result){
		die("Could not query the database. " . mysql_error());
	}
}

?>