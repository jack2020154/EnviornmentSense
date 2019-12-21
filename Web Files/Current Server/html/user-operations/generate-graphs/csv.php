<?php
include ".includes/dbhconnect.php";

$yes = "not true";

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=file.csv");

	
	
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


/*
outputCSV(array(
  array("name 1", "age 1", "city 1"),
  array("name 2", "age 2", "city 2"),
  array("name 3", "age 3", "city 3")
));
*/

/*
function pre_r($array){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}


*/
$sql = "SELECT * FROM Test01";


$array_info = array(
    "Location" => array(),
    "datetime" => array(),
    "CO2" => array(),
    "esp_id" => array(),
);


$array_info['Location'][] = "Location";
$array_info['datetime'][] = "Datetime";
$array_info['CO2'][] = "CO2";
$array_info['esp_id'][] = "ESP_ID";

$result = mysqli_query($connection, $sql);
while ( $row = mysqli_fetch_assoc( $result ) ) {
	
	//echo $row["Location"];
	$array_info['Location'][] = $row['Location'];
	$array_info['datetime'][] = $row['datetime'];
	$array_info['CO2'][] = $row['CO2'];
	$array_info['esp_id'][] = $row['esp_id'];
	

}

//pre_r($array_info);




$new_array = flip_row_col_array($array_info);


outputCSV($new_array);

	
	
?>











