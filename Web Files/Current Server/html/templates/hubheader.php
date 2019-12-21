<?php
session_start();
$folder_nav = "/";

include $_SERVER["DOCUMENT_ROOT"] . $folder_nav.  "assets/.includes/dbhconnect.php";
include $_SERVER["DOCUMENT_ROOT"] . $folder_nav. "assets/.includes/login_functions.php";
include $_SERVER["DOCUMENT_ROOT"] . $folder_nav. "assets/routes.php";


//Testing whether or not the person is logged in or not
if ( !isset( $_SESSION[ 'id' ] ) ) {
	Redirect($folder_nav . 'default-operations/login');
}
?>


<!doctype html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Project ENV</title>
	<meta name="description" content="Ela Admin - HTML5 Admin Template">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon" href="/bdst_april/assets/images/favicon.ico">
	<link rel="shortcut icon" href="/bdst_april/assets/images/favicon.ico">
<!-- Style Sheets -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
	<link rel="stylesheet" href="<?php echo $folder_nav; ?>assets/css/cs-skin-elastic.css">
	<link rel="stylesheet" href="<?php echo $folder_nav; ?>assets/css/style.css">
	<!-- Javascript time picker -->
	<link  href="<?php echo $folder_nav; ?>js/picker.css" rel="stylesheet">

	<link href="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/jqvmap@1.5.1/dist/jqvmap.min.css" rel="stylesheet">
	<script src="<?php echo $folder_nav; ?>js/plotly-latest.min.js" ></script>
</head>
<body>
	<!-- Left Panel -->
	<aside id="left-panel" class="left-panel " >
		<nav class="navbar navbar-expand-sm navbar-default">
			<div id="main-menu" class="main-menu collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li class="active">
						<a href="<?php echo $hub_abs_path ?>"><i class="menu-icon fa fa-laptop"></i>Dashboard </a>
					</li>
					<li class="menu-title">View Data</li>
					<!-- /.menu-title -->
<!-- 
					<li class="menu-item-has-children dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Tables</a>
						<ul class="sub-menu children dropdown-menu">
							<li><i class="fa fa-puzzle-piece"></i><a href="ui-buttons.html">Buttons</a>
							</li>
							<li><i class="fa fa-id-badge"></i><a href="ui-badges.html">Badges</a>
							</li>
							<li><i class="fa fa-bars"></i><a href="ui-tabs.html">Tabs</a>
							</li>
						</ul>
					</li>				 -->


					<li>
						<a href="<?php echo $report_24hr_abs_path ?>"> <i class="menu-icon fa fa-binoculars"></i>Full 24 hr Report</a>
					</li>
					<li>
						<a href="<?php echo $scatter_3d_abs_path ?>"> <i class="menu-icon fa fa-cube"></i> 3D Models </a>
					</li>
					<li>
						<a href="<?php echo $coordinate_parallel_abs_path ?>"> <i class="menu-icon fa fa-map-o"></i>Coordinate Parallel</a>
					</li>
					<li>
						<a href="<?php echo $heatmap_abs_path ?>"> <i class="menu-icon fa fa-coffee"></i>Concordia Heatmap</a>
					</li>
					<li>
						<a href="<?php echo $generate_table_abs_path ?>"> <i class="menu-icon fa fa-table"></i> Generate Tables</a>
					</li>
					<li>
						<a href="<?php echo $view_all_data_abs_path ?>"> <i class="menu-icon fa fa-folder-o"></i>View All Data</a>
					</li>
					<!-- <li> -->
						<!-- <a href="<?php echo $view_layout_abs_path ?>"> <i class="menu-icon fa fa-search"></i>School Layout </a> -->
					<!-- </li> -->
					<!--                -->

					<li class="menu-title">Manage Sensors</li>
					<li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-bar-chart"></i>Charts</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-line-chart"></i><a href="<?php echo $sensor_status_abs_path ?>">View Sensor Status</a></li>
                            <li><i class="menu-icon fa fa-area-chart"></i><a href="<?php echo $search_room_abs_path ?>">Search Room</a></li>
                        </ul>
                    </li>

				 <?php if(isset($_SESSION[ 'is_admin' ])){ ?>
					<li class="menu-title">Admin Actions</li>
					<li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-tasks"></i>Admin Actions</a>
                        <ul class="sub-menu children dropdown-menu">
							<li><i class="menu-icon fa fa-area-chart"></i><a href="<?php echo $reboot_sensors_abs_path ?>">Reboot Sensors</a></li>
							<li><i class="menu-icon fa fa-area-chart"></i><a href="<?php echo $reassign_names_abs_path ?>">Reassign Location Names</a></li>
							<li><i class="menu-icon fa fa-area-chart"></i><a href="<?php echo $change_corr_curves_abs_path ?>">Change Correction Curves</a></li>

							<br>
							<br>
						</ul>
						
					</li>
				 <?php }?>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</nav>
	</aside>
	<!-- /#left-panel -->
	<!-- Right Panel -->
	<div id="right-panel" class="right-panel">
		<!-- Header-->
		<header id="header" class="header">
			<div class="top-left">
				<div class="navbar-header">
					<a class="navbar-brand" href="<?php echo $hub_abs_path ?>"><img src="<?php echo $folder_nav; ?>assets/images/logo.png" alt="Logo"></a>
					<a class="navbar-brand hidden" href="<?php echo $hub_abs_path ?>"><img src="<?php echo $folder_nav; ?>assets/images/logo2.png" alt="Logo"></a>
					<a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>
				</div>
			</div>
			<div class="top-right">
				<div class="header-menu">

					<div class="user-area dropdown float-right">
						<a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="<?php echo $folder_nav; ?>assets/images/admin.jpg" alt="User Avatar">
                        </a>


						<div class="user-menu dropdown-menu">
<!--							<a class="nav-link" href="#"><i class="fa fa- user"></i>My Profile</a>-->

<!--							<a class="nav-link" href="#"><i class="fa fa -cog"></i>Settings</a>-->

							<a class="nav-link" href="logout.php"><i class="fa fa-power -off"></i>Logout</a>
						</div>
					</div>

				</div>
			</div>
		</header>
		<br>

	<?php if (isset($_GET['success'])) {?>
	<div class="alert alert-success" role="alert" style="margin-top: 0px;">
	<?php echo $_GET['success']; ?> </div>
    <?php }?>

	<?php if (isset($_GET['error'])) {?>
	<div class="alert alert-danger" role="alert" style="margin-top: 0px;">
	<?php echo $_GET['error']; ?> </div>
	<?php }?>
		<!-- /#header -->
		<script src="<?php echo $folder_nav; ?>js/jquery.min.js"></script>
		<script src="<?php echo $folder_nav; ?>js/Chart.min.js"></script>
		<script src="<?php echo $folder_nav; ?>js/moments.js"></script>
		<script src="<?php echo $folder_nav; ?>js/picker.js"></script>
