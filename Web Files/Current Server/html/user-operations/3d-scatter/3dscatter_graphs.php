<?php include '../../templates/hubheader.php'; ?>


<!-- Content -->
<div class="content">
	<!-- Animated -->
	<div class="animated fadeIn">

<div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
                    <h3>Filters + Selectors</h3>
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
                    <button class = "btn btn-danger" id = 'suspicious-btn'>Suspiscious Data</button>
                    <br>
                    <br>
                    <span>Select a day to analyze:</span>
					<input type="text" class="form-control js-date-picker" id = 'date_js' value="Mar 15, 2019">

					<br>
					<button class = "btn btn-success" id = 'generate_scatter_btn'></button>
				</div>
			</div>
        </div>

        <div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h3>3D Scatter - Time, PM25, CO2</h3>
					<div id="3dscatter-1"></div>
				</div>
			</div>
        </div>
        
        <div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h3>3D Scatter - Time, Temperature, Humidity</h3>
					<div id="3dscatter-2"></div>
				</div>
			</div>
        </div>
        
        <div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h3>3D Scatter - Time, Temperature, PM25</h3>
					<div id="3dscatter-3"></div>
				</div>
			</div>
        </div>

        <div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h3>3D Scatter - Time, Temperature, CO2</h3>
					<div id="3dscatter-4"></div>
				</div>
			</div>
        </div>

        <div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
					<h3>3D Scatter - CO2, PM25, Temperature</h3>
					<div id="3dscatter-5"></div>
				</div>
			</div>
        </div>



    </div>
</div>

<script src="<?php echo $folder_nav;?>js/3dscatter_graphs.js"></script>

<?php include '../../templates/footer.php';?>


<script>


new Picker(document.querySelector('.js-date-picker'), {
      format: 'MMM D, YYYY',
      text: {
        title: 'Pick a date',
      },
    });

</script>