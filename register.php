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
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Name"
                       name="name" id="name" required="">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Surname"
                       name="surname" id="surname" required="">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" placeholder="Email"
                       name="email" id="email" required="">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Password"
                       name="password" id="password" required="">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Confirm Password"
                       name="confirm_passoword" id="confirm_password" required="">
            </div>
            <div class="form-group">
                <!--                    TODO : ERMS AND POLICIES WITH A POP UP. THE USER NEED TO READ THEM.-->
                <div class="checkbox i-checks"><label> <input type="checkbox"><i></i> Agree the terms and policy </label></div>
            </div>
            <button type="submit" class="btn btn-primary block full-width m-b">Register</button>

            <p class="text-muted text-center"><small>Already have an account?</small></p>
            <a class="btn btn-sm btn-white btn-block" href="login.php">Login</a>
        </form>
        <p class="m-t"> <small>FTI STUDENTS &copy; 2025-2026</small> </p>
    </div>
</div>


<?php
require_once "includes/no_login/footer.php";
?>