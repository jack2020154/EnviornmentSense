<?php include '../../templates/hubheader.php';?>



<!-- Content -->
<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">

		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h1>Search Room</h1>
					<p>This page is to search for specific rooms and their history.</p>
					
					<form action="search_room_result.php">
						
						<input type="text" placeholder="Enter Room Number" name="room_search">
						<br>
						<br>
						<button type="submit" class = "btn btn-secondary" name = "submit_search">Search</button>
					</form>
				</div>
			</div>

		</div>
		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					
					<p>A room is online if there has been an upload within the past 2 days.</p>
					<table class="table table-light project-table table-hover">
						<tr style="background-color:ghostwhite;">
							<th>Room Number</th>
							<th>Last Upload Time</th>
							<th>Days Since Last Upload</th>
							<th>Monitoring Status</th>
							<th>Room History</th>
							
						</tr>
						<?php


						$sql = "SELECT DISTINCT Location FROM Test01 WHERE datetime > '2019-3-19' ORDER BY Location ASC";

						$result = mysqli_query( $connection, $sql );


						$resultCheck = mysqli_num_rows( $result );
						if ( $resultCheck > 0 ) {
							while ( $row = mysqli_fetch_assoc( $result ) ) {
								
								$Location_id = $row["Location"];
								
								$esp_query = "SELECT Location, datetime, esp_id FROM Test01 WHERE Location = '$Location_id' ORDER BY datetime Desc LIMIT 1";
								
								$esp_query_result = mysqli_query( $connection, $esp_query );
								
								while ( $row = mysqli_fetch_assoc( $esp_query_result ) ) {
									
									$history = "<a class = 'btn btn-primary' href='room_history.php?Location=$Location_id'>History</a>";
									
									
									//will change this to show minutes since uploaded
									
									$current_date = date("Y-m-d H:i:s");
									$current = strtotime($current_date);
									
 									 $date = strtotime($row[ "datetime"]);

									
									 //$datediff = $date - $current;
									
									$datediff = $current - $date;
									
									$difference = floor($datediff/(60*60*24));
									
									 
									//Testing within two days of today. If the sensor hasn't been uploading then
									//it is offline
									
									//to prevent -1 days if it uploaded today
									if ($difference == -1){
										$difference = 0;
									}
									
									if ($difference <= 2){
										;$status = "Online";
										
										$status = "<button class= 'btn btn-success'>Online</button>";
											
									} else {
										$status = "Offline";
										
										$status = "<button class= 'btn btn-danger'>Offline</button>";
									}
									
									


								echo "<tr>
    <td>" . $row[ "Location" ] . "</td>
	
    <td>" . $row[ "datetime" ] . "</td>
	
	 <td>" . $difference . "</td>
	
    <td>" . $status . "</td>
	
	<td>" . $history . "</td>


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


