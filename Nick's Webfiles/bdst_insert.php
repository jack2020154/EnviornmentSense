<?php
	// This block of code parses the POST request being sent to it (the PHP page)
	// It does this by taking the POST request and looking for keywords in it.
	// Lets use the first line as an example, where location data is being sent.
	// If the PHP file sees the keyword 'location' in the POST request, it assigns it the value that follows it.
	// Remember how our POST request looked like this: "location=xxx&temperature=xxx"
	// The ampersand denotes when one value has ended and another field begins
	// This block of code is the most complicated, so feel free to ask me questions

	/*
	$Location=htmlentities($_POST['location']);
	$temperature=htmlentities($_POST['temperature']);
	$humidity=htmlentities($_POST['heatIndex']);
	$co2=htmlentities($_POST['co2']);
	$pm25=htmlentities($_POST['pm25']);
	$pm10=htmlentities($_POST['pm10']);
	$pm100=htmlentities($_POST['pm100']);
	//$aqi=htmlentities($_POST['aqi']);
	//$ir=htmlentities($_POST['ir']);
	$light=htmlentities($_POST['light']);

	*/

//	$Location = $_GET['location'];

foreach($_REQUEST as $key => $value)
{
	if($key == 'location') {
		$Location = $value;
	}

	if($key == 'temperature') {
		$temperature = $value;
	}

	if($key == 'humidity') {
		$humidity = $value;
	}

	if($key == 'co2') {
		$co2 = $value;
	}

	if($key == 'pm25') {
		$pm25 = $value;
	}

	if($key == 'pm10') {
		$pm10 = $value;
	}

	if($key == 'pm100') {
		$pm100 = $value;
	}

	if($key == 'light') {
		$light = $value;
	}


}






	// These lines setup parameters for connecting to the MySQL database.
	$username = "nick_sms";
	$password = "sql2019";
	// You may not know this, but every MySQL table is stored in what's called a 'database'.
	// Databases are essentially folders, that contain tables.
	// Databases are used to store tables of similar properties, and are useful for organization of extremely large datasets.
	// These two lines below specify which database to look in, and which table in that database is to be accessed.
	$database = "Environmental01";
	$tablename = "Test01";
	//Provies the IP address of the server. This can also be a URL.
	$localhost = "localhost";
	//$link isn't actually a variable, but rather a function. Declaration like this is pretty rare.
	//It invokes the mysqli_connect command, which connects to the database being accessed.
	//It returns a boolean depending on whether or not it was able to establish a connection to the server.
	$link = mysqli_connect($localhost, $username, $password, $database);
	//Tests if link if 'true' or 'false'.
	//Statement runs if bool is 'true'
	if ($link)
		{
			//Selects the database being accessed
			@mysqli_select_db($link, $database) or die ("Unable to select database");
			echo 'connected';
			// The following line is the actual SQL command being queryed to the server. Google information on SQL queries if you have questions, or ask me.
			$query = "INSERT INTO $tablename (Location, Temperature, Humidity, CO2, PM100, PM25, PM10, Lux, Time) VALUES ('$Location', $temperature, $humidity, $co2, $pm100, $pm25, $pm10, $light, now());";
			//http://localhost/bdst/bdst_insertoffline.php?location=nick&temperature=22&humidity=55
			//Sends the query/command to the server using mysqli_query, while also logging whether or not the query was successful through $result.
			$result = mysqli_query($link,$query);
			echo $query;
			//Outputs in console if entry failed
			if(!$result) {
				echo "data entry failed";
			}
		} else {
			echo('Unable to connect to database.');
		}
?>
