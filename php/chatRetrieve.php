<?
/* set session variables and connect to the database*/
require_once('database_connect.php');

/* get the chat history involving the particular user alone*/
if(isset($_SESSION['user'])){
    $query='SELECT * FROM chat_history WHERE User_From = "'.$_SESSION['user']['netId'].'" OR User_From = "'."ac17".'";';
    $results=mysql_query($query);

    if (!$results) {
        die("Could not query the database. " . mysql_error());;
    }

    while($row=mysql_fetch_array($results)){
        echo "<div class='msgln'>(".$row['Time'].") <b>".$_SESSION['user']['firstName']."</b>: ".stripslashes(htmlspecialchars($row['Conversation']))."<br></div>";
    }
}
?>
