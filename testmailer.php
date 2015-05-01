<html>
<?php
	include_once('mailer.php');
	if (isset($_POST['hiddenMsg']) && $_POST['hiddenMsg'] == 'true') {
		$mailResult = "";
		if (mailer($_POST['emailName'], $_POST['emailAdd'], $_POST['emailSub'], $_POST['emailMsg'], $mailResult))
			echo "Thanks, your message has been sent!";
		else
			echo "Sorry, something went wrong! Please try again.";
	}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="testMailer">
	<input type="text" placeholder="Recipient Name" id="emailName" name="emailName">
	<input type="email" placeholder="ac17@princeton.edu" id="emailAdd" name="emailAdd">
	<input type="subject" placeholder="Subject" id="emailSub" name="emailSub">
	<input type="message" placeholder="Message" id="emailMsg" name="emailMsg">
    <input type="hidden" name="hiddenMsg" id="hiddenMsg" value="true">
    <button class="btn btn-default" id="msgSubmit" type="submit" form="testMailer" value="Submit">Submit</button>
</form>
</html>