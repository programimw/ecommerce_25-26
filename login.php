<?php
require_once "includes/no_login/header.php";
?>
<div class="loginColumns animated fadeInDown">
    <div class="row">

        <div class="col-md-6">
            <div class="ibox-content no-padding border-left-right">
                <!--                    TODO: FIND A BETTER PHOTO TO LOGIN-->
                <img alt="image" class="img-fluid" src="img/profile_big.jpg">
            </div>
        </div>
        <div class="col-md-6">
            <div class="ibox-content">
                <form class="m-t" method="post" action="profile.php">
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="Username" required="">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Password" required="">
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">Login</button>

                    <a href="#">
                        <small>Forgot password?</small>
                    </a>

                    <p class="text-muted text-center">
                        <small>Do not have an account?</small>
                    </p>
                    <a class="btn btn-sm btn-white btn-block" href="register.php">Create an account</a>
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
require_once "includes/no_login/footer.php";
?>



