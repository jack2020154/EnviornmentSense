<?php include '../../templates/hubheader.php';

$location = mysqli_real_escape_string( $connection, $_GET[ 'Location' ] );

?>


<!-- Content -->
<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">

		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					
					<?php echo "<h1>Sensor Data For Room: $location</h1>"?>
					<br>
					<label for="">Didn't find what you want?</label>
					<a class="btn btn-warning" href="search_room.php">Search Again</a>
					
					
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
						</tr>
						<?php


						$sql = "SELECT * FROM Test01 WHERE Location = '$location'";
						
						

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

    </tr>";

							}

							echo "</table>";
						} else {
							echo "0 results";
						}
						
						echo $sql;


						?>


				</div>
			</div>
		</div>



	</div>
</div>


<?php include '../../templates/footer.php';?>
