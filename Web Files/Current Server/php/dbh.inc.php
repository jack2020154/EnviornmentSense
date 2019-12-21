<?php
// local
$servername = 'localhost';
$username = 'aqiMachine';

$password = 'LEDcube2718';

$dbname = 'outdoorPM25';

// Create connection
$conn = new mysqli($servername, $username, $password);

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
	die('Database Connection Failed'. mysqli_error($conn));
}
?>

