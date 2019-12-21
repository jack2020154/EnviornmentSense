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


//moreso for the Parallel Coordinate Plots
$room_1 = $mysqli->real_escape_string($_GET['room_1']);
$room_2 = $mysqli->real_escape_string($_GET['room_2']);
$room_3 = $mysqli->real_escape_string($_GET['room_3']);
$room_4 = $mysqli->real_escape_string($_GET['room_4']);
$room_5 = $mysqli->real_escape_string($_GET['room_5']);
$room_sql = " AND (location = '$room_1' OR location = '$room_2' 
OR location = '$room_3' OR location = '$room_4' OR location = '$room_5') ";


//$date = "2019-3-15";
if(isset($_GET['chooseDate'])){
	$date = $mysqli->real_escape_string($_GET['chooseDate']);
}


//query to get data from the table
//$date = date('Y-m-d H:i:s',time()-(1*86400)); // 1 day ago

$date_start = $date . " " . "7:30:00";
$date_end = $date . " " ."17:30:00";
$time_string_selector = "";

if(isset($_GET['schoolTime'])){
	$time_string_selector = " datetime > '$date_start' AND datetime < '$date_end' ";
} else {
	$time_string_selector = " datetime > '$date' ";
}


if(isset($_GET['suspiciousFilter'])){
	$query = "SELECT * FROM Test01 WHERE " . $time_string_selector . " AND validity_check != 'suspicious'" . $room_sql;

} else {
	$query = "SELECT * FROM Test01 WHERE" . $time_string_selector . $room_sql;

}


//execute query
//Removing the memory limit so that the query can load larger results
ini_set('memory_limit', '-1');
$result = $mysqli->query($query);
$row_cnt = $result->num_rows;

if($row_cnt > 0){


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
} else {
	echo "null return";
}
} else {
	echo "Invalid Login";
}