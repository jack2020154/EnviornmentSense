<?php header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=file.csv");
session_start();
include "../../assets/.includes/dbhconnect.php";

	
function outputCSV($data) {
  $output = fopen("php://output", "wb");
  foreach ($data as $row)
    fputcsv($output, $row); // here you can change delimiter/enclosure
  fclose($output);
}

function flip_row_col_array($array) {
    $out = array();
    foreach ($array as  $rowkey => $row) {
        foreach($row as $colkey => $col){
            $out[$colkey][$rowkey]=$col;
        }
    }
    return $out;
}

if ( !isset( $_SESSION[ 'csv_room_list' ] ) ) {
	echo '<script>window.location.href = "hub.php";</script>';
}

if ( !isset( $_SESSION[ 'id' ] ) ) {
	echo '<script>window.location.href = "login.php";</script>';
}



$start_month = mysqli_real_escape_string($connection, $_GET['start_month']);
$start_day = mysqli_real_escape_string($connection, $_GET['start_day']);
$start_year = mysqli_real_escape_string($connection, $_GET['start_year']);
				
				
				//real escape strings to sanitize the data
$end_month = mysqli_real_escape_string($connection, $_GET['end_month']);
$end_day = mysqli_real_escape_string($connection, $_GET['end_day']);
$end_year = mysqli_real_escape_string($connection, $_GET['end_year']);

//creating the dates based on the dropdowns
$start_date = $start_year . "-" . $start_month . "-" . $start_day;
$end_date = $end_year . "-" . $end_month . "-" . $end_day;
				
				
$sql_room_list = "";
$location_array = array();
$location_array = $_SESSION[ 'csv_room_list' ];


foreach($location_array as $room){
					$sql_room_list .= $room . "' OR Location = '";
	 		} 

$sql = "SELECT * FROM Test01 WHERE datetime BETWEEN '$start_date' AND '$end_date' AND Location = '" . $sql_room_list. " 0' ORDER BY datetime DESC; " ;


$array_info = array(
    "Location" => array(),
	"datetime" => array(),
	"Temperature" => array(),
	"Humidity" => array(),
    "CO2" => array(),
	"PM25" => array(),
	"PM10" => array(),
	"PM100" => array(),
    "Lux" => array(),
    "VOC" => array(),
	"esp_id" => array(),
	"testMode" => array(),
);


$array_info['Location'][] = "Location";
$array_info['datetime'][] = "datetime";
$array_info['Temperature'][] = "Temperature";
$array_info['Humidity'][] = "Humidity";
$array_info['CO2'][] = "CO2";
$array_info['PM25'][] = "PM25";
$array_info['PM10'][] = "PM10";
$array_info['PM100'][] = "PM100";
$array_info['Lux'][] = "Lux";
$array_info['VOC'][] = "VOC";
$array_info['esp_id'][] = "Esp_id";
$array_info['testMode'][] = "testMode";



$result = mysqli_query( $connection, $sql );

//Setting no memory limit so larger queries can be selected
ini_set('memory_limit', '-1');
$resultCheck = mysqli_num_rows( $result );
		if ( $resultCheck > 0 ) {
				while ( $row = mysqli_fetch_assoc( $result ) ) {
				    
				    
					$array_info['Location'][] = $row['Location'];
					$array_info['datetime'][] = $row['datetime'];
					$array_info['Temperature'][] = $row['Temperature'];
					$array_info['Humidity'][] = $row['Humidity'];
					$array_info['CO2'][] = $row['CO2'];
					$array_info['PM25'][] = $row['PM25'];
					$array_info['PM10'][] = $row['PM10'];
					$array_info['PM100'][] = $row['PM100'];
					$array_info['Lux'][] = $row['Lux'];
					$array_info['VOC'][] = $row['VOC'];
					$array_info['esp_id'][] = $row['esp_id'];
					$array_info['testMode'][] = $row['testMode'];
					
					

			}
		}




$new_array = flip_row_col_array($array_info);


outputCSV($new_array);


?>