<?
/* set session variables and connect to the database*/
require_once('database_connect.php');

if(isset($_SESSION['user'])){
    $text = $_POST['text'];

    $date = date("Y-m-d H:i:s");    
    $from = $_SESSION['user']['netId'];
    $to = $_SESSION['user']['netId']; 

    /* save the conversation in the chat_history table*/
    $insertq = "INSERT INTO chat_history (User_From, User_To, Time, Conversation) VALUES ('".$from."', '".$to."', '".$date."', '".$text."');";
	$result = mysql_query($insertq);
	if (!$results) {
		echo mysql_errno($connection) . ": " . mysql_error($connection) . "\n";
	}
}
?>
