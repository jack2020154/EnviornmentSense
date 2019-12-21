<?php
$connection = mysqli_connect("localhost", 'root', 'sql2019');
if (!$connection) {
	die("Database Connection Failed". mysqli_error($connection));
}
$select_db = mysqli_select_db($connection, 'Environmental01');
if (!$select_db) {
	die("Database Selection Failed". mysqli_error($connection));
}

?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>



<title> BDST Display </title>

</head>


<link href="bdststyle.css" type="text/css" rel="stylesheet" />

<h1 style = "margin: 10px 10px; ">All Data</h1>

<a style = "margin: 10px 10px; "class="btn btn-primary" href="selectdisplay.php">Select Specific</a>




<div class="row">
	<table  class="table table-light project-table table-hover">
		<tr style="background-color:#01C5FF;">
		<th>Location</th>
		<th>Temperature</th>
		<th>Humidity</th>
		<th>CO2</th>
		<th>PM25</th>
		<th>PM10</th>
    	<th>Light</th>
    	<th>Time</th>
  </tr>
<?php


$sql = "SELECT * from Test01 ORDER BY Time DESC";
$result = mysqli_query($connection, $sql);


$resultCheck = mysqli_num_rows($result);
if ($resultCheck > 0) {
  while ($row = mysqli_fetch_assoc($result)) {

    echo "<tr>
    <td>". $row["Location"]. "</td>
    <td>". $row["Temperature"]. "</td>
    <td>". $row['Humidity'] . "</td>
    <td>". $row["CO2"]. "</td>
    <td>". $row["PM25"]. "</td>
    <td>". $row["PM10"]. "</td>
    <td>". $row["Light"]. "</td>
    <td>". $row["Time"]. "</td>
    </tr>";

       }

       echo "</table>";
     }
     else {
       echo "0 result";
     }
?>

