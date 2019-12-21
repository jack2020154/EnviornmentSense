<?php include '../../templates/hubheader.php';?>


<!-- Content -->
<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">

		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h1>All Sensor Data</h1>
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
							<th>ESP ID</th>
							<th>Heap</th>
						</tr>
						<?php


						$sql = "SELECT * FROM Test01 ORDER BY datetime DESC LIMIT 5000";
						//Removing the memory limit so that the query can load larger results
						ini_set('memory_limit', '-1');
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
	
	<td>" . $row[ "esp_id" ] . "</td>
	
	<td>" . $row[ "heap" ] . "</td>

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
