<?php
require_once "connect.php";
require_once "functions.php";
error_reporting(0);

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

    if ($results["role"] == "admin") {
        $location = "users.php";
    }

    http_response_code(200);
    $response = array("message" => "User logged in successfully", "location" => $location);
    echo json_encode($response);
    exit;
} else if ($_POST["action"] == "register") {
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
    $valid_date = date('Y-m-d H:i:s', strtotime(' +5 minutes '));

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
    $response = array("message" => "User registered successfully", "location" => "login.php");
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
    $response = array("message" => "User registered successfully", "location" => "login.php");
    echo json_encode($response);
    exit;
} else if ($_POST["action"] == "fillModalData") {
    session_start();
    $id = mysqli_real_escape_string($conn, $_POST["id"]);

    // check if the user is admin.
    if ($_SESSION['role'] != "admin") {
        http_response_code(202);
        $response = array("message" => "You do not have permission to access this end point");
        echo json_encode($response);
        exit;
    }

    // check if the user exists
    /**
     * Check if there is a user with that email in DB
     */
    $query_check = "
                SELECT name,
                       surname,
                       email
                FROM users
                WHERE id = '" . $id . "';";

    $result_check = mysqli_query($conn, $query_check);
    $row = mysqli_fetch_assoc($result_check);
    if (!$result_check) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    // if there is a user with that email
    if (mysqli_num_rows($result_check) == 0) {
        http_response_code(201);
        $response = array("message" => "There is no user in our system with that ID");
        echo json_encode($response);
        exit;
    }


    // if the user exists send the data
    $data = array();
    $data['name'] = $row['name'];
    $data['surname'] = $row['surname'];
    $data['email'] = $row['email'];

    http_response_code(200);
    $response = array("message" => "Data fetched successfully", "data" => $data);
    echo json_encode($response);
    exit;

} else if ($_POST["action"] == "update_user_data") {
    session_start();
    // check if the user is admin.
    if ($_SESSION['role'] != "admin") {
        http_response_code(202);
        $response = array("message" => "You do not have permission to access this end point");
        echo json_encode($response);
        exit;
    }

    //1. get data
    $id = mysqli_real_escape_string($conn, $_POST["id"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $surname = mysqli_real_escape_string($conn, $_POST["surname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
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

    // Validimi i email
    if (!preg_match($email_regex, $email)) {
        http_response_code(201);
        $response = array("message" => "E-Mail format is not allowed");
        echo json_encode($response);
        exit;
    }

    /**
     * Check if there is a user with that id exists in DB
     */
    $query_check = "SELECT id, email, email_verified
                    FROM users
                    WHERE id = '" . $id . "';";

    $result_check = mysqli_query($conn, $query_check);

    if (!$result_check) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    // if there is a user with that email
    if (mysqli_num_rows($result_check) == 0) {
        http_response_code(201);
        $response = array("message" => "There is no user in our system with that ID");
        echo json_encode($response);
        exit;
    }

    $row_check = mysqli_fetch_assoc($result_check);
    $email_verified = $row_check['email_verified'];
    if ($email != $row_check['email']) {
        $email_verified = 'no';
    }


    /**
     * Check if there is a user with that email in DB
     */
    $query_check = "SELECT id 
                    FROM users
                    WHERE email = '" . $email . "' AND id != '" . $id . "';";

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
     * Update data to database
     */
    $query_update = "UPDATE users SET
                     name = '" . $name . "',
                     surname = '" . $surname . "',
                     email = '" . $email . "',
                     email_verified = '" . $email_verified . "',
                     updated_at = '" . date("Y-m-d H:i:s") . "'
                      WHERE id = '" . $id . "';";

    $result_update = mysqli_query($conn, $query_update);
    if (!$result_update) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    http_response_code(200);
    $response = array("message" => "User updated successfully");
    echo json_encode($response);
    exit;
} else if ($_POST["action"] == "delete_user") {
    session_start();
    $id = mysqli_real_escape_string($conn, $_POST["id"]);

    // check if the user is admin.
    if ($_SESSION['role'] != "admin") {
        http_response_code(202);
        $response = array("message" => "You do not have permission to access this end point");
        echo json_encode($response);
        exit;
    }

    /**
     * Check if there is a user with that email in DB
     */
    $query_check = "SELECT id 
                    FROM users
                    WHERE id = '" . $id . "';";
    $result_check = mysqli_query($conn, $query_check);

    if (!$result_check) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    if (mysqli_num_rows($result_check) < 1) {
        http_response_code(202);
        $response = array("message" => "There is not a user in DB");
        echo json_encode($response);
        exit;
    }

    // the user exists so delete the user
    $query_delete = "DELETE FROM
                     users
                     WHERE id = '" . $id . "';";

    $result_delete = mysqli_query($conn, $query_delete);

    if (!$result_delete) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    http_response_code(200);
    $response = array("message" => "User deleted successfully");
    echo json_encode($response);
    exit;
} else if ($_POST["action"] == "add_user") {
    //1. get data
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $surname = mysqli_real_escape_string($conn, $_POST["surname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = "12345678";
    $email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $alpha_regex = "/^[a-zA-Z]{3,40}$/";
    $email_code = rand(10000, 99999);
    $email_token = password_hash($email_code, PASSWORD_BCRYPT);
    $valid_date = date('Y-m-d H:i:s', strtotime(' +5 minutes '));

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

    // Validimi i EMAIL
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
    $response = array("message" => "User registered successfully");
    echo json_encode($response);
    exit;
} else if ($_POST["action"] == "add_role") {
    //1. get data
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
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
    /**
     * Check if there is a role with that name in DB
     */
    $query_check = "SELECT id 
                    FROM roles
                    WHERE name = '" . $name . "';";

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
        $response = array("message" => "There is already a role with that name on DB");
        echo json_encode($response);
        exit;
    }

    /**
     * Add data to database
     */
    $query_insert = "INSERT INTO roles SET
                     name = '" . $name . "',
                     created_at = '" . date("Y-m-d H:i:s") . "' ";

    $result_insert = mysqli_query($conn, $query_insert);
    if (!$result_insert) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    http_response_code(200);
    $response = array("message" => "Role saved successfully");
    echo json_encode($response);
    exit;
} else if($_POST["action"] == "fillRoleModalData") {
    session_start();
    $id = mysqli_real_escape_string($conn, $_POST["id"]);

    // check if the user is admin.
    if ($_SESSION['role'] != "admin") {
        http_response_code(202);
        $response = array("message" => "You do not have permission to access this end point");
        echo json_encode($response);
        exit;
    }

    // check if the role exists
    /**
     * Check if there is a role with that email in DB
     */
    $query_check = " SELECT name
                     FROM roles
                     WHERE id = '" . $id . "';";

    $result_check = mysqli_query($conn, $query_check);
    $row = mysqli_fetch_assoc($result_check);
    if (!$result_check) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    // if there is a user with that email
    if (mysqli_num_rows($result_check) == 0) {
        http_response_code(201);
        $response = array("message" => "There is no role in system");
        echo json_encode($response);
        exit;
    }


    // if the user exists send the data
    $data = array();
    $data['name'] = $row['name'];

    http_response_code(200);
    $response = array("message" => "Data fetched successfully", "data" => $data);
    echo json_encode($response);
    exit;
} else if ($_POST["action"] == "update_role_data") {
    session_start();
    // check if the user is admin.
    if ($_SESSION['role'] != "admin") {
        http_response_code(202);
        $response = array("message" => "You do not have permission to access this end point");
        echo json_encode($response);
        exit;
    }

    //1. get data
    $id = mysqli_real_escape_string($conn, $_POST["id"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
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

    /**
     * Check if there is a role with that id in DB
     */
    $query_check = "SELECT id, name
                    FROM roles
                    WHERE id = '" . $id . "';";

    $result_check = mysqli_query($conn, $query_check);

    if (!$result_check) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    // if there is a user with that email
    if (mysqli_num_rows($result_check) == 0) {
        http_response_code(201);
        $response = array("message" => "There is no role in our system with that ID");
        echo json_encode($response);
        exit;
    }

    $row_check = mysqli_fetch_assoc($result_check);


    /**
     * Check if there is a role with that name in DB
     */
    $query_check = "SELECT id 
                    FROM roles
                    WHERE name = '" . $name . "' AND id != '" . $id . "';";

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
        $response = array("message" => "There is a role with that NAME");
        echo json_encode($response);
        exit;
    }

    /**
     * Update data to database
     */
    $query_update = "UPDATE roles SET
                     name = '" . $name . "',
                     updated_at = '" . date("Y-m-d H:i:s") . "'
                     WHERE id = '" . $id . "';";

    $result_update = mysqli_query($conn, $query_update);
    if (!$result_update) {
        http_response_code(202);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    http_response_code(200);
    $response = array("message" => "Role updated successfully");
    echo json_encode($response);
    exit;
}