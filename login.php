<?php
/* Login user with netId and pw, store errors in err. */
function login($netId, $pw, &$err) {
	$netId = stripslashes(htmlspecialchars($netId));
	$pw = stripslashes(htmlspecialchars($pw));
	$loginQ = "SELECT * FROM Users WHERE netId='{$netId}' AND password=MD5('{$pw}') AND verified=1";
	$loginR = mysql_query($loginQ);
	if (!$loginR) {
		$err['login_failure'] = 'Database connection error: ' . mysql_error();
		return;
	}
	elseif (mysql_num_rows($loginR) == 0) {
		$err['login_failure'] = 'Sorry, an account does not exist with this username/password combination.';
		return;
	}
	$setSessionIDQuery = "UPDATE Users SET session_id='" . session_id() . "' WHERE netId='{$netId}';";
	$setSessionIDResult = mysql_query($setSessionIDQuery);
	if (!$setSessionIDResult) {
		$err['login_failure'] = 'Database connection error: ' . mysql_error();
		return;
	}
	header('Location: dashboard.php');
	exit;
}
?>