<?php
//	$Location = $_GET['location'];
include '../assets/.includes/dbhconnect.php';
//http://10.0.0.25/sensor_upload_data.php?location=test&temperature=22&humidity=55&co2=55&pm25=55&pm100=

//http://localhost/bdst_april/hardware/sensor_upload_data.php?location=test&temperature=22&humidity=55&co2=55&heap=3000&pm25=55&pm100=55&pm10=55&light=55&VOC=55&esp_id=22&password=tetrahedron
//http://environment.concordiashanghai.org/hardware/sensor_upload_data.php?location=test&temperature=22&humidity=55&co2=300&heap=3000&pm25=20&pm100=0&pm10=1&light=55&VOC=55&esp_id=22&password=tetrahedron
foreach($_REQUEST as $key => $value)
{
	if($key == 'location') {
		$Location = $value;
	}

	if($key == 'temperature') {
		$temperature = $value;
	}

	if($key == 'humidity') {
		$humidity = $value;
	}

	if($key == 'co2') {
		$co2 = $value;
	}

	if($key == 'pm25') {
		$pm25 = $value;
	}

	if($key == 'pm10') {
		$pm10 = $value;
	}

	if($key == 'pm100') {
		$pm100 = $value;
	}

	if($key == 'light') {
		$light = $value;
	}

	if($key == 'VOC') {
		$VOC = $value;
	}

	if($key == 'esp_id') {
		$esp_id = $value;
	}

	if($key == 'testmode') {
		$testMode = $value;
	}

	if($key == 'password') {
		$password = $value;
	}
	
	if($key == 'heap') {
		$heap = $value;
	}



}


//escaping the httpGET to prevent any SQL Injections

	$escaped_location = mysqli_real_escape_string($connection, $Location);

	$escaped_temperature = mysqli_real_escape_string($connection, $temperature);

	$escaped_humidity = mysqli_real_escape_string($connection, $humidity);

	$escaped_co2 = mysqli_real_escape_string($connection, $co2);

	$escaped_pm25 = mysqli_real_escape_string($connection, $pm25);

	$escaped_pm10 = mysqli_real_escape_string($connection, $pm10);

	$escaped_pm100 = mysqli_real_escape_string($connection, $pm100);

	$escaped_light = mysqli_real_escape_string($connection, $light);

	$escaped_VOC = mysqli_real_escape_string($connection, $VOC);

	$escaped_esp_id = mysqli_real_escape_string($connection, $esp_id);

	//$escaped_testMode = mysqli_real_escape_string($connection, $testMode);

	$escaped_password = mysqli_real_escape_string($connection, $password);
	
	$escaped_heap = mysqli_real_escape_string($connection, $heap);


$delta_check_sql = "SELECT * FROM Test01 WHERE esp_id = '$escaped_esp_id'
AND validity_check != 'suspicious' ORDER BY datetime DESC LIMIT 3";

$query_co2_sum = 0;
$loopCnt = 0;

$delta_result = mysqli_query( $connection, $delta_check_sql );
$delta_resultCheck = mysqli_num_rows( $delta_result );
if ( $delta_resultCheck > 0 ) {
	while ( $row = mysqli_fetch_assoc( $delta_result ) ) {

										$query_co2_sum += $row[ "CO2" ];
										$loopCnt++;
								}
						}

$prev_co2_average = $query_co2_sum / $loopCnt;

if(abs($prev_co2_average - $escaped_co2) > 600){
	$data_validity = "suspicious";
} else {
	$data_validity = "valid";
}

if($escaped_location == "999" || $escaped_location == "" ){
    $noRoomSql = "SELECT * FROM bdst_esp_info WHERE esp_id = '$escaped_esp_id'";
    $noRoomSql_result = mysqli_query( $connection, $noRoomSql );
    while ( $row = mysqli_fetch_assoc( $noRoomSql_result ) ) {
        $escaped_location = $row[ "location" ];
    }
}

//preventing bad data from entering the database...haven't put a cap because that filter might come later
//$escaped_co2 < 400 ||
if($escaped_pm25 > 100 || $escaped_pm25 == 0){
    echo "Data is invalid";

} else {

//In total darkness or very dark condition the lux sensor will overflow. This is to prevent the value from entering the database.
if ($escaped_light > 2000){
    $escaped_light = 1;
}

//will uncomment later
if($sensor_upload_password == $escaped_password){

$sql = "INSERT INTO Test01 (Location, Temperature, Humidity, CO2, PM25, PM10, PM100, Lux, VOC, esp_id, validity_check, heap)
VALUES ('$escaped_location', '$escaped_temperature', '$escaped_humidity', '$escaped_co2', '$escaped_pm25', '$escaped_pm10', '$escaped_pm100', '$escaped_light', '$escaped_VOC', '$escaped_esp_id', '$data_validity', '$escaped_heap');";

$result = mysqli_query( $connection, $sql );

if($result) {
	echo "Success";
		
	
} else{
	echo "Failed to enter data";
}


} else {
	echo "Password Incorrect";
}

}

?>
