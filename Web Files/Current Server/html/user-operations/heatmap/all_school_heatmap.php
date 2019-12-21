<?php include '../../templates/hubheader.php';


if (isset($_POST['dateToQuery'])){
  $datetime = mysqli_real_escape_string($connection, $_POST['dateToQuery']);
  $_SESSION['dateToHeatmap'] = $datetime;
}

if(isset($_SESSION['dateToHeatmap'])){
  $currentDate = $_SESSION['dateToHeatmap'];
} else {
  $currentDate = date("Y-m-d");
}
?>

    <style>
      body, html { margin:0; padding:0; height:100%; font-family:Arial;}
      #heatmapContainerWrapper { width:100%; height:100%; }
      #heatmapContainer { width:100%; height:100%;}
      h1 { position:relative; background:lightblue; color:white; padding:10px; font-weight:200;}
      #all-examples-info { position:absolute; background:white; font-size:16px; padding:20px; top:100px; width:350px; line-height:150%; border:1px solid rgba(0,0,0,.2);}
      img { background:; }
    </style>


  <body>
    <h1 id = 'display_time'></h1>
    <br>
    <div class="col-lg-12 col-md-6">
			<div class="card">
				<div class="card-body">
        <h2>Concordia Intl School All School Heatmap</h2>
        <form METHOD="POST">
        <input type="date" name = "dateToQuery">
        <button class = "btn btn-success">Submit</button>
        </form>
        <?php echo "<h2>Designated Date: " . $currentDate . "</h2>" ?>

        <!-- TODO: Implement the time search function and fix the spacing stuff -->
        <br>
 <h2 id="heatmap_type_displayed"></h2>



<button class = "btn btn-success" id = 'PM25-btn'>AQI Heatmap</button>
<button class = "btn btn-warning" id = 'CO2-btn'>CO2 Heatmap</button>
<button class = "btn btn-secondary" id = 'Humidity-btn'>Humidity Heatmap</button>
<button class = "btn btn-danger" id = 'Temperature-btn'>Temperature Heatmap</button>
<br>
<br>

<button class = "btn btn-primary" id = '0btn'>0:00</button>

<button class = "btn btn-primary" id = '1btn'>1:00</button>

<button class = "btn btn-primary" id = '2btn'>2:00</button>

<button class = "btn btn-primary" id = '3btn'>3:00</button>

<button class = "btn btn-primary" id = '4btn'>4:00</button>

<button class = "btn btn-primary" id = '5btn'>5:00</button>

<button class = "btn btn-primary" id = '6btn'>6:00</button>

<button class = "btn btn-primary" id = '7btn'>7:00</button>

<button class = "btn btn-primary" id = '8btn'>8:00</button>

<button class = "btn btn-primary" id = '9btn'>9:00</button>

<button class = "btn btn-primary" id = '10btn'>10:00</button>

<button class = "btn btn-primary" id = '11btn'>11:00</button>

<button class = "btn btn-primary" id = '12btn'>12:00</button>

<button class = "btn btn-primary" id = '13btn'>13:00</button>

<button class = "btn btn-primary" id = '14btn'>14:00</button>

<button class = "btn btn-primary" id = '15btn'>15:00</button>

<button class = "btn btn-primary" id = '16btn'>16:00</button>

<button class = "btn btn-primary" id = '17btn'>17:00</button>

<button class = "btn btn-primary" id = '18btn'>18:00</button>

<button class = "btn btn-primary" id = '19btn'>19:00</button>

<button class = "btn btn-primary" id = '20btn'>20:00</button>

<button class = "btn btn-primary" id = '21btn'>21:00</button>

<button class = "btn btn-primary" id = '22btn'>22:00</button>

<button class = "btn btn-primary" id = '23btn'>23:00</button>
<br>
<br>


<div id="heatmapContainerWrapper">
      <div id="heatmapContainer"></div>
      <!-- The datapoints displayed -->
      <span id="displayed_data"></span>
</div>

    </div>



    <script src="<?php echo $folder_nav;?>js/heatmap.js"></script>
    <script src="<?php echo $folder_nav;?>js/svg-area-heatmap.js"></script>
    <script src="<?php echo $folder_nav;?>js/whole_school.js"></script>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
<script src="<?php echo $folder_nav;?>assets/js/main.js"></script>






</body>
</html>

<?php// include 'footer.php'?>
