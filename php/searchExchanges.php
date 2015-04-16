<?php 
header('Content-type: application/json');
//Get login information 
require('./database_connect.php');
//Get the id of the note to be displayed returned
$date = $_GET["date"];
$type = $_GET["type"];
$numPasses = $_GET["numPasses"];
$passType = $_GET["club"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$date = stripslashes(mysql_real_escape_string($date));
}

//Build a query
$select = ' SELECT '; 
$column =  ' *, x( Users.location ) AS my_point_x, y( Users.location ) AS my_point_y';  
$from = ' FROM ';  
$tables = ' Active_exchanges LEFT JOIN Users ON Active_exchanges.requesterNetId=Users.netId ';
$where = ' WHERE Active_exchanges.passDate="' . date ("Y-m-d", strtotime($date)) . '" AND Active_exchanges.passType LIKE "%' .$passType. '%" AND Active_exchanges.passNum LIKE "%'.$numPasses.'%" AND Active_exchanges.type LIKE "%'.$type.'%"';
$query = $select . $column . $from . $tables . $where; 
//Execute the query
$query_result = mysql_query($query);
//Provide an error message if the query failed
if(!$query_result){
	die("Could not query the database. " . mysql_error());
}


$exchanges = array();

//echo "Results for ".$queryTerms.": <br/><br/>";
//Display the results from the query
if ($query_result !== false)
{
	while($exchange = mysql_fetch_array(($query_result))){	
		array_push($exchanges, array('id' =>$exchange['id'], 
									 'club' =>$exchange['passType'],
									 'passNum' =>$exchange['passNum'],
									 'passDate' =>$exchange['passDate'],
									 'comments' =>$exchange['comments'],
									 'name' =>$exchange['firstName'],
									 'lat' =>$exchange['my_point_x'], 
									 'lng' =>$exchange['my_point_y'],
									 'type' =>$exchange['type'])); 
	}
}
echo json_encode(array("Exchanges"=>$exchanges)); 
mysql_close($connection);
 
?>