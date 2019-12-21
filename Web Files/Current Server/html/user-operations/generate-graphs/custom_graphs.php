<?php include '../../templates/hubheader.php';?>


<div class="content">
  	<div class="animated fadeIn">

    <div class="col-lg-12 col-md-6">
					<div class="card">
						<div class="card-body">
							<h3>CO2 Graph</h3>
							<div id="myDiv-CO2"></div>
						</div>
					</div>
        </div>
        
        <div class="col-lg-12 col-md-6">
					<div class="card">
						<div class="card-body">
							<h3>PM2.5 Graph</h3>
							<div id="myDiv-PM25"></div>
						</div>
					</div>
        </div>
        
        <div class="col-lg-12 col-md-6">
					<div class="card">
						<div class="card-body">
							<h3>Temperature Graph</h3>
							<div id="myDiv-Temperature"></div>
						</div>
					</div>
        </div>
        
        <div class="col-lg-12 col-md-6">
					<div class="card">
						<div class="card-body">
							<h3>Humidity Graph</h3>
							<div id="myDiv-Humidity"></div>
						</div>
					</div>
        </div>
        
        <div class="col-lg-12 col-md-6">
					<div class="card">
						<div class="card-body">
							<h3>CO2 Violin</h3>
							<div id="ViolinPlot-CO2"></div>
						</div>
					</div>
        </div>
        
        <div class="col-lg-12 col-md-6">
					<div class="card">
						<div class="card-body">
							<h3>PM2.5 Violin Graph</h3>
							<div id="ViolinPlot-PM25"></div>
						</div>
					</div>
        </div>
        
        <div class="col-lg-12 col-md-6">
					<div class="card">
						<div class="card-body">
							<h3>Temperature Violin Graph</h3>
							<div id="ViolinPlot-Temperature"></div>
						</div>
					</div>
        </div>
        
        <div class="col-lg-12 col-md-6">
					<div class="card">
						<div class="card-body">
							<h3>Humidity Violin Graph</h3>
							<div id="ViolinPlot-Humidity"></div>
						</div>
					</div>
				</div>


</div>
</div>

<!-- javascript -->

<script src="<?php echo $folder_nav; ?>js/custom_graphs.js"></script>
<?php include '../../templates/footer.php';?>
