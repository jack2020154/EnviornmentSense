<?php include '../../templates/hubheader.php';

if(!isset($_SESSION[ 'is_admin' ])){
    Redirect('../../default-operations/hub.php?error=Sorry! You need to be an Admin to access this page!');
}

date_default_timezone_set('Asia/Hong_Kong');
$current_date = date('m/d/Y h:i:s a', time());
?>

<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">

		<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
                    <h1>How to change the correction curves</h1>
				</div>
            </div>

            <div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
                    <p> To change the correction curves, please access the backend database and go to the table 
                        <strong>bdst_esp_info</strong> and alter any of the correction curves as necessary. </p>
                    
				</div>
            </div>
            
        </div>
    </div>
</div>








<?php include '../../templates/footer.php';?>
