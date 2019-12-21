<?php include '../../templates/hubheader.php';

function create_days_dropdown( $a ) {
	if ( $a <= 31 ) {
		echo "<option value=" . $a . ">" . $a . "</option>";
		create_days_dropdown( $a + 1 );
	}
}

function create_years_dropdown( $a ) {
	if ( $a <= 2030 ) {
		echo "<option value=" . $a . ">" . $a . "</option>";
		create_years_dropdown( $a + 1 );
	}
}
?>


<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">

		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h1>Generate Tables</h1>
					<p>Fill out both the time and the desired rooms.</p>

				</div>
			</div>
		</div>



		<!--		-->
		<div class="container">
			<div class="row">
				<div class="col-lg-5 col-md-6">
					<div class="card">
						<form action="generate_table_result.php">

							<div class="card-body">

								<h3>Select Timeframe</h3>
								<br>
								<hr>
								<h3>Start Date</h3>
								<div>
									<label for="">Month</label>
									<select class="form-control" name="start_month">
										<option value="1">January</option>
										<option value="2">February</option>
										<option value="3">March</option>
										<option value="4">April</option>
										<option value="5">May</option>
										<option value="6">June</option>
										<option value="7">July</option>
										<option value="8">August</option>
										<option value="9">September</option>
										<option value="10">October</option>
										<option value="11">November</option>
										<option value="12">December</option>
									</select>
									<!--  -->
									<label for="">Day</label>
									<select class="form-control" name="start_day">
										<?php
										$days = 1;
										create_days_dropdown( $days );
										?>
									</select>

									<!--  -->
									<label for="">Year</label>
									<select class="form-control" name="start_year">
										<?php
										$year = 2019;
										create_years_dropdown( $year );
										?>
									</select>
									<br>
									<label for="">Select Time of Day</label>
										<select class="form-control" name="start_hour">
											<option value="1:00">1:00</option>
									    <option value="2:00">2:00</option>
									    <option value="3:00">3:00</option>
									    <option value="4:00">4:00</option>
									    <option value="5:00">5:00</option>
									    <option value="6:00">6:00</option>
									    <option value="7:00">7:00</option>
									    <option value="8:00">8:00</option>
									    <option value="9:00">9:00</option>
									    <option value="10:00">10:00</option>
									    <option value="11:00">11:00</option>
									    <option value="12:00">12:00</option>
									    <option value="13:00">13:00</option>
									    <option value="14:00">14:00</option>
									    <option value="15:00">15:00</option>
									    <option value="16:00">16:00</option>
									    <option value="17:00">17:00</option>
									    <option value="18:00">18:00</option>
									    <option value="19:00">19:00</option>
									    <option value="20:00">20:00</option>
									    <option value="21:00">21:00</option>
									    <option value="22:00">22:00</option>
									    <option value="23:00">23:00</option>
									    <option value="24:00">24:00</option>
										</select>
								</div>
								<!-- Jack let me live okay im tired i dont want to make a for loop echo for this-->

								<br>

								<hr>
								<h3>End Date</h3>
								<div>
									<label for="">Month</label>
									<select class="form-control" name="end_month">
										<option value="1">January</option>
										<option value="2">February</option>
										<option value="3">March</option>
										<option value="4">April</option>
										<option value="5">May</option>
										<option value="6">June</option>
										<option value="7">July</option>
										<option value="8">August</option>
										<option value="9">September</option>
										<option value="10">October</option>
										<option value="11">November</option>
										<option value="12">December</option>
									</select>
									<!--  -->
									<label for="">Day</label>
									<select class="form-control" name="end_day">
										<?php
										$days = 1;
										create_days_dropdown( $days );
										?>
									</select>

									<!--  -->
									<label for="">Year</label>
									<select class="form-control" name="end_year">
										<?php
										$year = 2019;
										create_years_dropdown( $year );
										?>
									</select>
									<br>
									<!-- Jack let me live okay im tired i dont want to make a for loop echo for this-->
									<label for="">Select Time of Day</label>
									  <select class="form-control" name="end_hour">
											<option value="1:00">1:00</option>
											<option value="2:00">2:00</option>
											<option value="3:00">3:00</option>
											<option value="4:00">4:00</option>
											<option value="5:00">5:00</option>
											<option value="6:00">6:00</option>
											<option value="7:00">7:00</option>
											<option value="8:00">8:00</option>
											<option value="9:00">9:00</option>
											<option value="10:00">10:00</option>
											<option value="11:00">11:00</option>
											<option value="12:00">12:00</option>
											<option value="13:00">13:00</option>
											<option value="14:00">14:00</option>
											<option value="15:00">15:00</option>
											<option value="16:00">16:00</option>
											<option value="17:00">17:00</option>
											<option value="18:00">18:00</option>
											<option value="19:00">19:00</option>
											<option value="20:00">20:00</option>
											<option value="21:00">21:00</option>
											<option value="22:00">22:00</option>
											<option value="23:00">23:00</option>
											<option value="24:00">24:00</option>
									  </select>


								</div>



							</div>
								<div class="card-body">
									<hr>
									<label for="">Max CO2 Value</label>
									<input type="number" name="max_co2" value="" class = 'form-control' placeholder = 'default : 2500 ppm'>
									<br>
									<label for="">Min CO2 Value</label>
									<input type="number" name="min_co2" value="" class = 'form-control' placeholder = 'default : 400 ppm'>
									<br>
									<label for="">Max PM25 Value</label>
									<input type="number" name="max_pm25" value=""  class = 'form-control' placeholder = 'default : 50 ppm'>
									<br>
									<label for="">Min PM25 Value</label>
									<input type="number" name="min_pm25" value=""  class = 'form-control' placeholder = 'default : 0 ppm'>
									<br>
									<label for="">Resolution Value:</label>
									<input type="number" name="resolution_input" value=""  class = 'form-control' placeholder = 'default : 3'>


								</div>

					</div>
				</div>




				<div class="col-lg-7 col-md-3">
					<div class="card">
						<div class="card-body">

							<h3>Select Rooms</h3>

							<table class="table table-light project-table table-hover">
								<tr style="background-color:ghostwhite;">
									<th>Location</th>
									<th>Include</th>

								</tr>
								<?php
								$checkbox = "checkbox";
								$room_array = array();
								$room_array = "room_array[]";
								$space = "test";
								//$room_array();
								$sql = "SELECT DISTINCT Location FROM Test01 WHERE datetime > '2019-3-20'";

								$result = mysqli_query( $connection, $sql );


								$resultCheck = mysqli_num_rows( $result );
								if ( $resultCheck > 0 ) {
									while ( $row = mysqli_fetch_assoc( $result ) ) {


										echo "<tr>
    <td>" . $row[ "Location" ] . "</td>

	<td><input type=" . $checkbox . " name= 'room_array[]'" . " value='" . $row[ 'Location' ] . "'<span><span> " . "</td>


    </tr>";

									}

									echo "</table>";
								} else {
									echo "0 results";
								}


								?>
								<button class="btn btn-primary" name='generate_table' type="submit" value='submit'>Submit this value</button>

								</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>



<?php include '../../templates/footer.php';?>

