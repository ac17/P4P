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
    	if (strcmp($row['User_From'], $recipient) !== 0){
    		$name = 'You';
		$class = 'ownmsg';
		$photolink = $_SESSION['user']['photo'];
	}
    	else {
		$class = "othermsg";
    		$result = mysql_query('SELECT firstName FROM Users WHERE netId = "'.$recipient.'";'); 
    		if (!$result)
    			$name = "";
    		else
    			$name = mysql_result($result, 0);

		$result = mysql_query('SELECT photo FROM Users WHERE netId = "'.$recipient.'";'); 
    		if (!$result)
    			$photolink = "";
    		else
    			$photolink = mysql_result($result, 0);
    	}
    		
	if($photolink === "" || $photolink === null)
	{
		$photo = '<img class="miniProfilePic" src="img/default.jpg"></img>';
	}
	else
	{
		$photo = '<img class="miniProfilePic" src="img/'.$photolink.'"></img>';
	}

        echo "<div class='msgln'><span title = '".$row['Time']."'>".$photo."<b>".$name."</b>: ".stripslashes(htmlspecialchars($row['Conversation']))."</span><br></div>";
    }
}
?>
