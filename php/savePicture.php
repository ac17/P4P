<?php 
//Get login information 
require('../php/database_connect.php');
require('exchange_api/exchangeAPI.php');

if (isset($_FILES['image'])) {
    $aExtraInfo = getimagesize($_FILES['image']['tmp_name']);
    $sImage = "data:" . $aExtraInfo["mime"] . ";base64," . base64_encode(file_get_contents($_FILES['image']['tmp_name']));
    echo '<p>The image has been uploaded successfully</p><p>Preview:</p><img src="' . $sImage . '" alt="Your Image" />;
}

mysql_close($connection);

?>