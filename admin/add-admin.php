<?php
session_start();

// Block direct access - must be logged in to view this page
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_admin'])) {

    $username         = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email            = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password         = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $_SESSION['msg'] = "<div class='alert alert-danger'>Password and Confirm Password do not match.</div>";
        header("Location: add-admin.php");
        exit();
    }

    
    $check = mysqli_query($conn, "SELECT * FROM admin WHERE Name='$username' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['msg'] = "<div class='alert alert-danger'>Already reserved username.</div>";
        header("Location: add-admin.php");
        exit();
    }

   
    $check_email = mysqli_query($conn, "SELECT * FROM admin WHERE email='$email' LIMIT 1");
    if (mysqli_num_rows($check_email) > 0) {
        $_SESSION['msg'] = "<div class='alert alert-danger'>Already reserved email.</div>";
        header("Location: add-admin.php");
        exit();
    }

    $sql = "INSERT INTO admin (Name, Password, email, Role) VALUES ('$username', '$password', '$email', 'admin')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "<div class='alert alert-success w-100 mb-0'>Admin Added </div>";
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }

    header("Location: add-admin.php");
    exit();
}

// fetch admin list from db
$admins = mysqli_query($conn, "SELECT * FROM admin ORDER BY ID ASC");

require 'includes/ad-header.php';
require_once 'includes/sidebar.php';
?>


<div class="container-fluid main-content">

<nav class="navbar navbar-expand-lg">
   <div class="container-fluid">

        <a class="navbar-brand fw-bold text-dark fs-3" href="#">Add / Manage Admins</a>

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
                    <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>

    </div>
</nav><hr class="m-0">

<?php echo $msg; ?>

<div class="row row-cols-1 row-cols-md-2 g-4 mt-2">

  <div class="col col-md-4">
  <form method="POST" action="">
    <div class="card">

      <div class="card-body">
        <h5 class="card-title fw-bold ct">Add New Admin</h5>
        <div class="mb-3">
  <label for="username" class="form-label fw-bold">Username</label>
  <input type="text" class="form-control" name="username" value="" placeholder="Enter username" required>
</div>
<div class="mb-3">
  <label for="email" class="form-label fw-bold">Email</label>
  <input type="email" class="form-control" name="email" value="" placeholder="Enter email" required>
</div>
<div class="mb-3">
  <label for="password" class="form-label fw-bold">Password</label>
  <input type="password" name="password" class="form-control" value="" placeholder="Enter password" required>
</div>
 <div class="mb-3">
  <label for="confirm_password" class="form-label fw-bold">Confirm Password</label>
  <input type="password" name="confirm_password" class="form-control" value="" placeholder="Confirm new password" required>
</div>
<div class="col-12">
    <button type="submit" name="add_admin" class="btn up-btn">Add Admin</button>
  </div>
      </div>
    </div>
</form>
  </div>

    <div class="col col-md-8">
<div class="card">

<div class="card-body">
     <h5 class="card-title fw-bold ct">Admin List</h5>
   <table class="table table-striped">

  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Username</th>
      <th scope="col">Role</th>
      <th scope="col">Action</th>

    </tr>
  </thead>
  <tbody>
    <?php while ($row = mysqli_fetch_assoc($admins)): ?>
    <tr>
      <th scope="row"><?php echo $row['id']; ?></th>
      <td><?php echo htmlspecialchars($row['Name']); ?></td>
      <td><?php echo htmlspecialchars($row['Role']); ?></td>
     <td>
        <?php if ($row['Role'] !== 'Super admin'): ?>
        <a href="delete-admin.php?id=<?php echo $row['id']; ?>"
           class="btn btn-danger"
           onclick="return confirm('Want to delete this admin?');">Delete</a>
        <?php else: ?>
        <span class="badge bg-secondary">Protected</span>
        <?php endif; ?>
     </td>
    </tr>
    <?php endwhile; ?>
  </tbody>

</table>

  </div>
</div>


</div><!--row-->

</div><!--main content-->

</body>
</html>