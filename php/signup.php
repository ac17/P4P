<?php

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

/* Enter account info in table and send verification email. */
function signup($netId, $fName, $lName, $pw, &$err) {
	$netId = stripslashes(htmlspecialchars($netId));
	$fName = stripslashes(htmlspecialchars($fName));
	$lName = stripslashes(htmlspecialchars($lName));
	$pw = stripslashes(htmlspecialchars($pw));
	
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
	$query = "INSERT INTO Users (firstName, lastName, netId, password, verified, verifCode) VALUES ('" . $fName . "', '" . $lName . "', '" . $netId . "', md5('" . $pw . "'), 'FALSE', '{$vc}');";
	$result = mysql_query($query);
	if (!$result) {
		echo mysql_errno($connection) . ": " . mysql_error($connection) . "\n";
		return FALSE;
	}

	/* get ID */
	$uIDRow = mysql_fetch_assoc(mysql_query("SELECT id FROM Users WHERE netId='" . $netId . "';"));
	$uID = $uIDRow['id'];

	/* send verification email */
	$to = $netId . "@princeton.edu";
	$subject = "Welcome to Passes for Passes!";
	$headers = "From: ec2-54-149-32-72.us-west-2.compute.amazonaws.com";
	$headers .= "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
	$message = "
	<html>
	<head>
		<title>Welcome to Passes for Passes!</title>
	</head>
	<body>
		<p>Hi {$fName}!</p>
		<p>We're so excited to have you join Passes for Passes. In order to verify your account, please click <a href='http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/php/verification.php?uID={$uID}&vc={$vc}'>here</a>. If this wasn't you, then just ignore this email.</p>
		<p>Best,<br>The Passes for Passes Team</p>
	</body>
	</html>";
	/* mail($to, $subject, "Testing!"); */
	return TRUE;
}
?>