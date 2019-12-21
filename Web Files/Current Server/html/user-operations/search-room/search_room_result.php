<?php include '../../templates/hubheader.php';





if ( isset( $_GET[ 'room_search' ] ) ) {
	$search = mysqli_real_escape_string( $connection, $_GET[ 'room_search' ] );
	$sql = "SELECT DISTINCT Location FROM Test01 WHERE Location LIKE '%$search%';";
	$result = mysqli_query( $connection, $sql );
	$queryResult = mysqli_num_rows( $result );


	?>
	<!-- Content -->
	<div class="content">
		<!-- Animated -->
		<div class="animated fadeIn">

			<div class="col-lg-12 col-md-6">
				<div class="card">
					<div class="card-body">

						<?php echo "<h1>Search for Room: $search </h1>"?>
						<br>

						<label for="">Didn't find what you want?</label>
						<a class="btn btn-warning" href="search_room.php">Search Again</a>

						<br>


						<?php

						echo "" . $queryResult . " result(s) for '$search' <hr>";


						if ( $queryResult > 0 ) {
							while ( $row = mysqli_fetch_assoc( $result ) ) {
								echo "
				
					<div>
					<h3>" . $row[ 'Location' ] . "</h3>
					</div>
					<br>
					<a class = 'btn btn-primary' href = 'room_history.php?Location=" . $row[ 'Location' ] . "'>View Location Data</a>
				<hr>";


							}

						} else {
							echo "There are no results matching your search.";
						}

						} else {
							echo '<script>window.location.href = "search_room.php";</script>';
						}
						?>

						</form>
					</div>
				</div>

			</div>
		</div>
	</div>




	<?php include '../../templates/footer.php';?>
