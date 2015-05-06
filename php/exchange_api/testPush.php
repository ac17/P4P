<?php

$deviceToken = '8354187b9656beb7c54c15239fef3696d03194586976b9e8cd11345cfcc614e3';
$output = array(); 
$message =  "Brigham requested your pass.";
echo shell_exec(PHP_BINDIR . "/php pushNotification.php " . $deviceToken . " '" . $message . "'");

?>