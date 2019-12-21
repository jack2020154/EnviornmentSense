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


<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Select Display</title>
<link href="bdststyle.css" type="text/css" rel="stylesheet" />
</head>
<body style="background-color:#729CCA;">
<br>
<h1 style="margin-left:20px; margin-bottom:30px;">Select Parameters</h1>
<a style = "margin: 10px 20px; "class="btn btn-primary" href="bdst_display.php">All Data</a>
<div class = "select-form">
	<form method="GET">
		<div class = "Entry-Form">
			<h3>Room:</h3>
<!--	    <input type="text" name="roomid" class="form-control" required> -->
			<select name="roomid">
				<option value="">Select Room</option>
				<option value="SMS">SMS</option>
				<option value="H529">H529</option>
				<option value="H300">H300</option>
				<option value="H200">H200</option>
		    </select>
			<h3>Date Start:</h3>
			<input type="date" name="datestart" class="form-control" >
			<h3>Date End:</h3>
			<input type="date" name="dateend" class="form-control" >
			<br>
			<button class="btn btn-secondary" type="submit">submit</button>
		</div>

	</form>
</div>


	<?php


	if (isset($_GET['roomid'])) {
	$roomid = mysqli_real_escape_string($connection, $_GET['roomid']);	
	$datestart = mysqli_real_escape_string($connection, $_GET['datestart']);
	$dateend = mysqli_real_escape_string($connection, $_GET['dateend']);

		if ($_GET['datestart'] == "") {
			$datestart = '1010-08-10 10:41:45';
		}
		if ($_GET['dateend'] == "") {
			$dateend = '3020-08-10 10:41:45';
		}

	$roomsql = "SELECT * FROM Test01 WHERE Location = '$roomid' and Time >= '$datestart' and Time <= '$dateend'";
		//echo $roomsql;
		//SELECT * FROM Test01 WHERE Location = 'SMS'
		//SELECT * FROM Test01 WHERE Location = 'SMS' and Time >= '2010-08-10 10:41:45' and Time <= '2020-08-10 10:41:45'
			$roomresult = mysqli_query($connection, $roomsql);
			$roomcount = mysqli_num_rows($roomresult);
	}?>





	
	
	
	
	<?php

if ($roomcount >= 1) {		?>
		<div class="select-move">
		<table  class="table table-light select-table table-hover">
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
		
		  while ($row = mysqli_fetch_assoc($roomresult)) {

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
			 //  echo "0 result";
			 }

?>


</body>
</html>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>




