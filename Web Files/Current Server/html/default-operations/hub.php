<?php
include '../templates/hubheader.php';
?>

<!-- Content -->
<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">
		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h1>Home Page</h1>
				</div>
			</div>
		</div>
		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">


					<h3>Welcome to the Environmental Sensor Website Page</h3>
					<br>
					<h5>View the functions to the right to generate tables, download CSVs, view certain rooms, or view school layout.</h5>
					<br>
					<button class = "btn btn-secondary" id = 'all-btn'>All Data</button>
					<button class = "btn btn-warning" id = 'all-school-btn'>Whole School</button>
					<button class = "btn btn-primary" id = 'Highschool-btn'>Highschool</button>
					<button class = "btn btn-warning" id = 'PC-btn'>Phoenix Center</button>
					<button class = "btn btn-primary" id = 'Rittman-btn'>Rittman</button>
					<button class = "btn btn-warning" id = 'Intermediate-btn'>Intermediate</button>
					<button class = "btn btn-primary" id = 'Elementary-btn'>Elementary</button>
					<br>
					<br>
					<button class = "btn btn-danger" id = 'suspicious-btn'></button>
					<br>
					<br>
					<button class = "btn btn-success" id = 'live-btn'></button>
					<br>
					<br>
					<label for=""> Resolution (default: 10%): </label>
					<button class = "btn btn-primary" id = 'resolution_10'>10</button>
					<button class = "btn btn-primary" id = 'resolution_5'>20</button>
					<button class = "btn btn-primary" id = 'resolution_3'>33</button>
					<button class = "btn btn-primary" id = 'resolution_1'>100</button>
					<br>
					<br>
					<button class = "btn btn-dark" id = 'past_1_day'>Past Day</button>
					<button class = "btn btn-dark" id = 'past_3_day'>Past 3 Days</button>
					<button class = "btn btn-dark" id = 'past_5_day'>Past 5 Days</button>


					
					
					
				</div>
			</div>
		</div>

		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h3>CO2 Graph (Parts Per Million)</h3>
					<div id="myDiv-CO2"></div>
				</div>
			</div>
		</div>

		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h3>PM25 Graph (Parts Per Million)</h3>
					<div id="myDiv-PM25"></div>
				</div>
			</div>
		</div>
				<div class="col-lg-12 col-md-6">
					<div class="card">
						<div class="card-body">
							<h3>Temperature Graph (Degrees Celsius)</h3>
							<div id="myDiv-Temperature"></div>
						</div>
					</div>
				</div>

				<div class="col-lg-12 col-md-6">
					<div class="card">
						<div class="card-body">
							<h3>Humidity Graph (in %)</h3>
							<div id="myDiv-Humidity"></div>
						</div>
					</div>
				</div>

				<div class="col-lg-12 col-md-6">
					<div class="card">
						<div class="card-body">
							<h3>VOC (Parts Per Billion)</h3>
							<div id="myDiv-VOC"></div>
						</div>
					</div>
				</div>
		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">

					<h3>Latest Activity</h3>
					<br>
					<p>Last 20 uploads</p>


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


						$sql = "SELECT Location, datetime, Temperature, Humidity, CO2, PM25, PM10, PM100, esp_id, Lux, VOC FROM Test01 ORDER BY datetime DESC LIMIT 20";

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
						?>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo $folder_nav; ?>js/hub_graphs.js"></script>


<?php include '../templates/footer.php';?>
