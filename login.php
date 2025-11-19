<?php
require_once "includes/no_login/header.php";
?>
<div class="loginColumns animated fadeInDown">
    <div class="row">
        <div class="col-md-6">
            <div class="ibox-content no-padding border-left-right">
                <!--TODO: FIND A BETTER PHOTO TO LOGIN-->
                <img alt="image" class="img-fluid" src="img/profile_big.jpg">
            </div>
        </div>
<!--        border-danger-->
<!--        text-danger-->
        <div class="col-md-6">
            <div class="ibox-content">
                <form class="m-t" method="post" action="profile.php">
                    <div class="form-group">
                        <input type="email" id ="email" class="form-control " placeholder="Username" >
                        <span id = "email_message" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <input type="password" id = "password" class="form-control " placeholder="Password" required="">
                        <span id = "password_message" class="text-danger"></span>
                    </div>
                    <button type="button" class="btn btn-primary block full-width m-b" onclick="login()">Login</button>

                    <a href="#">
                        <small>Forgot password?</small>
                    </a>

                    <p class="text-muted text-center">
                        <small>Do not have an account?</small>
                    </p>
                    <a class="btn btn-sm btn-white btn-block" href="register.php" >Create an account</a>
                </form>
                <p class="m-t">
                    <small>Inspinia we app framework base on Bootstrap 3 &copy; 2014</small>
                </p>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-6">
            Copyright Example Company
        </div>
        <div class="col-md-6 text-right">
            <small>© 2014-2015</small>
        </div>
    </div>
</div>

<?php
include "includes/no_login/footer.php";
?>


<script>
    function login(){
        var email = $("#email").val();
        var password = $("#password").val();
        var email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        var error = 0;

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
        } else{
            $("#password").removeClass("border-danger")
            $("#password_message").text("");
        }


        var data = new FormData();
        data.append("action", "login");
        data.append("email", email);
        data.append("password", password);


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
                    // response = JSON.parse(response);

                    // if (call.status == 200) {
                    //     window.location.href = response.location;
                    // } else {
                    //     $("#" + response.tagError).text(response.message);
                    //     $("#" + response.tagElement).addClass('error');
                    //     // Swal.fire('Error', response.message, 'error')
                    // }
                },
            })
        }
    }


</script>


