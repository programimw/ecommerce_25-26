<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Profile</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <!-- Toastr style -->
    <link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">
    <!-- Datatable -->
    <link href="css/plugins/dataTables/datatables.min.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>
<body>
<div id="wrapper">
<?php
require_once "connect.php";
error_reporting(0);
session_start();
/**
 * Check if the user is logged in
 */
if (!isset($_SESSION["id"]) && !isset($_SESSION["email"])) {
    $_SESSION = array();
    session_destroy();
    header("location:login.php");
}

// Example URL path
$urlPath = $_SERVER['REQUEST_URI'];

// If you want to remove the file extension as well
$filename = basename($urlPath);
$filenameArray = explode(".", $filename);

?>