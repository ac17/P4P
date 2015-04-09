<?
require_once('database_connect.php');
session_start();

function loginForm() {
	echo '
	<div id="loginform">
		<form action="index.php" method="post">
			<p>Please enter your netid to continue:</p>
			<label for="netID">NetID:</label>
			<input type="text" name="netID" id="netID">
			<input type="submit" name="enter" id="enter" value="Enter">
		</form>
	</div>';
}

function authenticate($username) {
	$results = mysql_query('SELECT * FROM Users WHERE netID="' . $username .'";');
	if (mysql_num_rows($results) == 0)
		return False;
	else {
		$row = mysql_fetch_assoc($results);
		return $row;
	}
}

if (isset($_POST['enter'])) {
	if ($_POST['netID'] != "") {
		$username = stripslashes(htmlspecialchars($_POST['netID']));
		$userInfo = authenticate($username);
		if ($userInfo) {
			echo 'Hello, ' . $userInfo['firstName'] . '!';
		}
		else {
			echo "Uh oh, you're not in our database!";
		}
	}
	else {
		echo '<span class="error">Please type in a netID.</span>';
	}
}
?>