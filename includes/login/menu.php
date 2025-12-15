<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <img alt="image" class="rounded-circle" src="img/profile_small.jpg"/>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="block m-t-xs font-bold">David Williams <b class="caret"></b></span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="login.php">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    EC
                </div>
            </li>
            <li id="profile">
                <a href="profile.php"><i class="fa fa-user"></i>
                    <span class="nav-label">Profile</span>
                </a>
            </li>
            <li id="products">
                <a href="products.php"><i class="fa fa-shopping-cart"></i>
                    <span class="nav-label">Products</span>
                </a>
            </li>
            <?php if ($_SESSION['role'] == 'admin' ) { ?>
                <li id="users">
                    <a href="users.php"><i class="fa fa-users"></i>
                        <span class="nav-label">Users</span>
                    </a>
                </li>
            <?php }?>
        </ul>

    </div>
</nav>


