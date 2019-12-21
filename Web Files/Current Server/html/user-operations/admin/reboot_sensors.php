<?php include '../../templates/hubheader.php';

if(!isset($_SESSION[ 'is_admin' ])){
    Redirect('../../default-operations/hub.php?error=Sorry! You need to be an Admin to access this page!');
}

date_default_timezone_set('Asia/Hong_Kong');
$current_date = date('m/d/Y h:i:s a', time());

$upload_hour_tolerance = 1;
?>


<!-- Content -->
<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">

		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h1>Reboot Sensors</h1>
					<br>
					<p>Click on one of the sensors to reboot it... Inactivity for 3 hrs = OFFLINE</p>
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
							<th>Reboot Sensor</th>
							
						</tr>
						<?php

						$sql = "SELECT DISTINCT esp_id FROM Test01 WHERE datetime > '2019-3-19' ORDER BY esp_id ASC";

						$result = mysqli_query( $connection, $sql );

						$resultCheck = mysqli_num_rows( $result );
						if ( $resultCheck > 0 ) {
							while ( $row = mysqli_fetch_assoc( $result ) ) {
								
								$esp_id = $row["esp_id"];
								
								$esp_query = "SELECT Location, datetime, esp_id FROM Test01 WHERE esp_id = '$esp_id' ORDER BY datetime Desc LIMIT 1";
								
								$esp_query_result = mysqli_query( $connection, $esp_query );
								
								while ( $row = mysqli_fetch_assoc( $esp_query_result ) ) {
									
									$reboot = "<a class = 'btn btn-primary' href='reboot_sensor_action?esp_id=$esp_id'>Click 2 Reboot</a>";
					
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
										;$status = "Online";
										
										$status = "<button class= 'btn btn-success'>Online</button>";
											
									} else {
										$status = "Offline";
										
										$status = "<button class= 'btn btn-danger'>Offline</button>";
									}
									
									//Querying whether it is currently rebooting or not
									$sqlRebootStatus = "SELECT rebootStatus FROM bdst_esp_info WHERE rebootStatus = '1' AND esp_id = '$esp_id'";
									$sqlRebootStatusResult = mysqli_query( $connection, $sqlRebootStatus );
									while ( $row2 = mysqli_fetch_assoc( $sqlRebootStatusResult ) ) {
										if ($row2['rebootStatus'] == '1'){
											$reboot = "<a class = 'btn btn-warning' href='reboot_sensor_action?esp_id=$esp_id'>Rebooting...</a>";
										} else {
											$reboot = "<a class = 'btn btn-primary' href='reboot_sensor_action?esp_id=$esp_id'>Click 2 Reboot</a>";
										}
									}


								echo "<tr>
    <td>" . $row[ "esp_id" ] . "</td>
	
    <td>" . $row[ "datetime" ] . "</td>
	
    <td>" . $row[ 'Location' ] . "</td>
	
	 <td>" . $difference_days . "</td>
	 
	 <td>" . $difference_hours . "</td>
	
    <td>" . $status . "</td>
	
	<td>" . $reboot . "</td>


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



<?php include '../../templates/footer.php';?>
