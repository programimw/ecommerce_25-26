<?php
require_once "includes/login/header.php";
require_once "includes/login/menu.php";

if ($_SESSION['role'] != "admin"){
    header("Location: profile.php");
}

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

$data = array();
while ($row = mysqli_fetch_assoc($result_users)){
    $data[$row['id']]['id'] = $row['id'];
    $data[$row['id']]['name'] = $row['name'];
    $data[$row['id']]['surname'] = $row['surname'];
    $data[$row['id']]['email'] = $row['email'];
    $data[$row['id']]['role'] = $row['role'];
    $data[$row['id']]['email_verified'] = $row['email_verified'];
    $data[$row['id']]['created_at'] = $row['created_at'];
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
                                        <th>E-Mail verified</th>
                                        <th>User Registered at</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $id => $values){ ?>
                                            <tr>
                                                <td>
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"
                                                    onclick = "fillModalData('<?=$id?>')">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </button>
                                                </td>
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


<div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated fadeInDown">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <i class="fa fa-edit modal-icon"></i>
                <h4 class="modal-title">Edit User's data</h4>
                <small class="font-bold">Below you can modify users data.</small>
            </div>
            <div class="modal-body">
                <form action="#" class="m-t" role="form">
                    <input type="hidden" id="id_modal" value="">
                    <div class="form-group">
                        <input class="form-control" id="name_modal" name="name"
                               placeholder="Name" required="" type="text"
                               value="">
                        <span id = "name_message" class="pull-left text-danger"></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control" id="surname_modal" name="surname"
                               placeholder="Surname" required="" type="text"
                               value="">
                        <span id = "surname_message" class="pull-left text-danger"></span>
                    </div>
                    <div class="form-group">
                        <input class="form-control" id="email_modal" name="email"
                               placeholder="Email" required="" type="email"
                               value="">
                        <span id = "email_message" class="pull-left text-danger"></span>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary"
                        onclick="saveUserData()">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>



<?php
require_once "includes/login/footer.php";
?>

<!-- Page-Level Scripts -->
<script>
    toastr.options = {
        "closeButton": true,
        "debug": true,
        "progressBar": true,
        "preventDuplicates": true,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "7000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }



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


    function fillModalData(id){
        // prepare the data to send to backend
        var data = new FormData();
        data.append("action", "fillModalData");
        data.append("id", id);
        $("#id_modal").val(id);

        // send data on backed

            $.ajax({
                type: "POST",
                url: "ajax.php",
                // dataType: 'json',
                async: false,
                cache: false,
                processData: false,
                data: data,
                contentType: false,
                success: function (response, status, call) {
                    response = JSON.parse(response);
                    console.log(response);
                    if (call.status == 200) {
                       // fill form with the information fetched
                        $("#name_modal").val(response.data.name);
                        $("#surname_modal").val(response.data.surname);
                        $("#email_modal").val(response.data.email);
                    } else {
                        toastr["warning"](response.message, "Warning");
                        // empty the modal form
                        $("#name_modal").val("");
                        $("#surname_modal").val("");
                        $("#email_modal").val("");
                    }
                },
            })
        }


        function saveUserData(){

            var id = $("#id_modal").val();
            var name = $("#name_modal").val();
            var surname = $("#surname_modal").val();
            var email = $("#email_modal").val();
            var email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            var alpha_regex = /^[a-zA-Z]{3,40}$/;
            var error = 0;

            // validimi i emrit
            if (!alpha_regex.test(name)){
                $("#name_modal").addClass("border-danger");
                $("#name_message").text("Name must be aphabumeric at least 3 letters.");
                error++;
            } else {
                $("#name_modal").removeClass("border-danger")
                $("#name_message").text("");
            }

            // validimi i mbiemrit
            if (!alpha_regex.test(surname)){
                $("#surname_modal").addClass("border-danger");
                $("#surname_message").text("Surname must be aphabumeric at least 3 letters.");
                error++;
            } else {
                $("#surname_modal").removeClass("border-danger")
                $("#surname_message").text("");
            }

            // Validation of the E-Mail
            if (!email_regex.test(email)){
                $("#email_modal").addClass("border-danger");
                $("#email_message").text("E-Mail format is not allowed");
                error++;
            } else {
                $("#email_modal").removeClass("border-danger")
                $("#email_message").text("");
            }
            // prepare the data to send to backend
            var data = new FormData();
            data.append("action", "update_user_data");
            data.append("id", id);
            data.append("name", name);
            data.append("surname", surname);
            data.append("email", email);

            // send data on backed
            if (error == 0) {
                $.ajax({
                    type: "POST",
                    url: "ajax.php",
                    // dataType: 'json',
                    async: false,
                    cache: false,
                    processData: false,
                    data: data,
                    contentType: false,
                    success: function (response, status, call) {
                        response = JSON.parse(response);
                        if (call.status == 200) {
                            toastr["success"](response.message, "Success")
                            setTimeout(function (){
                                window.location.reload();
                                //TODO update table directly and close the modal
                            },2000);
                        } else {
                            toastr["warning"](response.message, "Warning");
                        }
                    },
                })
            }
        }

</script>
