<?php
require_once('../database_connect.php');

function verify($idNum, $vc) {
	$verifyQ = "UPDATE Users SET verified='TRUE' WHERE id={$idNum} AND verifCode={$vc};";
	$verifyR = mysql_query($verifyQ);
	if (!$verifyR) {
		echo mysql_errno($connection) . ": " . mysql_error($connection) . "\n";
		return FALSE;
	}
	if (mysql_num_rows($verifyR) == 0) {
		return FALSE;
	}
	else {
		return TRUE;
	}
}
?>

<html>
<head>
</head>
<body>
	<?php
		$num = $_POST['uID'];
		$verC = $_POST['vc'];
		if (verify($num, $verC))
			echo "You have been successfully verified!";
		else
			echo "Uh oh, there was a problem verifying you.";
	?>
</body>
</html>