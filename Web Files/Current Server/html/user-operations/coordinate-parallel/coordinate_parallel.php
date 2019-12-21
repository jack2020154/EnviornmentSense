<?php include '../../templates/hubheader.php'; ?>

<!-- Content -->
<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">

		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h1>Coordinate Plot Graph</h1>
				</div>
			</div>

		</div>
		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">

					<h3>Parallel Coordinate Plot</h3>
					<br>
					<h5>Select up to 5 rooms listed below and a day. Then generate a parallel coordinate plot.</h5>
          <br>

					<button class = "btn btn-danger" id = 'choices_left'></button>
          <br>
          <br>
          <h3 id = "chosen_rooms"></h3>
          <br>
          <span>Select a day to analyze:</span>
          <input type="text" class="form-control js-date-picker" id = 'date_js' value="Mar 15, 2019">
          <br>
		  <span>Include every Nth datapoint (1-20)</span>
		  <input type="number" class="form-control" id = "resolution_input" placeholder = "Default: 3">
		  <br>
		  <br>
          <h3 class = "btn btn-secondary" id = 'generate-btn'>Generate Graph</h3>
          <br>
          <br>
          <h3 id = "room_colors"></h3>
				</div>
			</div>
		</div>

		<div class="col-lg-12 col-md-12">
			<div class="card">
				<div class="card-body">
					<h3>Generated Map</h3>
          <div id="parallelDiv"></div>


				</div>
			</div>
    </div>
    

    <div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<table class="table table-light project-table table-hover">
						<tr style="background-color:ghostwhite;">
							<th>ESP Number</th>
							<th>Last Upload Time</th>
							<th>Last Upload Location</th>
							<th>Days Since Last Upload</th>
							<th>Hours Since Last Upload</th>
							<th>Status</th>
							
						</tr>
						<?php
						$upload_hour_tolerance = 3;
						$sql = "SELECT DISTINCT esp_id FROM Test01 WHERE datetime > '2019-3-15' ORDER BY esp_id ASC";

						$result = mysqli_query( $connection, $sql );


						$resultCheck = mysqli_num_rows( $result );
						if ( $resultCheck > 0 ) {
							while ( $row = mysqli_fetch_assoc( $result ) ) {
								
								$esp_id = $row["esp_id"];
								
								$esp_query = "SELECT * FROM Test01 WHERE esp_id = '$esp_id' ORDER BY datetime Desc LIMIT 1";
								
								$esp_query_result = mysqli_query( $connection, $esp_query );
								
								while ( $row = mysqli_fetch_assoc( $esp_query_result ) ) {
									
									$history = "<a class = 'btn btn-primary' href='sensor_history.php?esp_id=$esp_id'>History</a>";
					
									$current_date = date("Y-m-d H:i:s");
									
									
 									$date = strtotime($row[ "datetime" ]);
									
									$current = strtotime($current_date);
									
									// trying out minute code
									
							
									 $datediff = $current - $date;
									
									
									$difference_days = floor($datediff/(60*60*24));
									
									$difference_hours = floor($datediff/(60*60));
									 
									//3 hour tolerance If the sensor hasn't been uploading then
									//it is offline
									if ($difference_hours == -1){
										$difference_hours = 0;
									}
									
									if ($difference_days <= -1){
										$difference_days = 0;
									}
									
									if ($difference_hours <= $upload_hour_tolerance){
										$status = "Online";
										
                    $status = "<button class= 'btn btn-success' id = 'clicked' value = '$room_Value' >Online</button>";											
									} else {
                    $status = "Offline";
                    $room_Value = $row[ 'Location' ];
										
										$status = "<button class= 'btn btn-primary' id = 'clicked' value = '$room_Value' >Offline</button>";
									}
									
								echo "<tr>
    <td>" . $row[ "esp_id" ] . "</td>
	
    <td>" . $row[ "datetime" ] . "</td>
	
    <td>" . $row[ 'Location' ] . "</td>
	
	 <td>" . $difference_days . "</td>
	 
	 <td>" . $difference_hours . "</td>
	
    <td>" . $status . "</td>


    </tr>";
								}
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
<script src="<?php echo $folder_nav;?>js/coordinate_parallel.js"></script>

<?php include '../../templates/footer.php';?>
