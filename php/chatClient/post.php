<?
require_once('../database_connect.php');
session_start();

if(isset($_SESSION['user'])){
    $text = $_POST['text'];
    
    $fp = fopen("log.html", 'a');
    fwrite($fp, "<div class='msgln'>(".date("g:i A").") <b>".$_SESSION['user']['firstName']."</b>: ".stripslashes(htmlspecialchars($text))."<br></div>");
    fclose($fp);

    $date = date("Y-m-d H:i:s");    
    $from = $_SESSION['user']['netId'];
    $to = $_SESSION['user']['netId']; 

    $insertq = "INSERT INTO chat_history (User_From, User_To, Time, Conversation) VALUES ('".$from."', '".$to."', '".$date."', '".$text."');";
	$result = mysql_query($insertq);
	if (!$results) {
		echo mysql_errno($connection) . ": " . mysql_error($connection) . "\n";
	}
}
?>