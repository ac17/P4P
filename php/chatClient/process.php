<?
/* selectively display one user's conversations*/
if(isset($_SESSION['user'])){
    $from = $_SESSION['user']['netId'];
    $to = $_SESSION['user']['netId']; 

    $action=$_POST["action"];
    if($action=="loadLog"){
        $query="SELECT * FROM chat_history WHERE User_From = $from OR User_To = $to";
        $results=mysql_query($query);

        if (!$results) {
        echo mysql_errno($connection) . ": " . mysql_error($connection) . "\n";
        }

        while($row=mysql_fetch_array($results)){
            echo "<div class='msgln'>(".$row['Time'].") <b>".$_SESSION['user']['firstName']."</b>: ".stripslashes(htmlspecialchars($row['Conversation']))."<br></div>";
        }
    }
}
?>
