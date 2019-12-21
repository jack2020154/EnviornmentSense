<?php include '../../templates/hubheader.php';?>


	<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">
			<?php
			if (isset($_GET['generate_table'])){
				
				//real escape strings to sanitize the data
				$start_month = mysqli_real_escape_string($connection, $_GET['start_month']);
				$start_day = mysqli_real_escape_string($connection, $_GET['start_day']);
				$start_year = mysqli_real_escape_string($connection, $_GET['start_year']);
				$start_hour = mysqli_real_escape_string($connection, $_GET['start_hour']);
				$resolution = mysqli_real_escape_string($connection, $_GET['resolution_input']);
				//the Max mins for CO2 and PM25
				$max_co2 = mysqli_real_escape_string($connection, $_GET['max_co2']);
				$min_co2 = mysqli_real_escape_string($connection, $_GET['min_co2']);
				$max_pm25 = mysqli_real_escape_string($connection, $_GET['max_pm25']);
				$min_pm25 = mysqli_real_escape_string($connection, $_GET['min_pm25']);

				if(!$max_co2){
					$max_co2 = 2500;
				}

				if(!$min_co2){
					$min_co2 = 200;
				}

				if(!$max_pm25){
					$max_pm25 = 50;
				}

				if(!$min_pm25){
					$min_pm25 = 0;
				}

				if(!$resolution){
					$resolution = 3;
				}

				//to be appended to SQL statement
				$co2_pm25_sql = "AND PM25 < '$max_pm25'  AND PM25 > '$min_pm25' 
				AND CO2 < '$max_co2' AND CO2 > '$min_co2'";


				//real escape strings to sanitize the data
				$end_month = mysqli_real_escape_string($connection, $_GET['end_month']);
				$end_day = mysqli_real_escape_string($connection, $_GET['end_day']);
				$end_year = mysqli_real_escape_string($connection, $_GET['end_year']);
				$end_hour = mysqli_real_escape_string($connection, $_GET['end_hour']);


				//creating the dates based on the dropdowns
				$start_date = $start_year . "-" . $start_month . "-" . $start_day . " " . $start_hour . ":00";
				$end_date = $end_year . "-" . $end_month . "-" . $end_day . " " . $end_hour . ":00";


				$location_array = array();
				$location_array = $_GET['room_array'];

				?>

			<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h3>Generated Table</h3>
					<br>

			<?php
				$sql_room_list = "";
				$displaying_message = "<strong>Displaying values for: ";
				foreach($location_array as $room){
					$sql_room_list .= $room . "' AND datetime BETWEEN '$start_date' AND '$end_date' OR Location = '";
					$displaying_message .=  $room . " | ";

				}

				$displaying_message .= "<br><br> Between the dates $start_date and $end_date.</strong><br><br>";


				echo $displaying_message;

				$sql = "SELECT * FROM 
				( SELECT @row := @row +1 AS rownum, datetime, Location, Temperature, Humidity, PM25, CO2, Lux, esp_id, validity_check, PM10, PM100, VOC, testMode 
				FROM ( SELECT @row :=0) r, Test01 WHERE (datetime BETWEEN '$start_date' AND '$end_date'
				AND Location = '" . $sql_room_list. "1') $co2_pm25_sql) 
				ranked WHERE rownum % $resolution = 0";


				/*"SELECT * FROM 
				( SELECT @row := @row +1 AS rownum, datetime, Location, Temperature, Humidity, PM25, CO2, Lux, esp_id, validity_check 
				 FROM ( SELECT @row :=0) r, Test01 WHERE (datetime BETWEEN '2019-1-1 1:00:00' AND '2020-1-1 1:00:00' 
				 AND Location = 'H429' AND datetime BETWEEN '2019-1-1 1:00:00' AND '2020-1-1 1:00:00' OR Location = 'H306' AND datetime BETWEEN '2019-1-1 1:00:00' AND '2020-1-1 1:00:00' OR Location = 'H218' AND datetime BETWEEN '2019-1-1 1:00:00' AND '2020-1-1 1:00:00' OR Location = '1') AND PM25 < '50' AND PM25 > '0' AND CO2 < '2500' AND CO2 > '200') ranked WHERE rownum % 3 = 0
							   "*/

				/*
				echo $sql = "SELECT * FROM Test01 WHERE (datetime BETWEEN '$start_date' AND '$end_date'
				AND Location = '" . $sql_room_list. "1') $co2_pm25_sql ORDER BY datetime DESC; " ; */

				?>

				<a class = "btn btn-primary" href="generate_table">Go Back</a>
				<br>
				<br>
				<form method="POST">
				<button class = "btn btn-success" name = "download_csv">Download CSV</button>
				</form>
				<br>
				<form method="POST">
				<button class = "btn btn-warning" name = "generate_graphs">Generate Custom Graph</button>
				</form>
				<?php

				if(isset($_POST['generate_graphs'])){
					$_SESSION['generate_graphs'] = $sql;

					echo "<script>window.location.href = 'custom_graphs';</script>";
				}


				if(isset($_POST['download_csv'])){

				$_SESSION['csv_room_list'] = $location_array;

				echo "<script>window.location.href = 'download_generated_csv_table.php?start_month=$start_month&start_day=$start_day&start_year=$start_year&end_month=$end_month&end_day=$end_day&end_year=$end_year';</script>";

				}

				?>

				<br>
				<br>
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
							<th>Test Mode</th>
						</tr>
						<?php
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

	<td>" . $row[ "testMode" ] . "</td>

    </tr>";

							}

							echo "</table>";
						} else {
							echo "<h3>0 results</h3>";
							echo "</table>";
						}


						?>



			<?php

			}?>


</div>
			</div>
		</div>
					</div>
		</div>






		<?php //include '../../templates/footer.php';?>

