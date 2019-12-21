<?php include '../../templates/hubheader.php';

$esp_id = mysqli_real_escape_string($connection, $_GET['esp_id']);


?>


<!-- Content -->
<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">

		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					
					<?php
					echo "<h1>Sensor History for: ESP #$esp_id </h1>"
					?>
					<br>
					<a class = "btn btn-primary" href="sensor_status">Back</a>
				</div>
			</div>

		</div>
		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<table class="table table-light project-table table-hover">
						<tr style="background-color:ghostwhite;">
							<th>Location</th>
							<th>Time</th>
							<th>Temperature</th>
							<th>Humidity</th>
							<th>CO2</th>
							<th>PM25</th>
							<th>PM10</th>
							<th>PM100</th>
							<th>Lux</th>
							<th>VOC</th>
						</tr>
						<?php


						$sql = "SELECT * FROM Test01 WHERE esp_id = '$esp_id'";

						$result = mysqli_query( $connection, $sql );


						$resultCheck = mysqli_num_rows( $result );
						if ( $resultCheck > 0 ) {
							while ( $row = mysqli_fetch_assoc( $result ) ) {


								echo "<tr>
    <td>" . $row[ "Location" ] . "</td>
	
    <td>" . $row[ "datetime" ] . "</td>
	
    <td>" . $row[ 'Temperature' ] . "</td>
	
    <td>" . $row[ "Humidity" ] . "</td>
   
	<td>" . $row[ "CO2" ] . "</td>
	
	<td>" . $row[ "PM25" ] . "</td>
	
	<td>" . $row[ "PM10" ] . "</td>
	
	<td>" . $row[ "PM100" ] . "</td>
		
	<td>" . $row[ "Lux" ] . "</td>
	
	<td>" . $row[ "VOC" ] . "</td>

    </tr>";

							}

							echo "</table>";
						} else {
							echo "0 results";
						}


						?>


				</div>
			</div>
		</div>



	</div>
</div>



<?php include '../../templates/footer.php';?>
