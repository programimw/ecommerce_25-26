<?php
    require_once "includes/no_login/header.php";
?>

<div class="middle-box text-center loginscreen   animated fadeInDown">
    <div>
        <div>
            <h1 class="logo-name">EC</h1>
        </div>
        <h3>Register to Ecommerce</h3>
        <p>Create your account to buy our products.</p>
        <form class="m-t" role="form" action="login.php">
            <div class="form-group mb-4">
                <input type="text" class="form-control" placeholder="Name"
                       name="name" id="name" >
                <span id = "name_message" class="pull-left text-danger"></span>
            </div>
            <div class="form-group mb-4">
                <input type="text" class="form-control" placeholder="Surname"
                       name="surname" id="surname" >
                <span id = "surname_message" class="pull-left text-danger"></span>
            </div>
            <div class="form-group mb-4">
                <input type="email" class="form-control" placeholder="Email"
                       name="email" id="email" >
                <span id = "email_message" class="pull-left text-danger"></span>            </div>
            <div class="form-group mb-4">
                <input type="password" class="form-control" placeholder="Password"
                       name="password" id="password" >
                <span id = "password_message" class="pull-left text-danger"></span>
            </div>
            <div class="form-group mb-4">
                <input type="password" class="form-control" placeholder="Confirm Password"
                       id = "confirm_password" name="confirm_password" id="confirm_password" >
                <span id = "confirm_password_message" class="pull-left text-danger"></span>
            </div>
            <div class="form-group mb-4">
                <!--                    TODO : ERMS AND POLICIES WITH A POP UP. THE USER NEED TO READ THEM.-->
                <div class="checkbox i-checks"><label> <input type="checkbox"><i></i> Agree the terms and policy </label></div>
            </div>
            <button type="button" class="btn btn-primary block full-width m-b" onclick="register()">Register</button>
            <p class="text-muted text-center"><small>Already have an account?</small></p>
            <a class="btn btn-sm btn-white btn-block" href="login.php">Login</a>
        </form>
        <p class="m-t"> <small>FTI STUDENTS &copy; 2025-2026</small> </p>
    </div>
</div>


<?php
require_once "includes/no_login/footer.php";
?>


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

    function register(){

        var name = $("#name").val();
        var surname = $("#surname").val();
        var email = $("#email").val();
        var password = $("#password").val();
        var confirm_password = $("#confirm_password").val();
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

        // Validation of the Password
        if (isEmpty(password)){
            $("#password").addClass("border-danger");
            $("#password_message").text("Password can not be empty");
            error++;
        } else{
            $("#password").removeClass("border-danger")
            $("#password_message").text("");
        }

        // Validation of the Password
        if (confirm_password != password ){
            $("#confirm_password").addClass("border-danger");
            $("#confirm_password_message").text("Confirm password must be equal to password");
            error++;
        } else{
            $("#confirm_password").removeClass("border-danger")
            $("#confirm_password_message").text("");
        }

        // prepare the data to send to backend
        var data = new FormData();
        data.append("action", "register");
        data.append("name", name);
        data.append("surname", surname);
        data.append("email", email);
        data.append("password", password);
        data.append("confirm_password", confirm_password);

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

