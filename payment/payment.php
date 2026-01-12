<?php
error_reporting(0);

$host = "localhost";
$user = "root";
$password = "";
$database = "test_paga";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    echo "Error: Unable to connect to MySQL.";
    echo "Debugging errno: " . mysqli_connect_errno();
    echo "Error: " . mysqli_connect_error();
}


function printArray($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function isWeekend($date) {
    return (date('N', strtotime($date)) >= 6);
}
// get off days
$query_off_days = "SELECT date 
                   FROM off_days;";

$result_off_days = mysqli_query($conn, $query_off_days);

if (!$result_off_days) {
    echo "Error: Unable to run query " . $query_off_days . ".";
    exit;
}

$off_days = array();
while ($row = mysqli_fetch_assoc($result_off_days)) {
    $off_days[$row["date"]] = $row["date"];
}

// get data
$query_data = "SELECT users.id,
                      full_name,
                      total_paga,
                      date,
                      hours
               FROM working_days left join users on 
               users.id = working_days.user_id
               WHERE hours>0 ";


$result_data = mysqli_query($conn, $query_data);

if (!$result_data) {
    echo "Error: Unable to run query " . $query_data . ".";
    exit;
}

$data = array();
while ($row = mysqli_fetch_assoc($result_data)) {

    $data[$row["id"]]['id'] = $row["id"];
    $data[$row["id"]]['full_name'] = $row["full_name"];
    $data[$row["id"]]['total_paga'] = $row["total_paga"];
    $data[$row["id"]]['hourly_payment'] = round($row["total_paga"]/160,2);
    $hourly_payment = round($row["total_paga"]/160,2);

    // ndarja e oreve ne in dhe oyut
    $hours_in = $row["hours"];
    $hours_out = 0;
    if ($row["hours"] > 8){
        $hours_in = 8;
        $hours_out = $row["hours"] - 8;
    }

    // shtimi i oreve ne grupimin per user
    $data[$row["id"]]['hours_in'] += $hours_in;
    $data[$row["id"]]['hours_out'] += $hours_out;
    $data[$row["id"]]['total_hours'] += $row["hours"];

    // Llogaritja per cdo date
    $data[$row["id"]]['details'][$row['date']]['hours_in'] += $hours_in;
    $data[$row["id"]]['details'][$row['date']]['hours_out'] += $hours_out;
    $data[$row["id"]]['details'][$row['date']]['total_hours'] += $row["hours"];


    if (isset($off_days[$row["date"]])){
        $k_in = 1.5;
        $k_out = 2;
    } else if (isWeekend($row["date"])){
        $k_in = 1.25;
        $k_out = 1.5;
    } else {
        $k_in = 1;
        $k_out = 1.25;
    }

    // llogaritja totale
    $data[$row["id"]]['hours_in_payment'] += $hourly_payment * $hours_in * $k_in;
    $data[$row["id"]]['hours_out_payment'] += $hourly_payment * $hours_out * $k_out;
    $data[$row["id"]]['total_payment'] += $hourly_payment * $hours_in * $k_in + $hourly_payment * $hours_out * $k_out;
    // llogaritja per cdo date
    $data[$row["id"]]['details'][$row['date']]['hours_in_payment'] += $hourly_payment * $hours_in * $k_in;
    $data[$row["id"]]['details'][$row['date']]['hours_out_payment'] += $hourly_payment * $hours_out * $k_out;
    $data[$row["id"]]['details'][$row['date']]['total_payment'] += $hourly_payment * $hours_in * $k_in + $hourly_payment * $hours_out * $k_out;

}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <table border="1" style="font-size: 20px; margin:0px auto; text-align: center;">
        <thead>
            <tr>
                <th>Action</th>
                <th>Full Name</th>
                <th>Hours In</th>
                <th>Hours Out</th>
                <th>Total Hours</th>
                <th>Hours In Payment</th>
                <th>Hours Out Payment</th>
                <th>Total Payment</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $id=> $values) { ?>
            <tr>
                <td>+</td>
                <td><?=$values['full_name']?></td>
                <td><?=$values['hours_in']?></td>
                <td><?=$values['hours_out']?></td>
                <td><?=$values['total_hours']?></td>
                <td><?=$values['hours_in_payment']?></td>
                <td><?=$values['hours_out_payment']?></td>
                <td><?=$values['total_payment']?></td>
            </tr>

            <?php foreach ($values['details'] as $date => $info) { ?>
                <tr>
                    <td colspan="2"><?=$date?></td>
                    <td><?=$info['hours_in']?></td>
                    <td><?=$info['hours_out']?></td>
                    <td><?=$info['total_hours']?></td>
                    <td><?=$info['hours_in_payment']?></td>
                    <td><?=$info['hours_out_payment']?></td>
                    <td><?=$info['total_payment']?></td>
                </tr>
            <?php } ?>


        <?php } ?>
        </tbody>
    </table>


</body>
</html>

