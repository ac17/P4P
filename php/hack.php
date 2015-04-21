<?php

$url = 'http://skedg.tk/events/add/';
$myvars = 'csrfmiddlewaretoken=uB5bM2wigcKeWqRfeLQ2uL6dAZ1SNPnO&title=sadsa&desc=&start_date=&end_date&time_range=&event_length=&invitees=&creator=art124';

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_REFERER, 'http://www.google.com');

$response = curl_exec( $ch );

echo $response;

?>