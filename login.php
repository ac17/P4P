<?
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

if (isset($_POST['enter'])) {
	if ($_POST['netID'] != "") {
		$_SESSION['netID'] = stripslashes(htmlspecialchars($_POST['name']));
	}
	else {
		echo '<span class="error">Please type in a netID.</span>';
	}
}
?>