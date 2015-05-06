<?php
include_once('database_connect.php');
?>

<html>
<head>
</head>
<body>
	<?php
		$num = $_GET['uID'];
		$verC = $_GET['vc'];
		if (verify($num, $verC, $connection))
			echo "You have been successfully verified!";
		else
			echo "Uh oh, there was a problem verifying you.";
	?>
</body>
</html>