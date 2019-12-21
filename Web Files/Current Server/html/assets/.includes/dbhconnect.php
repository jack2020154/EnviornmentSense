<?php
$connection = mysqli_connect("localhost", 'root', 'Mon2Sun6*');
if (!$connection) {
	die("Database Connection Failed". mysqli_error($connection));
}
$select_db = mysqli_select_db($connection, 'environmentCISS');
if (!$select_db) {
	die("Database Selection Failed". mysqli_error($connection));
}

//simple password to prevent outsiders from uploading
$sensor_upload_password = "tetrahedron";

//Function for redirection. Using Javascript due to Headers being a pain.
function Redirect($url){
  echo "<script>window.location.href = '$url';</script>";
}
?>

