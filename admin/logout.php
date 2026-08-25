<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// if admin already logged out / session doesnot exist.go to login
if (!isset($_SESSION['admin_id'])) {   //put actual session variable name
    header("Location: login.php");
    exit();
}

if (isset($_GET['confirm']) && $_GET['confirm'] == '1') {
    $_SESSION = [];
    session_destroy();
    header("Location: login.php");
    exit();
}
?>


<?php require 'includes/ad-header.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>
<div class="container-fluid main-content">
<nav class="navbar navbar-expand-lg">
   <div class="container-fluid">

        <a class="navbar-brand fw-bold text-dark fs-3" href="#">Logout</a>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center fs-5 text-dark"
                  href="#"
                   id="navbarDropdownMenuLink"
                   role="button"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
              <div class="user mx-2">  <i class="fa-solid fa-user"></i></div>   Admin
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="add-admin.php">Profile</a></li>
                    <li><a class="dropdown-item" href="setting.php">Settings</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>

    </div>
</nav><hr class="m-0">


<div class="card text-center mt-5">
  
  <div class="card-body">
    <i class="fa-solid fa-arrow-right-from-bracket signout"></i>
    <h3 class="card-title fw-bold mt-3 mb-3">Are you sure you want to logout?</h3>
    <p class="text-muted">You will be redirected to the login page</p>
   <div>
    <a href="logout.php?confirm=1" class="btn signout-btn">Yes,Logout</a>
     <a href="#" class="btn signout-btn">Cancel</a>
</div>
  </div>
 
</div>

</div><!--main-->
<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>