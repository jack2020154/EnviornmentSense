<?php
//include($_SERVER['DOCUMENT_ROOT'] . '/bdst_april/assets/.includes/json_dbh.php');
include('../.includes/json_dbh.php');

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

if(isset($_GET['load_num_days'])){
	$load_num_days = $mysqli->real_escape_string($_GET['load_num_days']);
} else {
	$load_num_days = 1;
}


if($load_num_days == 5){
	$date = date('Y-m-d h:i:s',time()-(5*86400)); // 1 day ago
	

} elseif($load_num_days == 3){
	$date = date('Y-m-d h:i:s',time()-(3*86400)); // 1 day ago
	
	
} else {
	$date = date('Y-m-d h:i:s',time()-(1*86400)); // 1 day ago

}


$date2 = date('Y-m-d h:i:s',time()-(0*86400)); // 0 day ago

//custom date set


//For the parallel coordinate Plot
if(isset($_GET['room_1'])){
	//moreso for the Parallel Coordinate Plots
	$room_1 = $mysqli->real_escape_string($_GET['room_1']);
	$room_2 = $mysqli->real_escape_string($_GET['room_2']);
	$room_3 = $mysqli->real_escape_string($_GET['room_3']);
	$room_4 = $mysqli->real_escape_string($_GET['room_4']);
	$room_5 = $mysqli->real_escape_string($_GET['room_5']);
	$room_sql = " AND (location = '$room_1' OR location = '$room_2' 
	OR location = '$room_3' OR location = '$room_4' OR location = '$room_5') ";
} else {
    $room_sql = "";
}


//Setting the resolution (loading every nth datapoint). Used for performance optimization
//If not set then the resolution will be by default 3
if(isset($_GET['resolution'])){
	$resolution = $mysqli->real_escape_string($_GET['resolution']);
} else {
	$resolution = 3;
}



$string = "";
//selecting a particular day....Used in 3dScatter Functionality
if(isset($_GET['chooseDate'])){
	$date = $mysqli->real_escape_string($_GET['chooseDate']);
	$dateb =  substr($date,0, 8); 
    $date2 =  $dateb . (($date[8]) + 1);
    $string = " AND datetime < '$date2' ";	
} 

//loading only within the school time (7:30 am to 5:30 pm)
//Used in the violinPlots to get an accurate measurement of the day readings
if(isset($_GET['schoolTime'])){
$date = date('Y-m-d',time()-(1*86400)); // 1 day ago
//----------------------
$date_start = $date . " " . "7:30:00";
$date_end = $date . " " ."17:30:00";
$time_string_selector = " datetime > '$date_start' AND datetime < '$date_end' ";
} else {
	$time_string_selector = " datetime > '$date' " . $string;
}

//Suspicious Filter determines whether or not to include the "suspicious" or potentially spurious datapoints
if(isset($_GET['suspiciousFilter'])){
	$query = "SELECT * FROM 
( SELECT @row := @row +1 AS rownum, datetime, Location, Temperature, Humidity, PM25, CO2, Lux, esp_id, validity_check 
FROM ( SELECT @row :=0) r, Test01 WHERE $time_string_selector AND validity_check != 'suspicious' $room_sql) 
ranked WHERE rownum % $resolution = 0";
} else {
	$query = "SELECT * FROM 
( SELECT @row := @row +1 AS rownum, datetime, Location, Temperature, Humidity, PM25, CO2, Lux, esp_id, validity_check 
FROM ( SELECT @row :=0) r, Test01 WHERE $time_string_selector $room_sql) 
ranked WHERE rownum % $resolution = 0";
}

if(isset($_GET['isVOC'])){
	$query = "SELECT * FROM 
	( SELECT @row := @row +1 AS rownum, datetime, VOC, Location 
	FROM ( SELECT @row :=0) r, Test01 WHERE datetime > '$date' AND VOC != '1' AND VOC >= '10' AND VOC <= '1000') 
	ranked WHERE rownum % $resolution = 0";
}

//echo $query;

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
	//echo $query;
}

}