<?php
require_once "connect.php";
require_once "functions.php";

if ($_POST["action"] == "login") {

    //1. get data
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

    /**
     * Data Validation
     */
    // Validimi i emailit
    if (!preg_match($email_regex, $email)) {
        http_response_code(201);
        $response = array("message" => "E-Mail format is not allowed");
        echo json_encode($response);
        exit;
    }

    // TODO: Validate the password with Regex
    // validimi i passwordit
    if (empty($password)) {
        http_response_code(201);
        $response = array("message" => "Password can not be empty");
        echo json_encode($response);
        exit;
    }

    /**
     * Check if there is a user with that email in DB
     */
    $query_check = "
                SELECT id, 
                       email,
                       role,
                       password
                FROM users
                WHERE email = '" . $email . "';";

    $result_check = mysqli_query($conn, $query_check);
    $results = mysqli_fetch_assoc($result_check);
    if (!$result_check) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    // if there is a user with that email
    if (mysqli_num_rows($result_check) == 0) {
        http_response_code(201);
        $response = array("message" => "There is no user with that E-Mail");
        echo json_encode($response);
        exit;
    }

    // If there is a user with that email, check password
    if (!password_verify($password, $results["password"])) {
        http_response_code(201);
        $response = array("message" => "Incorrect Password");
        echo json_encode($response);
        exit;
    }
    // TODO: VERIFY USERS EMAIL. If email not verified, he can not log in.

    session_start();
    $_SESSION["id"] = $results["id"];
    $_SESSION["email"] = $results["email"];
    $_SESSION["role"] = $results["role"];
    $location = "profile.php";

    if ($results["role"] == "admin"){
        $location = "users.php";
    }

    http_response_code(200);
    $response = array("message" => "User logged in successfully",
        "location" => $location);
    echo json_encode($response);
    exit;
}
else if ($_POST["action"] == "register") {
    //1. get data
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $surname = mysqli_real_escape_string($conn, $_POST["surname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $confirm_password = mysqli_real_escape_string($conn, $_POST["confirm_password"]);
    $email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $alpha_regex = "/^[a-zA-Z]{3,40}$/";
    $email_code = rand(10000, 99999);
    $email_token = password_hash($email_code, PASSWORD_BCRYPT);
    $valid_date = date('Y-m-d H:i:s', strtotime(' +5 minutes '));;

    /**
     * Data Validation
     */
    // validimi i emrit
    if (!preg_match($alpha_regex, $name)) {

        http_response_code(201);
        $response = array("message" => "Name must be aphabumeric at least 3 letters.");
        echo json_encode($response);
        exit;
    }

    // Validimi i mbiemrit
    if (!preg_match($alpha_regex, $surname)) {
        http_response_code(201);
        $response = array("message" => "Surname must be aphabumeric at least 3 letters.");
        echo json_encode($response);
        exit;
    }

    // Validimi i mbiemrit
    if (!preg_match($email_regex, $email)) {
        http_response_code(201);
        $response = array("message" => "E-Mail format is not allowed");
        echo json_encode($response);
        exit;
    }
    // validimi i passwordit
    if (empty($password)) {
        http_response_code(201);
        $response = array("message" => "Password can not be empty");
        echo json_encode($response);
        exit;
    }

    // validimi i confirm password
    if ($password != $confirm_password) {
        http_response_code(201);
        $response = array("message" => "Confirm password must be equal to password");
        echo json_encode($response);
        exit;
    }

    /**
     * Check if there is a user with that email in DB
     */
    $query_check = "SELECT id 
                    FROM users
                    WHERE email = '" . $email . "';";

    $result_check = mysqli_query($conn, $query_check);

    if (!$result_check) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    // if there is a user with that email
    if (mysqli_num_rows($result_check) > 0) {
        http_response_code(201);
        $response = array("message" => "There is a user with that E-Mail");
        echo json_encode($response);
        exit;
    }

    /**
     * Add data to database
     */
    $query_insert = "INSERT INTO users SET
                     name = '" . $name . "',
                     surname = '" . $surname . "',
                     email = '" . $email . "',
                     email_code = '" . $email_code . "',
                     code_date = '" . $valid_date . "',
                     email_token = '" . $email_token . "',
                     token_date = '" . $valid_date . "',
                     password = '" . password_hash($password, PASSWORD_BCRYPT) . "',
                     created_at = '" . date("Y-m-d H:i:s") . "' ";

    $result_insert = mysqli_query($conn, $query_insert);
    if (!$result_insert) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    /**
     * Send E-Mail to user to verify his E-Mail address
     */
    $user_id = mysqli_insert_id($conn);
    $data['code'] = $email_code;
    $data['id'] = $user_id;
    $data['token'] = $email_token;
    $data['user_email'] = $email;
    sendEmail($data);

    http_response_code(200);
    $response = array("message" => "User registered successfully",
                       "location" => "login.php");
    echo json_encode($response);
    exit;
} else if ($_POST["action"] == "update_user") {
    //1. get data
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $surname = mysqli_real_escape_string($conn, $_POST["surname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $confirm_password = mysqli_real_escape_string($conn, $_POST["confirm_password"]);
    $email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $alpha_regex = "/^[a-zA-Z]{3,40}$/";

    /**
     * Data Validation
     */
    // validimi i emrit
    if (!preg_match($alpha_regex, $name)) {

        http_response_code(201);
        $response = array("message" => "Name must be aphabumeric at least 3 letters.");
        echo json_encode($response);
        exit;
    }

    // Validimi i mbiemrit
    if (!preg_match($alpha_regex, $surname)) {
        http_response_code(201);
        $response = array("message" => "Surname must be aphabumeric at least 3 letters.");
        echo json_encode($response);
        exit;
    }

    // Validimi i mbiemrit
    if (!preg_match($email_regex, $email)) {
        http_response_code(201);
        $response = array("message" => "E-Mail format is not allowed");
        echo json_encode($response);
        exit;
    }

    /**
     * Check if there is a user with that email in DB
     */
    $query_check = "SELECT id 
                    FROM users
                    WHERE email = '" . $email . "';";

    $result_check = mysqli_query($conn, $query_check);

    if (!$result_check) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    // if there is a user with that email
    if (mysqli_num_rows($result_check) > 0) {
        http_response_code(201);
        $response = array("message" => "There is a user with that E-Mail");
        echo json_encode($response);
        exit;
    }

    /**
     * Add data to database
     */
    $query_insert = "INSERT INTO users SET
                     name = '" . $name . "',
                     surname = '" . $surname . "',
                     email = '" . $email . "',
                     password = '" . password_hash($password, PASSWORD_BCRYPT) . "',
                     created_at = '" . date("Y-m-d H:i:s") . "' ";

    $result_insert = mysqli_query($conn, $query_insert);
    if (!$result_insert) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    // TODO: Send E-Mail to the user's email.
    // He need to verify his E-Mail to log in.

    http_response_code(200);
    $response = array("message" => "User registered successfully",
        "location" => "login.php");
    echo json_encode($response);
    exit;
}

