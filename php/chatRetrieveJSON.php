<?php
/* set session variables and connect to the database*/
require_once('database_connect.php');
$recipient = $_GET['recipient'];
$user = $_GET['user'];

/* get the chat history involving the particular users alone*/
$query='SELECT * FROM chat_history WHERE (User_From = "'.$user.'" AND User_To = "'.$recipient.'") 
OR (User_To = "'.$user.'" AND User_From = "'.$recipient.'");';
$results=mysql_query($query);

if (!$results) {
    die("Could not query the database. " . mysql_error());;
}

    //print the appropriate first name//
echo "[";
$counter = 0;
while($row=mysql_fetch_array($results)){
    if (strcmp($row['User_From'], $recipient) !== 0) {
        $result = mysql_query('SELECT firstName FROM Users WHERE netId = "'.$user.'";'); 
        if (!$result)
            $name = "";
        else
            $name = mysql_result($result, 0);
    }
    else {
        $result = mysql_query('SELECT firstName FROM Users WHERE netId = "'.$recipient.'";'); 
        if (!$result)
            $name = "";
        else
            $name = mysql_result($result, 0);
    }
 if ($counter === 0) {
    echo "{time: ".$row['Time'].", from: ".$name.", conversation: ". $row['Conversation']."}";
} else {
    echo ", {time: ".$row['Time'].", from: ".$name.", conversation: ". $row['Conversation']."}";   
}
$counter = $counter + 1;
}
echo "]";

?>
