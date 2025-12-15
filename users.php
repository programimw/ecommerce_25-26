<?php
require_once "includes/login/header.php";
require_once "includes/login/menu.php";


$query_users = "SELECT 
                id, 
                name,
                surname,
                email,
                role,
                email_verified,
                created_at
                FROM users";


$result_users = mysqli_query($conn, $query_users);
if (!$result_users){
    echo mysqli_error($conn);
    exit;
}

$row_users = mysqli_fetch_assoc($result_users);

$data = array();
while ($row_users = mysqli_fetch_assoc($result_users)){
    $data[$row_users['id']]['id'] = $row_users['id'];
    $data[$row_users['id']]['name'] = $row_users['name'];
    $data[$row_users['id']]['surname'] = $row_users['surname'];
    $data[$row_users['id']]['email'] = $row_users['email'];
    $data[$row_users['id']]['role'] = $row_users['role'];
    $data[$row_users['id']]['email_verified'] = $row_users['email_verified'];
    $data[$row_users['id']]['created_at'] = $row_users['created_at'];
}

?>

    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <?php
            require_once "includes/login/top_menu.php";
            ?>
        </div>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>User List</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a>E-commerce</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <strong>User List</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>

        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover user-list-table" >
                                    <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Name</th>
                                        <th>Surname</th>
                                        <th>E-Mail</th>
                                        <th>Role</th>
                                        <th>E-Mail verified at</th>
                                        <th>User Registered at</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $id => $values){ ?>
                                            <tr>
                                                <td><button class="btn btn-primary"><i class="fa fa-edit"></i> Edit</button></td>
                                                <td><?=$values['name']?></td>
                                                <td><?=$values['surname']?></td>
                                                <td><?=$values['email']?></td>
                                                <td><?=$values['role']?></td>
                                                <td><?=$values['email_verified']?></td>
                                                <td><?=$values['created_at']?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        require_once "includes/login/copyright.php";
        ?>

    </div>
    </div>


<?php
require_once "includes/login/footer.php";
?>

<!-- Page-Level Scripts -->
<script>
    $(document).ready(function(){
        $('.user-list-table').DataTable({
            pageLength: 25,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                { extend: 'copy'},
                {extend: 'csv'},
                {extend: 'excel', title: 'ExampleFile'},
                {extend: 'pdf', title: 'ExampleFile'},

                {extend: 'print',
                    customize: function (win){
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ]
        });
    });

</script>
