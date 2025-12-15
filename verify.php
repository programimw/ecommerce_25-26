<?php
echo "<h1>Verify the E-Mail</h1>";

require_once "connect.php";

$user_id = $_GET['id'];
$email_token = $_GET['token'];


$query_user ="SELECT 
             id,
             token_date
             FROM users 
             where id = ".$user_id."
             AND email_token = '".$email_token."'";

$result_user = mysqli_query($conn, $query_user);
if (!$result_user) {
    echo "Error: " . $query_user . "<br>" . mysqli_error($conn);
    exit;
}

$row_user = mysqli_fetch_assoc($result_user);


$valid_datetime = strtotime($row_user['token_date']);
$now = strtotime(date("Y-m-d H:i:s"));


if ($now < $valid_datetime) {
    $query_update = "
                       UPDATE users
                       set 
                           email_verified =  'yes',
                           email_verified_at = '".date("Y-m-d H:i:s")."'
                       WHERE id = '".$user_id."'";

    $result_update = mysqli_query($conn, $query_update);
    if (!$result_update) {
        echo "Error: " . $query_update . "<br>" . mysqli_error($conn);
        exit;
    }
    echo "<h1>E-Mail verified successfylly</h1>";
} else{
    echo "<h1>Token is not valid</h1>";
}
