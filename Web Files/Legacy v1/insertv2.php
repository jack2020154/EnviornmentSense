<?php
	// This block of code parses the POST request being sent to it (the PHP page)
	// It does this by taking the POST request and looking for keywords in it.
	// Lets use the first line as an example, where location data is being sent.
	// If the PHP file sees the keyword 'location' in the POST request, it assigns it the value that follows it.
	// Remember how our POST request looked like this: "location=xxx&temperature=xxx"
	// The ampersand denotes when one value has ended and another field begins
	// This block of code is the most complicated, so feel free to ask me questions
	$location=htmlentities($_POST['location']);
	$temperature=htmlentities($_POST['temperature']);
	$humidity=htmlentities($_POST['heatIndex']);
	$co2=htmlentities($_POST['co2']);
	$pm100=htmlentities($_POST['pm100']);
	$pm25=htmlentities($_POST['pm25']);
	$pm10=htmlentities($_POST['pm10']);
	$aqi=htmlentities($_POST['aqi']);
	$ir=htmlentities($_POST['ir']);
	$light=htmlentities($_POST['light']);
	// These lines setup parameters for connecting to the MySQL database.
	$username = "esp8266";
	$password = "82ndjbkb8!";
	// You may not know this, but every MySQL table is stored in what's called a 'database'.
	// Databases are essentially folders, that contain tables.
	// Databases are used to store tables of similar properties, and are useful for organization of extremely large datasets.
	// These two lines below specify which database to look in, and which table in that database is to be accessed.
	$database = "sensor_db";
	$tablename = "sensor_data";
	//Provies the IP address of the server. This can also be a URL.
	$localhost = "127.0.0.1";
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
			// The following line is the actual SQL command being queryed to the server. Google information on SQL queries if you have questions, or ask me.
			$query = "INSERT INTO $tablename (Location, Temperature, HeatIndex, CO2, PM100, PM25, PM10, AQI, Infrared, Light, timestamp) VALUES ('$location', $temperature, $humidity, $co2, $pm100, $pm25, $pm10, $aqi, $ir, $light, now());";
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