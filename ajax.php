<?php


if ($_POST["action"] == "login"){

    //1. get data
    $email = $_POST["email"];
    $password = $_POST["password"];
    //2. validation of the data
    //3. verification, if there is a user with that email and password
    //4. response on front end and login the user. Redirect to profile
}
else if ($_POST["action"] == "register"){
    print_r($_POST);
    exit;
    //1. get data
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    //2. validation of the data
    //3. verification, if there is a user with that email
    //4. add the data to database
    //5. response on front end
}
