<?php
/*  emailServerIP.php
 *	Version: 1.0
 *  Author: Artur Filipowicz
 *  Copyright (c) 2014 Artur Filipowicz
 *	Description: Script for checking if the public ip has changed and emailing the new ip.
 */
function email()
{
	// Pear Mail Library
	require_once "php/Mail-1.2.0/Mail.php";

	$from = '<passes4passes@gmail.com>';
	$to = '<artur124@gmail.com>';
	$subject = 'Public Ip Changed';
	$body = "Welcome to P4P";

	$headers = array(
			'From' => $from,
			'To' => $to,
			'Subject' => $subject
	);

	$smtp = Mail::factory('smtp', array(
			'host' => 'ssl://smtp.gmail.com',
			'port' => '465',
			'auth' => true,
			//'username' => 'passes4passes@gmail.com',
			//'password' => '##coolEmailPassword'
			'username' => 'artur124@gmail.com',
			'password' => 'mcasmet124'
		));

	$mail = $smtp->send($to, $headers, $body);

	if (PEAR::isError($mail)) {
			echo('<p>' . $mail->getMessage() . '</p>');
	} else {
			echo('<p>Message successfully sent!</p>');
	}
}

email();
?>