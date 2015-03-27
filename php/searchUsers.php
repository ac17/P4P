<?php 
//Get login information 
require('./database_connect.php');
//Get the id of the note to be displayed returned
$queryTerms = $_GET["query"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$queryTerms = mysql_real_escape_string($queryTerms);
	$queryTerms = stripslashes($queryTerms);
}

//Build a query
$select = ' SELECT '; 
$column =  ' * ';  
$from = ' FROM ';  
$tables = ' Users ';
$where = ' WHERE firstName LIKE "%' . $queryTerms .'%"';
$limit = ' LIMIT 50 ';
$query = $select . $column . $from . $tables . $where . $limit; 
//Execute the query
$query_result = mysql_query($query);

echo "Results for ".$queryTerms.": <br/><br/>";
//Display the results from the query
while($user = mysql_fetch_array(($query_result))){
	
	
	//write the title of the note
	echo $user['firstName'];

	echo "<br />";
}

//Close connection
mysql_close($connection);
?>