<?php 
header('Content-type: application/json');
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
$column =  ' *, x( location ) AS my_point_x, y( location ) AS my_point_y';  
$from = ' FROM ';  
$tables = ' Users ';
$where = ' WHERE firstName LIKE "%' . $queryTerms .'%"';
$limit = ' LIMIT 50 ';
$query = $select . $column . $from . $tables . $where . $limit; 
//Execute the query
$query_result = mysql_query($query);

$points = array();

//echo "Results for ".$queryTerms.": <br/><br/>";
//Display the results from the query
while($user = mysql_fetch_array(($query_result))){
	
	array_push($points, array('name' =>$user['firstName'], 'lat' =>$user['my_point_x'], 'lng' =>$user['my_point_y'])); 
	//write the title of the note
	//echo $user['firstName'] . " " . $user['my_point_x'] . " " . $user['my_point_y'];
	//echo "<br />";
}

echo json_encode(array("Locations"=>$points)); 
mysql_close($connection);
 
?>