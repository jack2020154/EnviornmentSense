<?php
  require_once( '/var/www/php/dbh.inc.php' );
  $error = false;

  $start_date = 0;
  $end_date = 0;

  if (isset($_POST['start_date']) & !empty(isset($_POST['start_date'])))
  {
    // have dates in iso 8601
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $start_date = intval($start_date); // unix timestamps
  }

  if (isset($_POST['end_date']) & !empty(isset($_POST['end_date'])))
  {
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $end_date = intval($end_date); // unix timestamps
  }

  if (isset($_POST['resolution']) & !empty(isset($_POST['resolution'])))
  {
    $resolution = mysqli_real_escape_string($conn, $_POST['resolution']);
    $resolution = intval($resolution);
  }
  else {
    $resolution = 1;
  }

  if (!empty($start_date) and !empty($end_date)) {
    // select * from `Data`
    // where Date between '2012-03-11 00:00:00' and '2012-05-11 23:59:00'
    // order by Date desc;

    $start = date('Y-m-d H:i:s', $start_date);
    $end = date('Y-m-d H:i:s', $end_date);

    $query = "SELECT * FROM `Data` WHERE `Date` BETWEEN '$start' AND '$end' ORDER BY `Date` ASC;";
    $queryresult = mysqli_query($conn, $query);

    $diff = $end_date - $start_date; // difference in seconds

    if ($resolution > 0) {
      $step = intval($diff / ($resolution*60*60) + 0.5); // im retarded
      //echo $step . "\n";
      //echo $diff . "\n";
      //echo $resolution . "\n";
    }
    else {
      $step = 1; // step is in seconds
    }

    // columns: `Date`, `AQI`, `Conc`,`Qtot`,`Temp`,`Errors`
    if (mysqli_num_rows($queryresult) > 0) {
      if ($diff < $resolution*60*60) {
        // no averaging
        $data = mysqli_fetch_all($queryresult, MYSQLI_ASSOC);
        foreach($data as $row) {
          $value = $row["AQI"];
          if ($row["Errors"] == "Tape Error") {
            $value = "Tape Error";
          }
          echo $row["Date"] . " " . $value . "\n";
        }
      }
      else {
        // with averaging
        // output data of each row
        $data = mysqli_fetch_all($queryresult, MYSQLI_ASSOC);

        $numOfValues = 0;
        $sumAcrossRange = 0;
        $lastTime = $start_date;
        $lastDate = "";
        foreach($data as $row) {
          if (strtotime($row["Date"]) >= ($step*60*60 + $lastTime)) {
            echo $lastDate . " " . intval($sumAcrossRange / $numOfValues) . "\n";
            $numOfValues = 0;
            if ($row["Errors"] != "Tape Error") {
              $sumAcrossRange = $row["AQI"];
              $numOfValues += 1;
            }
            else {
              $sumAcrossRange = 0;
            }
            $lastTime += $step*60*60;
          }
          else {
            if ($row["Errors"] != "Tape Error") {
              $sumAcrossRange += $row["AQI"];
              $numOfValues += 1;
            }
          }
          $lastDate = $row["Date"];
        }
      }
    }
  }


  ?>
