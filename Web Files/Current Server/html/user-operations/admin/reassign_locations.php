<?php include '../../templates/hubheader.php';

if(!isset($_SESSION[ 'is_admin' ])){
    Redirect('../../default-operations/hub?error=Sorry! You need to be an Admin to access this page!');
}
date_default_timezone_set('Asia/Hong_Kong');
$current_date = date('m/d/Y h:i:s a', time());
?>


<!-- Content -->
<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">

		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h1>Reassign Location Names</h1>
				</div>
			</div>

		</div>
		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					
					<p>Current Setup:</p>
					<table class="table table-light project-table table-hover">
						<tr style="background-color:ghostwhite;">
							<th>ESP Number</th>
                            <th>Corresponding Name</th>
                            <th>Reassign Name</th>

							
						</tr>
						<?php

						$sql = "SELECT DISTINCT Location, esp_id FROM bdst_esp_info ORDER BY esp_id ASC";

                        $result = mysqli_query( $connection, $sql );
                    
						$resultCheck = mysqli_num_rows( $result );
						if ( $resultCheck > 0 ) {
							while ( $row = mysqli_fetch_assoc( $result ) ) {
                                $esp_id = $row[ "esp_id" ];
                                $reassign = "<a class = 'btn btn-primary' href='reassign_espLoc_action?esp_id=$esp_id'>Click to Reassign</a>";

                                echo "<tr>
                                
    <td>" . $row[ "esp_id" ] . "</td>
	
    <td>" . $row[ "Location" ] . "</td>

    <td>" . $reassign . "</td>
	
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


