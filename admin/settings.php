<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';
/**@var mysqli $conn */

$msg = "";

if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$admin_id = $_SESSION['admin_id'] ?? null;

$result   = mysqli_query($conn, "SELECT * FROM settings WHERE id=1 LIMIT 1");
$settings = mysqli_fetch_assoc($result);


$admin_result = mysqli_query($conn, "SELECT * FROM admin WHERE id='$admin_id' LIMIT 1");
$admin_data   = mysqli_fetch_assoc($admin_result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // ---------------- General Settings form ----------------
    if (isset($_POST['save-settings'])) {

        $store_name = mysqli_real_escape_string($conn, trim($_POST['store_name']));
        $email      = mysqli_real_escape_string($conn, trim($_POST['email']));
        $phone      = mysqli_real_escape_string($conn, trim($_POST['phone']));

        $sql = "UPDATE settings SET store_name='$store_name', email='$email', phone='$phone' WHERE id=1";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['msg'] = "<div class='alert alert-success w-25 mb-0'>Settings Updated </div>";
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
        header("Location: settings.php");
        exit();
    }

    // ---------------- Change Password + Email (combined form) ----------------
    if (isset($_POST['update_account'])) {

        if (!$admin_id) {
            $_SESSION['msg'] = "<div class='alert alert-danger'>Session expired, please login again.</div>";
            header("Location: login.php");
            exit();
        }

        $current_password = $_POST['current_password'];
        $new_password      = trim($_POST['new_password']);
        $confirm_password  = trim($_POST['confirm_password']);
        $new_email         = mysqli_real_escape_string($conn, trim($_POST['new_email']));

        $check_result = mysqli_query($conn, "SELECT * FROM admin WHERE id='$admin_id' LIMIT 1");
        $current_admin = mysqli_fetch_assoc($check_result);

        if (!$current_admin || !password_verify($current_password, $current_admin['password'])) {
            $_SESSION['msg'] = "<div class='alert alert-danger'>Current password is incorrect.</div>";
            header("Location: settings.php");
            exit();
        }

        $sets = [];

        if ($new_password !== '') {
            if ($new_password !== $confirm_password) {
                $_SESSION['msg'] = "<div class='alert alert-danger'>New password and confirm password do not match.</div>";
                header("Location: settings.php");
                exit();
            }
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $sets[] = "`password`='$new_hashed'";
        }

        if ($new_email !== '') {
            if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['msg'] = "<div class='alert alert-danger'>Please enter a valid email.</div>";
                header("Location: settings.php");
                exit();
            }
            $email_check = mysqli_query($conn, "SELECT * FROM admin WHERE email='$new_email' AND id!='$admin_id' LIMIT 1");
            if (mysqli_num_rows($email_check) > 0) {
                $_SESSION['msg'] = "<div class='alert alert-danger'>This email is already used by another admin.</div>";
                header("Location: settings.php");
                exit();
            }
            $sets[] = "email='$new_email'";
        }

        if (empty($sets)) {
            $_SESSION['msg'] = "<div class='alert alert-danger'>Please fill new password or new email to update.</div>";
            header("Location: settings.php");
            exit();
        }

        $update = mysqli_query($conn, "UPDATE admin SET " . implode(', ', $sets) . " WHERE id='$admin_id'");

        if ($update) {
            $_SESSION['msg'] = "<div class='alert alert-success mb-0'>Account updated successfully.</div>";
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }

        header("Location: settings.php");
        exit();
    }
}
require 'includes/ad-header.php';
require_once 'includes/sidebar.php';
?>

<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted || (window.performance && window.performance.getEntriesByType("navigation")[0].type === "back_forward")) {
        window.location.reload();
    }
});
</script>

 <div class="container-fluid main-content">
<nav class="navbar navbar-expand-lg">
   <div class="container-fluid">
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">Settings</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center fs-5 text-dark" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user mx-2"><i class="fa-solid fa-user"></i></div> Admin
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="add-admin.php">Profile</a></li>
                    <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav><hr class="m-0">

<div class="d-flex align-items-center">
<a href="add-admin.php" class="btn border-dark ms-auto mt-3 fw-bold text-white" style="background-color:#0e0831;">
+ Add Admin
</a>
</div>
<?php echo $msg; ?>
<div class="row row-cols-1 row-cols-md-2 g-4 mt-2">

  <div class="col">
  <form method="POST" action="">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-bold ct">General Settings</h5>
        <div class="mb-3">
          <label for="store_name" class="form-label fw-bold">Store Name</label>
          <input type="text" class="form-control" name="store_name" value="<?php echo htmlspecialchars($settings['store_name'] ?? ''); ?>">
        </div>
        <div class="mb-3">
          <label for="email" class="form-label fw-bold">Email</label>
          <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>">
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label fw-bold">Phone</label>
          <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($settings['phone'] ?? ''); ?>">
        </div>
        <div class="col-12">
          <button type="submit" name="save-settings" class="btn up-btn">Save Changes</button>
        </div>
      </div>
    </div>
  </form>
  </div>

  <!-- Combined Change Password + Email form -->
  <div class="col">
  <form method="POST" action="">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-bold ct">Change Password / Email</h5>

        <div class="mb-3">
          <label for="current_password" class="form-label fw-bold">Current Password</label>
          <input type="password" class="form-control" name="current_password" value="" placeholder="Enter current password" required>
        </div>

        <div class="mb-3">
          <label for="new_password" class="form-label fw-bold">New Password</label>
          <input type="password" name="new_password" class="form-control" value="" placeholder="Enter new password">
        </div>

        <div class="mb-3">
          <label for="confirm_password" class="form-label fw-bold">Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control" value="" placeholder="Confirm new password">
        </div>

        <div class="mb-3">
          <label for="new_email" class="form-label fw-bold">New Email <small class="text-muted">(leave blank to keep same)</small></label>
          <input type="email" name="new_email" class="form-control" value="" placeholder="Enter new email">
        </div>

        <div class="col-12">
          <button type="submit" name="update_account" class="btn up-btn">Update</button>
        </div>
      </div>
    </div>
  </form>
  </div>

</div>

</div><!--main content-->

</body>
</html>