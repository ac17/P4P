<?php
/**
 * Use PHP Mailer to send msg (in HTML format) to the recipent with name recipName
 * and email recipEmail. Returns TRUE upon success and FALSE upon error.
 */

function mailer($recipName, $recipEmail, $subject, $msg) {
	/* path to php mailer files */
	$path = 'PHPMailer-master';
	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
	date_default_timezone_set('America/New_York');

	require 'PHPMailer-master/PHPMailerAutoload.php';

	//Create a new PHPMailer instance
	$mail = new PHPMailer(true);

	//Tell PHPMailer to use SMTP
	$mail->isSMTP();

	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;

	//Ask for HTML-friendly debug output
	$mail->Debugoutput = 'html';

	//Set the hostname of the mail server
	$mail->Host = 'smtp.gmail.com';

	//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
	$mail->Port = 587;

	//Set the encryption system to use - ssl (deprecated) or tls
	$mail->SMTPSecure = 'tls';

	//Whether to use SMTP authentication
	$mail->SMTPAuth = true;

	//Username to use for SMTP authentication - use full email address for gmail
	$mail->Username = "passesforpasses@gmail.com";

	//Password to use for SMTP authentication
	$mail->Password = htmlspecialchars("P4p333!!");

	//Set who the message is to be sent from
	$mail->setFrom('pass.esforpasses@gmail.com', 'Passes for Passes');

	//Set an alternative reply-to address
	$mail->addReplyTo('pass.esforpasses@gmail.com', 'Passes for Passes');

	//Set who the message is to be sent to
	$mail->addAddress($recipEmail, $recipName);

	//Set the subject line
	$mail->Subject = $subject;

	//Read an HTML message body from msg
	//convert HTML into a basic plain-text alternative body
	$mail->msgHTML($msg);

	//send the message, check for errors
	if (!$mail->send()) {
	    return TRUE;
	} else {
	    return FALSE;
	}
}

?>