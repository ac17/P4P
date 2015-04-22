<?php 
header('Content-type: application/json');
//Get login information 
require('./database_connect.php');
//Get the id of the note to be displayed returned
$netId = $_GET["netId"];
//Protect against SQL injection
if(get_magic_quotes_gpc()){
	$queryTerms = stripslashes(mysql_real_escape_string($netId));
}

//Build a query
$select = ' SELECT '; 
$column =  ' * ';  
$from = ' FROM ';  
$tables = ' Active_exchanges ';
$where = 'WHERE requesterNetId="' . $netId . '"';
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
									 'club' =>$exchange['passClub'],
									 'passNum' =>$exchange['passNum'],
									 'passDate' =>$exchange['passDate'],
									 'comments' =>$exchange['comments'],
									 'associatedExchanges' =>$exchange['associatedExchanges'],
									 'type' =>$exchange['type'])); 
	}
}
echo json_encode(array("Exchanges"=>$exchanges)); 
mysql_close($connection);
 
?>