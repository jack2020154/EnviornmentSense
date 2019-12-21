<?php  
session_start();
include "../assets/.includes/dbhconnect.php";
include "../assets/.includes/login_functions.php";

//Redirecting the user if they are already logged in. 
if ( isset( $_SESSION[ 'id' ] ) ) {
  Redirect('hub');
}

if ( isset( $_POST ) & !empty( $_POST ) ) {
	$username = mysqli_real_escape_string( $connection, $_POST[ 'username' ] );
	$password = mysqli_real_escape_string( $connection, $_POST[ 'password' ] );
  //function in login_functions.php
	$logged_in =  login_user_function($username, $password, $connection);
	
	
	if($logged_in != "failed"){		
    $_SESSION[ 'id' ] = $logged_in;

    //TODO: Create this function
    if(is_admin_verification($username, $connection)){
      $_SESSION[ 'is_admin' ] = "this user is an admin";
    } 


    Redirect('hub');
	} elseif ($logged_in == "failed") {
    Redirect('login.php?error=Incorrect Username or Password', false);
	} else {
    Redirect('login.php?error=Something Went Wrong', false);
	}
} ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <meta name="description" content="Login to view ENV Concordia">
  <meta name="author" content="Vincent Garreau" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <link rel="stylesheet" media="screen" href="../assets/login_assets/css/style.css">
</head>
<body>
<div id="particles-js">
  <div id="login">
    <h3>Login</h3>
    <br>
        <form method="POST">
          <div>
            <label for="username">Username</label>
            <br>
            <input type="username" id="email" name="username">
          </div>
          <div>
            <label for="password">Password</label>
            <br>
           <input type="password" id="password" name="password">
          </div>
          <input type="submit" value="Login">
        </form>
      </div>
</div>

<!-- scripts -->
<script src="../assets/login_assets/particles.js"></script>
<script src="../assets/login_assets/js/app.js"></script>
<!-- MIT License https://github.com/VincentGarreau/particles.js/  -->
</body>
</html>
