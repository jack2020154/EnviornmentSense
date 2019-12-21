<?php
session_start();
include "../../assets/.includes/dbhconnect.php";
$esp_id = mysqli_real_escape_string($connection, $_GET[ 'esp' ] );


$sql = "SELECT rebootStatus FROM bdst_esp_info WHERE esp_id = '$esp_id'";

						$result = mysqli_query( $connection, $sql );


						$resultCheck = mysqli_num_rows( $result );
						if ( $resultCheck > 0 ) {
							while ( $row = mysqli_fetch_assoc( $result ) ) {

                                echo $row[ "rebootStatus" ];
                                
                                

                            }

                        $rebootSql = "UPDATE bdst_esp_info SET rebootStatus = '0' WHERE esp_id = '$esp_id';";
                        $result2 = mysqli_query( $connection, $rebootSql );

                        }



?>