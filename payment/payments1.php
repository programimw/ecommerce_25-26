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

// printimi i vektoreve
function printArray($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

function isWeekend($date) {
    return (date('N', strtotime($date)) >= 6);
}

$query_off_days = "SELECT date 
                   FROM off_days";

$result_off_days = mysqli_query($conn, $query_off_days);

if (!$result_off_days) {
    echo "Error: Unable to run query " . $query_off_days . ".";
    exit;
}

$off_days = array();
while ($row = mysqli_fetch_assoc($result_off_days)) {
    $off_days[$row["date"]] = $row["date"];
}


$query_data = "select users.id, full_name, total_paga, date, hours
               from working_days
               left join users on working_days.user_id = users.id;";

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
    $data[$row["id"]]['hourly_payment'] = round($row["total_paga"] / 160, 2);
    $hourly_payment = round($row["total_paga"] / 160, 2);

    $hours_in = $row["hours"];
    $hours_out = 0;
    if ($row["hours"] > 8) {
        $hours_in = 8;
        $hours_out = $row["hours"] - 8;
    }

    // Llogaritja e oreve per user
    $data[$row["id"]]['hours_in'] += $hours_in;
    $data[$row["id"]]['hours_out'] += $hours_out;
    $data[$row["id"]]['total_hours'] += $row["hours"];

    // Llogaritja e oreve per user ne cdo date
    $data[$row["id"]]["details"][$row['date']]['hours_in'] += $hours_in;
    $data[$row["id"]]["details"][$row['date']]['hours_out'] += $hours_out;
    $data[$row["id"]]["details"][$row['date']]['total_hours'] += $row["hours"];

    // Llogaritja e pages
    if (isset($off_days[$row["date"]])) {
        $k_in = 1.5;
        $k_out = 2;
    } else if (isWeekend($row["date"])){
        $k_in = 1.25;
        $k_out = 1.5;
    } else {
        $k_in = 1;
        $k_out = 1.25;
    }

    // llogaritja e pagave
    $data[$row["id"]]['payment_in'] += $hours_in * $hourly_payment * $k_in ;
    $data[$row["id"]]['payment_out'] +=  $hours_out * $hourly_payment * $k_out;
    $data[$row["id"]]['total_payment'] += $hours_in * $hourly_payment * $k_in  + $hours_out * $hourly_payment * $k_out;
    // llogaritja e pagave per user ne cdo date
    $data[$row["id"]]["details"][$row['date']]['payment_in'] += $hours_in * $hourly_payment * $k_in ;
    $data[$row["id"]]["details"][$row['date']]['payment_out'] +=  $hours_out * $hourly_payment * $k_out;
    $data[$row["id"]]["details"][$row['date']]['total_payment'] += $hours_in * $hourly_payment * $k_in  + $hours_out * $hourly_payment * $k_out;

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
    <style>
        .hide-row{
            /*display: none;*/
        }
    </style>
</head>
<body>

<table border="1" style="font-size: 20px; margin 0 auto">
    <thead>
        <tr>
            <th>Action</th>
            <th>Full Name</th>
            <th>Hours In</th>
            <th>Hours Out</th>
            <th>Total Hours</th>
            <th>Payment in</th>
            <th>Payment out</th>
            <th>Total Payment</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $id => $values) {  ?>
            <tr>
                <td><center><span onclick="toogle()">+</span></center></td>
                <td><?=$values['full_name']?></td>
                <td><?=$values['hours_in']?></td>
                <td><?=$values['hours_out']?></td>
                <td><?=$values['total_hours']?></td>
                <td><?=$values['payment_in']?></td>
                <td><?=$values['payment_out']?></td>
                <td><?=$values['total_payment']?></td>
            </tr>

            <?php  foreach ($values['details'] as $date => $details) { ?>
                <tr class="hide-row">
                    <td colspan="2"><?= $date ?></td>
                    <td><?=$details['hours_in']?></td>
                    <td><?=$details['hours_out']?></td>
                    <td><?=$details['total_hours']?></td>
                    <td><?=$details['payment_in']?></td>
                    <td><?=$details['payment_out']?></td>
                    <td><?=$details['total_payment']?></td>
                </tr>
            <?php } ?>

        <?php }  ?>
    </tbody>


</table>
<script>
    function toogle() {
        // document.getElementsByClassName('hide-row');
    }
</script>
</body>
</html>
