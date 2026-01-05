<?php
require_once "includes/login/header.php";
require_once "includes/login/menu.php";

if ($_SESSION['role'] != "admin"){
    header("Location: profile.php");
}

$query_roles = "SELECT 
                id, 
                name,
                created_at
                FROM roles";


$result_roles = mysqli_query($conn, $query_roles);
if (!$result_roles){
    echo mysqli_error($conn);
    exit;
}

$data = array();
while ($row = mysqli_fetch_assoc($result_roles)){
    $data[$row['id']]['id'] = $row['id'];
    $data[$row['id']]['name'] = $row['name'];
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
                <h2>Role List</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a>E-commerce</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <strong>Role List</strong>
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
                        <div class="ibox-title" style="padding: 30px;">
                            <div class="ibox-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addRoleModal"
                                    <i class="fa fa-plus"></i> Add Role
                                </button>
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover role-list-table" >
                                    <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Name</th>
                                        <th>Created At</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $id => $values){ ?>
                                            <tr id = "row_<?=$id?>">
                                                <td>
                                                    <nobr>
                                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#roleModal"
                                                        onclick = "fillModalData('<?=$id?>')">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger"
                                                        onclick = "deleteRole('<?=$id?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </nobr>
                                                </td>
                                                <td id = "name_<?=$id?>"><?=$values['name']?></td>
                                                <td id = "created_at_<?=$id?>"><?=$values['created_at']?></td>
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


<div class="modal inmodal" id="roleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated fadeInDown">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <i class="fa fa-edit modal-icon"></i>
                <h4 class="modal-title">Edit Role's data</h4>
                <small class="font-bold">Below you can modify role data.</small>
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
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary"
                        onclick="saveRoleData()">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal inmodal" id="addRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated fadeInDown">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <i class="fa fa-user modal-icon"></i>
                <h4 class="modal-title">Add new Role</h4>
            </div>
            <div class="modal-body">
                <form class="m-t" role="form">
                    <input type="hidden" id = "id" name="id" value="2">

                    <div class="form-group mb-4">
                        <input type="text" class="form-control" placeholder="Name"
                               name="name" id="name" >
                        <span id = "name_message" class="pull-left text-danger"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary"
                        onclick="addRole()">
                    <i class="fa fa-save"></i> Add Role
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
        $('.role-list-table').DataTable({
            pageLength: 10,
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
        data.append("action", "fillRoleModalData");
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

                    if (call.status == 200) {
                       // fill form with the information fetched
                        $("#name_modal").val(response.data.name);
                    } else {
                        toastr["warning"](response.message, "Warning");
                        // empty the modal form
                        $("#name_modal").val("");
                    }
                },
            })
        }


        function saveRoleData(){

            var id = $("#id_modal").val();
            var name = $("#name_modal").val();
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

            // prepare the data to send to backend
            var data = new FormData();
            data.append("action", "update_role_data");
            data.append("id", id);
            data.append("name", name);

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
                            // setTimeout(function (){
                                // window.location.reload();
                              $("name_"+id).text(name);
                              $('#roleModal').modal('toggle');
                              $("#row_"+id).addClass("animated flash")
                            // },2000);
                        } else {
                            toastr["warning"](response.message, "Warning");
                        }
                    },
                })
            }
        }

        function deleteRole(id){
            // prepare the data to send to backend
            var data = new FormData();
            data.append("action", "delete_role");
            data.append("id", id);

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
                        toastr["success"](response.message, "Success")
                        $("#row_"+id).addClass("animated fadeOutLeftBig");
                        setTimeout(function (){
                            $("#row_"+id).remove();
                        },500);
                    } else {
                        toastr["warning"](response.message, "Warning");
                    }
                },
            })

        }

    function addRole(){

        var name = $("#name").val();
        var alpha_regex = /^[a-zA-Z]{3,40}$/;
        var error = 0;

        // validimi i emrit
        if (!alpha_regex.test(name)){
            $("#name").addClass("border-danger");
            $("#name_message").text("Name must be aphabumeric at least 3 letters.");
            error++;
        } else {
            $("#name").removeClass("border-danger")
            $("#name_message").text("");
        }

        // prepare the data to send to backend
        var data = new FormData();
        data.append("action", "add_role");
        data.append("name", name);

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
                    toastr["success"](response.message, "Success")
                    $('#addRoleModal').modal('toggle');
                    if (call.status == 200) {
                        setTimeout(function (){
                            window.location.reload();
                        },2000);
                    } else {
                        toastr["warning"](response.message, "Warning");
                    }
                },
            })
        }
    }




</script>
