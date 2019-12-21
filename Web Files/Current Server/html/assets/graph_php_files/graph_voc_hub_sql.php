<?php
//Unused. functionality migrated to graph_hub_sql.php
include '../.includes/json_dbh.php';


session_start();

if ( isset( $_SESSION[ 'id' ] ) ) {
	
//setting header to json
header('Content-Type: application/json');


//database
define('DB_HOST', $host);
define('DB_USERNAME', $user);
define('DB_PASSWORD', $password);
define('DB_NAME', $database);

//get connection
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$mysqli){
	die("Connection failed: " . $mysqli->error);
}

//query to get data from the table
$date = date('Y-m-d H:i:s',time()-(1*86400)); // 1 day ago
//$date = "2019-3-15";
$query = "SELECT * FROM Test01 WHERE datetime > '$date' AND VOC != '1' AND VOC >= '10' AND VOC <= '1000' ORDER BY datetime ASC";

//execute query
$result = $mysqli->query($query);

//loop through the returned data
$data = array();
foreach ($result as $row) {
	$data[] = $row;
}

//free memory associated with result
$result->close();

//close connection
$mysqli->close();

//now print the data
print json_encode($data);
}