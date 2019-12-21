<?php include '../../templates/hubheader.php';

if(!isset($_SESSION[ 'is_admin' ])){
    Redirect('../../default-operations/hub.php?error=Something Went Wrong');
}

if(isset($_GET['esp_id'])){

$esp_id = $_GET['esp_id'];

$sql = "UPDATE `bdst_esp_info` SET rebootStatus = 1 WHERE esp_id = '$esp_id'";

	$result = mysqli_query( $connection, $sql );

	if ($result) {
		Redirect('reboot_sensors.php?success=Success! Sensor will reboot shortly.');
	} else {
		Redirect('reboot_sensors.php?error=Something Went Wrong');
	}

}
?>