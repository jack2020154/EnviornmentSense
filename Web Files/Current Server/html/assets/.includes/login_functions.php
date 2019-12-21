<?php
//login function used in login.php
function login_user_function($username, $password, $connection){
	$password1 = md5($password);
	$password2 = sha1($password1);
    //logging the user in
	$sql = "SELECT * FROM `bdst_login_data` WHERE user = '$username' AND pwd = '$password2';";
	$result = mysqli_query( $connection, $sql );
	$count = mysqli_num_rows( $result );
	if ( $count == 1 ) {
		while ( $user_name = $result->fetch_assoc() ):
		$login = $user_name['login_id'];
		endwhile;
		return $login;
	} else {
		$login = "failed";
		return $login;
	}
}

function is_admin_verification($username, $connection){
	$sql = "SELECT * FROM `bdst_login_data` WHERE user = '$username' AND is_admin = '1';";
	$result = mysqli_query( $connection, $sql );
	$count = mysqli_num_rows( $result );
	if ( $count == 1 ) {
		return true;
	} else {
		return false;
	}
}


?>