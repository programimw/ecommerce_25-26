<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "ecommerce";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    echo "Error: Unable to connect to MySQL.";
    echo "Debugging errno: " . mysqli_connect_errno();
    echo "Error: " . mysqli_connect_error();
}