<?php
include ('../.includes/json_dbh.php');
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
/*
$query = sprintf("SELECT datetime, CO2, Location FROM Test01 WHERE Location = 'control_13'
	OR Location = 'control_14' OR Location = 'control_32' ORDER BY datetime");
  */
$query = $_SESSION[ 'generate_graphs' ];

//$query = sprintf("SELECT datetime, CO2, location FROM Test01 ORDER BY datetime");


//Removing the memory limit so that the query can load larger results
ini_set('memory_limit', '-1');
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