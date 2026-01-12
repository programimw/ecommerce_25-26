<?php
session_start();
//error_reporting(0);
date_default_timezone_set('Europe/Rome');


$query_insert_log = "INSERT INTO logs
                            SET user_id = '".mysqli_real_escape_string($conn, $_SESSION['id'])."',
                                action = '".mysqli_real_escape_string($conn, $action)."',
                                data = '".mysqli_real_escape_string($conn, $data)."',
                                ip = '".mysqli_real_escape_string($conn, $ip)."',
                                created_at = '". date("Y-m-d H:i:s") ."'
                            ";
$result_stripe_logs_cards = mysqli_query($conn, $query_insert_log);