<?php
/* set session variables and connect to the database*/
require_once('database_connect.php');
$recipient = $_GET['recipient'];

/* get the chat history involving the particular users alone*/
if(isset($_SESSION['user'])){
    $query='SELECT * FROM chat_history WHERE (User_From = "'.$_SESSION['user']['netId'].'" AND User_To = "'.$recipient.'") 
    	OR (User_To = "'.$_SESSION['user']['netId'].'" AND User_From = "'.$recipient.'");';
    $results=mysql_query($query);

    if (!$results) {
        die("Could not query the database. " . mysql_error());;
    }

    //print the appropriate first name//
    while($row=mysql_fetch_array($results)){
    	if (strcmp($row['User_From'], $recipient) !== 0)
    		$name = $_SESSION['user']['firstName'];
    	else {
    		$result = mysql_query('SELECT firstName FROM Users WHERE netId = "'.$recipient.'";'); 
    		if (!$result)
    			$name = "";
    		else
    			$name = mysql_result($result, 0);
    	}
    		
        echo "<div class='msgln'>(".$row['Time'].") <b>".$name."</b>: ".stripslashes(htmlspecialchars($row['Conversation']))."<br></div>";
    }
}
?>
