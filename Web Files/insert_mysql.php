<?php

foreach ($_REQUEST as $key => $value)
{
  if ($key == "location") {
  $location = $value;
	}
  if ($key == "comb_temp") {
  $comb_temp = $value;
	}
  if ($key == "comb_hI") {
  $comb_hI = $value;
	}
  if ($key == "comb_co2") {
  $comb_co2 = $value;
	}
  if ($key == "comb_pm100") {
  $comb_pm100 = $value;
	}
  if ($key == "comb_pm25") {
  $comb_pm25 = $value;
	}
  if ($key == "comb_pm10") {
  $comb_pm10 = $value;
	}
  if ($key == "comb_aqi") {
  $comb_aqi = $value;
  }
  if ($key == "comb_ir") {
  $comb_ir = $value;
  }
  if ($key == "comb_lux") {
  $comb_lux = $value;
  }
}
$arr = get_defined_vars();
var_dump($arr);
// EDIT: Your mysql database account information
$username = "esp8266";
$password = "82ndjbkb8!";

$database = "sensor_db";
$tablename = "sensor_table";
$localhost = "127.0.0.1";
$link = mysqli_connect($localhost, $username, $password, $database);

// Check Connection to Database
if (mysqli_connect($localhost, $username, $password, $database))
  {
  	@mysqli_select_db( $link , $database) or die ("Unable to select database");

    // Next two lines will write into your table 'test_table_name_here' with 'yourdata' value from the arduino and will timestamp that data using 'now()'
    if ($comb_temp != 0 && $comb_hI != 0 && $comb_co2 != 0 && $comb_pm100 != 0 && $comb_pm25 != 0 && $comb_pm10 != 0 && $comb_aqi != 0 && $comb_ir != 0 && $comb_lux != 0) {
    $query = "INSERT INTO $tablename VALUES ($location, $comb_temp, $comb_hI, $comb_co2, $comb_pm100, $comb_pm25, $comb_pm10, $comb_aqi, $comb_ir, $comb_lux, now())";
  	$result = mysqli_query($link, $query);
    $comb_aqi = 0;
    $comb_co2 = 0;
    $comb_hI = 0;
    $comb_ir = 0;
    $comb_lux = 0;
    $comb_pm10 = 0;
    $comb_pm100 = 0;
    $comb_pm25 = 0;
    $comb_temp = 0;}
  } else {
  	echo('Unable to connect to database.');
  }

?>