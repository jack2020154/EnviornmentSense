<?php
/**
 * 
 * Using HTTP Get to retrieve the timestamp...
*
 */

$timeVal = $_GET[ 'timeVal' ];
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


//INSERT INTO `Test01` (`Location`, `datetime`, `Temperature`, `Humidity`, `CO2`, `PM25`, `PM10`, `PM100`, `Lux`, `VOC`, `esp_id`, `testMode`) VALUES ('P106', '2019-02-07 03:00:00', '22', '50', '900', '20', '5', '5', '50', '10', '2', 'none'), ('PC_commons', '2019-02-07 03:00:01', '23', '50', '1300', '25', '1', '1', '30', '10', '2', 'none');

//testing the new function set
//INSERT INTO `Test01` (`Location`, `datetime`, `Temperature`, `Humidity`, `CO2`, `PM25`, `PM10`, `PM100`, `Lux`, `VOC`, `esp_id`, `testMode`) VALUES ('P106', '2019-02-07 01:00:00', '22', '50', '900', '20', '5', '5', '50', '10', '2', 'none'), ('PC_commons', '2019-02-07 01:00:02', '23', '50', '900', '25', '1', '1', '30', '10', '2', 'none'), ('P106', '2019-02-07 05:00:00', '22', '50', '1500', '20', '5', '5', '50', '10', '2', 'none'), ('PC_commons', '2019-02-07 05:00:02', '23', '50', '1300', '25', '1', '1', '30', '10', '2', 'none'), ('P106', '2019-02-07 12:00:00', '22', '50', '2000', '20', '5', '5', '50', '10', '2', 'none'), ('PC_commons', '2019-02-07 12:00:02', '23', '50', '1900', '25', '1', '1', '30', '10', '2', 'none');

date_default_timezone_set('Asia/Hong_Kong'); 

//$date1 = date("Y-m-d"); 

if(isset($_SESSION['dateToHeatmap'])){
$date1 = $_SESSION['dateToHeatmap'];
} else {
$date1 = date("Y-m-d"); 
}
//can be used to designate other things




if ($timeVal == '1am'){
	$start = $date1 . " 1:00:00";
	$end = $date1 . " 2:00:00";
} else if ($timeVal == '2am'){
	$start = $date1 . " 2:00:00";
	$end = $date1 . " 3:00:00";
} else if ($timeVal == '3am'){
	$start = $date1 . " 3:00:00";
	$end = $date1 . " 4:00:00";
} else if ($timeVal == '4am'){
	$start = $date1 . " 4:00:00";
	$end = $date1 . " 5:00:00";
} else if ($timeVal == '5am'){
	$start = $date1 . " 5:00:00";
	$end = $date1 . " 6:00:00";
} else if ($timeVal == '6am'){
	$start = $date1 . " 6:00:00";
	$end = $date1 . " 7:00:00";
} else if ($timeVal == '7am'){
	$start = $date1 . " 7:00:00";
	$end = $date1 . " 8:00:00";
} else if ($timeVal == '8am'){
	$start = $date1 . " 8:00:00";
	$end = $date1 . " 9:00:00";
} else if ($timeVal == '9am'){
	$start = $date1 . " 9:00:00";
	$end = $date1 . " 10:00:00";
} else if ($timeVal == '10am'){
	$start = $date1 . " 10:00:00";
	$end = $date1 . " 11:00:00";
} else if ($timeVal == '11am'){
	$start = $date1 . " 11:00:00";
	$end = $date1 . " 12:00:00";
} else if ($timeVal == '12pm'){
	$start = $date1 . " 12:00:00";
	$end = $date1 . " 13:00:00";
} else if ($timeVal == '1pm'){
	$start = $date1 . " 13:00:00";
	$end = $date1 . " 14:00:00";
} else if ($timeVal == '2pm'){
	$start = $date1 . " 14:00:00";
	$end = $date1 . " 15:00:00";
} else if ($timeVal == '3pm'){
	$start = $date1 . " 15:00:00";
	$end = $date1 . " 16:00:00";
} else if ($timeVal == '4pm'){
	$start = $date1 . " 16:00:00";
	$end = $date1 . " 17:00:00";
} else if ($timeVal == '5pm'){
	$start = $date1 . " 17:00:00";
	$end = $date1 . " 18:00:00";
} else if ($timeVal == '6pm'){
	$start = $date1 . " 18:00:00";
	$end = $date1 . " 19:00:00";
} else if ($timeVal == '7pm'){
	$start = $date1 . " 19:00:00";
	$end = $date1 . " 20:00:00";
} else if ($timeVal == '8pm'){
	$start = $date1 . " 20:00:00";
	$end = $date1 . " 21:00:00";
} else if ($timeVal == '9pm'){
	$start = $date1 . " 21:00:00";
	$end = $date1 . " 22:00:00";
} else if ($timeVal == '10pm'){
	$start = $date1 . " 22:00:00";
	$end = $date1 . " 23:00:00";
} else if ($timeVal == '11pm'){
	$start = $date1 . " 23:00:00";
	$end = $date1 . " 24:00:00";
} else if ($timeVal == '12am'){
	$start = $date1 . " 00:00:00";
	$end = $date1 . " 1:00:00";
} else {
	echo "something went wrong...";
}


$query = sprintf("SELECT * FROM Test01 WHERE datetime BETWEEN '$start' AND '$end' ORDER BY datetime");
 

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