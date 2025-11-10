    <?php
        require_once "includes/login/menu.php";
        require_once "includes/login/header.php";
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

                            <form action="login.php" class="m-t" role="form">
                                <div class="form-group">
                                    <input class="form-control" id="name" name="name"
                                           placeholder="Name" required="" type="text">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" id="surname" name="surname"
                                           placeholder="Surname" required="" type="text">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" id="email" name="email"
                                           placeholder="Email" required="" type="email">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" id="password" name="password"
                                           placeholder="Password" required="" type="password">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" id="confirm_password" name="confirm_passoword"
                                           placeholder="Confirm Password" required="" type="password">
                                </div>
                                <button class="btn btn-primary block m-b" type="submit"><i class="fa fa-save"></i> Save</button>
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