    <?php
    require_once "includes/login/header.php";
    require_once "includes/login/menu.php";

    /**
     * Get User Data
     */
    require_once "connect.php";
    $id = mysqli_real_escape_string($conn, $_SESSION['id']);
    $query_user_data = "SELECT name,
                               surname,
                               email
                        FROM users WHERE id = '".$id."'";

    $result_user_data = mysqli_query($conn, $query_user_data);
    if (!$result_user_data) {
        echo "Error: " . $query_user_data . "<br>" . mysqli_error($conn);
        exit;
    }

    $row_user_data = mysqli_fetch_assoc($result_user_data);

    ?>

    <div class="gray-bg" id="page-wrapper">
        <div class="row border-bottom">
            <?php
            require_once "includes/login/top_menu.php";
            ?>
        </div>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Profile</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <strong>Profile</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>
        <div class="wrapper wrapper-content">
            <div class="row animated fadeInRight">
                <div class="col-md-4">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Profile Detail</h5>
                        </div>
                        <div>
                            <div class="ibox-content no-padding border-left-right">
                                <img alt="image" class="img-fluid" src="img/profile_big.jpg">
                            </div>
                            <div class="ibox-content profile-content">
                                <h4><strong>Monica Smith</strong></h4>
                                <p><i class="fa fa-map-marker"></i> Not Specified</p>

                                <div class="user-button">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <button class="btn btn-primary btn-sm btn-block" type="button">
                                                <i class="fa fa-save"></i> Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Personal Information</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <form action="#" class="m-t" role="form">
                                <div class="form-group">
                                    <input class="form-control" id="name" name="name"
                                           placeholder="Name" required="" type="text"
                                    value="<?=$row_user_data['name']?>">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" id="surname" name="surname"
                                           placeholder="Surname" required="" type="text"
                                           value="<?=$row_user_data['surname']?>">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" id="email" name="email"
                                           placeholder="Email" required="" type="email"
                                           value="<?=$row_user_data['email']?>">
                                </div>
                                <button class="btn btn-primary block m-b" type="button" onclick="update_user()"><i class="fa fa-save"></i> Save</button>
                            </form>
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

    <script type="text/javascript">

        function update_user(){

            var name = $("#name").val();
            var surname = $("#surname").val();
            var email = $("#email").val();
            var email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
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

            // validimi i mbiemrit
            if (!alpha_regex.test(surname)){
                $("#surname").addClass("border-danger");
                $("#surname_message").text("Surname must be aphabumeric at least 3 letters.");
                error++;
            } else {
                $("#surname").removeClass("border-danger")
                $("#surname_message").text("");
            }

            // Validation of the E-Mail
            if (!email_regex.test(email)){
                $("#email").addClass("border-danger");
                $("#email_message").text("E-Mail format is not allowed");
                error++;
            } else {
                $("#email").removeClass("border-danger")
                $("#email_message").text("");
            }



            // prepare the data to send to backend
            var data = new FormData();
            data.append("action", "update_user");
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
                                window.location.href = response.location
                            },2000);
                        } else {
                            toastr["warning"](response.message, "Warning");
                        }
                    },
                })
            }
        }
    </script>
