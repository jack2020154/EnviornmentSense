<?php
session_start();
include "../../assets/.includes/dbhconnect.php";
//http://localhost/bdst_april/hardware/getdata/getCO2A.php?esp=22
$esp_id = mysqli_real_escape_string($connection, $_GET[ 'esp' ] );

$sql = "SELECT co2a FROM bdst_esp_info WHERE esp_ID = '$esp_id'";

						$result = mysqli_query( $connection, $sql );
						$resultCheck = mysqli_num_rows( $result );
						if ( $resultCheck > 0 ) {
							while ( $row = mysqli_fetch_assoc( $result ) ) {
                                echo $row[ "co2a" ];
                            }
                        }
?>
