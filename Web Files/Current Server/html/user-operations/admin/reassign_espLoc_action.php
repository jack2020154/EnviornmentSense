<?php include '../../templates/hubheader.php';

if(!isset($_SESSION[ 'is_admin' ])){
    Redirect('../../default-operations/hub?error=Sorry! You need to be an Admin to access this page!');
}
date_default_timezone_set('Asia/Hong_Kong');
$current_date = date('m/d/Y h:i:s a', time());





if(isset($_GET[ 'esp_id' ])){
    $esp_id = mysqli_real_escape_string( $connection, $_GET[ 'esp_id' ] );
    $sql = "SELECT * FROM bdst_esp_info WHERE esp_id = '$esp_id'";
    $result = mysqli_query( $connection, $sql );
    $resultCheck = mysqli_num_rows( $result );
		if ( $resultCheck > 0 ) {
			while ( $row = mysqli_fetch_assoc( $result ) ) {
                $location = $row['location'];
            }
        }    
?>
<!-- Content -->
<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">

		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
                    <h1>Reassign Location Name</h1>
                    <hr>
                    <?php echo "<h3>ESP ID Currently On: <label>" . $esp_id. "</label></h3>"?>
                    <?php echo "<h3>Location Currently Set: " . $location. "</h3>"?>
					<br>
					<strong>NOTE: You must use a name from the pre-determined list below. Otherwise the system will evaluate it as a non-legitimate sensor name.
					<br>
					<br>
					Please refer to school layout maps to determine the Location of the room and its name.
					</strong>
					<br>
                    <form method="POST">
						
						<!-- <input type="text" class = "form-control" placeholder="Enter New Location" name="reassign_esp_room"> -->
						<!-- <br>
						<br> -->
						<br>
						<select id="room_names" name="reassign_esp_room" class = "form-control">        
						</select>
						 <br>
						 <br>
						<button type="submit" class = "btn btn-secondary" name = "submit_search">Submit Value</button>
					</form>
				</div>
            </div>
				</div>
			</div>
		</div>



	</div>
</div>
<?php 
if(isset($_POST['reassign_esp_room'])){
	$reassign_esp_room = mysqli_real_escape_string( $connection, $_POST[ 'reassign_esp_room' ] );
	$sql = "UPDATE bdst_esp_info
	SET location = '$reassign_esp_room'
	WHERE esp_id = '$esp_id';";

	$result = mysqli_query( $connection, $sql );
	if($result){
		Redirect('reassign_locations?success=Sucessfully updated');
	} else {
		Redirect('reassign_locations?error=Something went wrong');
	}
}
} ?>

<script>
var esp_id = document.getElementsByTagName("label");


var Room_names = new Array(
	"select one of the following rooms:",
	"testmode_id_" + esp_id[0].innerHTML,
	//Elementary
	'E101B', 'E103', 'E104', 'E105', 'E106',
                            'E107', 'ef1stair', 'E111', 'E121', 'E117',
                            'E115', 'E114', 'E113', 'E108', 'E109', 'esf1hallway', 'E122',
        
                            'esf2stairs', 'E207', 'E206', 'E205', 'E204',
                            'E203', 'E202', 'E201', 'E209' ,'E208', 'E221',
                            'E216', 'E215', 'E214', 'E213', 'E220', 'esf2hall',
        
                            'esf3stairs', 'E307', 'E306', 'E305', 'E304',
                            'E303', 'E302', 'E301', 'E309', 'E308', 'E321',
                            'E316', 'E315', 'E314', 'E313', 'E320', 'E311',
                            'E322', 'E310',
        
                            'esf4stairs', 'E407', 'E406', 'E405', 'E404', 'E403',
                            'E402', 'E401', 'E409', 'E408', 'E421', 'E416', 'E415',
							'E414', 'E413', 'E422', 'E410', 'esf4hall', 'esf3hall',
	//Intermediate Building
	'M110', 'M111', 'M115' ,'Intermediate_hallway',
                            'M109', 'M108', 'M102', 'M104', 'M105', 'M106',
                            'M107', 'M101A', 'M101', 'M101B', 'M101C',
                            'M101D', 'M124','M123A', 'M123B', 'M123C',
                            'M121B', 'M121A', 'M121', 'M123', 'M119', 'M117',
                            'M120', 'M116', 'M122', 'M124A',
        
                            'intermediate_f2_hallways', 'M211', 'M210', 'M209', 'M208', 'M207', 'M206',
                            'M205', 'M204', 'M202', 'M201', 'M225', 'M224',
							'M223', 'M216', 'M219', 'M220', 'M217',
	
	//The PC
	'PC_entrance', 'PC_commons',
                  'P106', 'stairs','P103', 'P107',
                  'P108', 'P111', 'P113b', 'P113a',
                  'P116', 'P117', 'P121', 'P128',
                  'P132', 'P133', 'P134', 'P135',
                  'P137', 'P138',
        
                  'P235', 'P234', 'P233' , 'P232', 'P230', 'P201', 'P202A',
                  'P202', 'P203', 'P204', 'PChallA', 'PCstairs', 'PCoverhead',
                   'P207', 'H208', 'P209', 'P211',
				  'PCac',
				  
	//rittman
	'R101', 'R102', 'R110', 'R103', 'lobby_hallway', 'R104',
                            'R105', 'R107', 'R106', 'R108', 'R109', 'f2_hallway',
                            'R220', 'R221', 'practice_rooms', 'R228', 'R229',
                            'f3_hallway', 'R302', 'R301', 'R303', 'R305', 'R306',
                            'R307', 'R308', 'R401', 'R402', 'R403', 'R407', 'R404',
							'R405', 'R406', 'roof_garden', 'f4_hallway',
							


	//HS
	'H036', 'H034', 'H032', 'H030', 'H026', 'H020', 'H018',
                            'H015B', 'H015A', 'H108', 'H107', 'H006', 'H005', 'H004',
                            'H003', 'H041', 'H040', 'H015', 'L1stair',
        
                            'H127', 'H117', 'F1stairs', 'f1hall', 'H105', 'H104', 'H103',
                            'H102', 'H115', 'H115A', 'H116', 'H118', 'H116A', 'f1stair2',
                            'welcomecenter',
        
                            'H227', 'H227C', 'H227A', 'H227B','H229', 'H230', 'H231', 'H232',
                            'H233', 'H234', 'H235', 'H236', 'H237', 'H240', 'H216', 'H218', 'H218C',
                            'H218A', 'pclounge', 'pccafe', 'H206', 'H205', 'H204', 'H203', 'H201',
        
                            'operations', 'H301','H306', 'H305', 'H304', 'H303', 'H318', 'f3hall1', 'f3hall2',
        
                            'H405', 'H404', 'H403', 'H401', 'f4hallA', 'f4hallB', 'f4hallC',
                            'H429', 'H418', 'H416', 'H417', 'H429A',
        
                            'H506', 'H505', 'H504', 'H503', 'H501', 'f5HallC', 'f5HallA',
                            'f5HallB', 'H529', 'H518', 'H518A', 'H516', 'H517',
        
                            'f6hallB', 'H606', 'H605', 'H604', 'H603', 'f6hallA', 'H601'),


selectEl = document.getElementById('room_names');


for(var i = 0; i < Room_names.length; i++){
    selectEl.options.add(new Option(Room_names[i], Room_names[i]));
}            


</script>


<?php include '../../templates/footer.php';?>

