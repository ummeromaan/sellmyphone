<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';
/**@var mysqli $conn */

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone     = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $email     = mysqli_real_escape_string($conn, trim($_POST['email']));
    $address   = mysqli_real_escape_string($conn, trim($_POST['address']));
    $facebook  = mysqli_real_escape_string($conn, trim($_POST['facebook_url']));
    $instagram = mysqli_real_escape_string($conn, trim($_POST['instagram_url']));
    $linkedin  = mysqli_real_escape_string($conn, trim($_POST['linkedin_url']));
    $twitter   = mysqli_real_escape_string($conn, trim($_POST['twitter_url']));

    mysqli_query($conn, "UPDATE contact_info SET
        phone='$phone', email='$email', address='$address',
        facebook_url='$facebook', instagram_url='$instagram',
        linkedin_url='$linkedin', twitter_url='$twitter'
        WHERE id=1");

    $msg = "<div class='alert alert-success w-50 mb-0'>Contact Info Updated.</div>";
}

$info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM contact_info WHERE id=1"));

require 'includes/ad-header.php';
require_once 'includes/sidebar.php';
?>
<link rel="stylesheet" href="home-content.css">

<div class="container-fluid main-content">

<nav class="navbar navbar-expand-lg">
   <div class="container-fluid">
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">Contact Page Info</a>
    </div>
</nav><hr class="m-0">

<div class="mt-3"><?php echo $msg; ?></div>

<div class="hc-card mb-4">
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Phone Number</label>
                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($info['phone']); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Email Address</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($info['email']); ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Address</label>
                <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($info['address']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Facebook URL</label>
                <input type="text" class="form-control" name="facebook_url" value="<?php echo htmlspecialchars($info['facebook_url']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Instagram URL</label>
                <input type="text" class="form-control" name="instagram_url" value="<?php echo htmlspecialchars($info['instagram_url']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">LinkedIn URL</label>
                <input type="text" class="form-control" name="linkedin_url" value="<?php echo htmlspecialchars($info['linkedin_url']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Twitter URL</label>
                <input type="text" class="form-control" name="twitter_url" value="<?php echo htmlspecialchars($info['twitter_url']); ?>">
            </div>
        </div>
        <button type="submit" class="btn up-btn mt-3">Save Changes</button>
    </form>
</div>

</div>
</body>
</html>