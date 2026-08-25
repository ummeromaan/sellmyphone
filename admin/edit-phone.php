<?php
session_start();

// Block direct access - must be logged in to view this page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';
/**@var mysqli $conn */

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: phones.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id'] ?? $_POST['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_phone'])) {

    $brand          = mysqli_real_escape_string($conn, $_POST['brand']);
    $phone_model    = mysqli_real_escape_string($conn, trim($_POST['phone_model']));
    $storage        = mysqli_real_escape_string($conn, trim($_POST['storage']));
    $flawless_price = floatval($_POST['flawless_price']);
    $good_price     = floatval($_POST['good_price']);
    $fair_price     = floatval($_POST['fair_price']);

    // ---------- If a new image was provided, replace the old one ----------
    $image_sql = "";
    $new_image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $image_name = time() . '-' . rand(100000000, 999999999) . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/' . $image_name)) {
                $old = mysqli_query($conn, "SELECT image FROM phones WHERE id='$id' LIMIT 1");
                $old_row = mysqli_fetch_assoc($old);
                if ($old_row && !empty($old_row['image']) && file_exists('../assets/images/' . $old_row['image'])) {
                    unlink('../assets/images/' . $old_row['image']);
                }
                $image_sql = ", image='$image_name'";
                $new_image_name = $image_name;
            }
        }
    }

    $sql = "UPDATE phones SET
                brand='$brand',
                phone_model='$phone_model',
                storage='$storage',
                flawless_price='$flawless_price',
                good_price='$good_price',
                fair_price='$fair_price'
                $image_sql
            WHERE id='$id'";

    mysqli_query($conn, $sql);

    // ---------- Keep the image consistent across every storage variant of this model ----------
    // (the frontend picks the image from the first variant row, so all variants must share the same one)
    if ($new_image_name !== null) {
        mysqli_query($conn, "UPDATE phones SET image='$new_image_name' WHERE brand='$brand' AND phone_model='$phone_model'");
    }

    // ---------- Also update accessories for this model ----------
    $charger_price   = floatval($_POST['charger_price'] ?? 0);
    $earphones_price = floatval($_POST['earphones_price'] ?? 0);
    $box_price       = floatval($_POST['box_price'] ?? 0);
    $bill_price      = floatval($_POST['bill_price'] ?? 0);

    mysqli_query($conn, "INSERT INTO model_accessories (brand, phone_model, charger_price, earphones_price, box_price, bill_price)
        VALUES ('$brand', '$phone_model', '$charger_price', '$earphones_price', '$box_price', '$bill_price')
        ON DUPLICATE KEY UPDATE
        charger_price='$charger_price', earphones_price='$earphones_price', box_price='$box_price', bill_price='$bill_price'");

    $_SESSION['msg'] = "<div class='alert alert-success w-100 mb-0'>Phone Updated</div>";
    header("Location: phones.php");
    exit();
}

// ---------- Fetch the record and pre-fill the form ----------
$result = mysqli_query($conn, "SELECT * FROM phones WHERE id='$id' LIMIT 1");
$phone  = mysqli_fetch_assoc($result);

if (!$phone) {
    header("Location: phones.php");
    exit();
}

// ---------- Also fetch this model's accessories for pre-filling ----------
$brand_esc = mysqli_real_escape_string($conn, $phone['brand']);
$model_esc = mysqli_real_escape_string($conn, $phone['phone_model']);
$acc_result = mysqli_query($conn, "SELECT * FROM model_accessories WHERE brand='$brand_esc' AND phone_model='$model_esc' LIMIT 1");
$accessories = mysqli_fetch_assoc($acc_result);

require 'includes/ad-header.php';
require_once 'includes/sidebar.php';
?>
<div class="container-fluid main-content">
<nav class="navbar navbar-expand-lg">
   <div class="container-fluid">
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">Edit Phone</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center fs-5 text-dark"
                  href="#" id="navbarDropdownMenuLink" role="button"
                   data-bs-toggle="dropdown" aria-expanded="false">
              <div class="user mx-2"><i class="fa-solid fa-user"></i></div> Admin
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav><hr class="m-0">

<div class="row mt-3">
  <div class="col col-md-6">
  <form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $phone['id']; ?>">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-bold ct">Edit Phone</h5>

        <div class="mb-3">
          <label class="form-label fw-bold">Brand</label>
          <select name="brand" class="form-control" required>
            <option value="Apple" <?php echo ($phone['brand'] === 'Apple') ? 'selected' : ''; ?>>Apple</option>
            <option value="Samsung" <?php echo ($phone['brand'] === 'Samsung') ? 'selected' : ''; ?>>Samsung</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Phone Model</label>
          <input type="text" class="form-control" name="phone_model" value="<?php echo htmlspecialchars($phone['phone_model']); ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Storage</label>
          <input type="text" class="form-control" name="storage" value="<?php echo htmlspecialchars($phone['storage']); ?>" required>
        </div>

        <hr>
        <label class="form-label fw-bold">Condition-wise Prices</label>

        <div class="mb-2">
          <label class="form-label">Flawless (AED)</label>
          <input type="number" step="0.01" class="form-control" name="flawless_price" value="<?php echo $phone['flawless_price']; ?>" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Good (AED)</label>
          <input type="number" step="0.01" class="form-control" name="good_price" value="<?php echo $phone['good_price']; ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Fair (AED)</label>
          <input type="number" step="0.01" class="form-control" name="fair_price" value="<?php echo $phone['fair_price']; ?>" required>
        </div>

        <hr>
        <h6 class="fw-bold ct">Accessories(AED)</h6>
        <div class="row g-2 mb-3">
          <div class="col-6">
            <label class="form-label">Charger (AED)</label>
            <input type="number" step="0.01" class="form-control" name="charger_price" value="<?php echo $accessories['charger_price'] ?? 0; ?>">
          </div>
          <div class="col-6">
            <label class="form-label">Earphones (AED)</label>
            <input type="number" step="0.01" class="form-control" name="earphones_price" value="<?php echo $accessories['earphones_price'] ?? 0; ?>">
          </div>
          <div class="col-6">
            <label class="form-label">Box (AED)</label>
            <input type="number" step="0.01" class="form-control" name="box_price" value="<?php echo $accessories['box_price'] ?? 0; ?>">
          </div>
          <div class="col-6">
            <label class="form-label">Valid Bill (AED)</label>
            <input type="number" step="0.01" class="form-control" name="bill_price" value="<?php echo $accessories['bill_price'] ?? 0; ?>">
          </div>
        </div>
        <p class="text-muted" style="font-size:12px;">(<?php echo htmlspecialchars($phone['phone_model']); ?>).</p>

        <div class="mb-3">
          <label class="form-label fw-bold">Replace Image (optional)</label>
          <input type="file" class="form-control" name="image" accept=".jpg,.jpeg,.png,.webp">
        </div>

        <button type="submit" name="update_phone" class="btn up-btn">Save Changes</button>
        <a href="phones.php" class="btn btn-secondary">Cancel</a>
      </div>
    </div>
  </form>
  </div>
</div>

</div><!--main-->
</body>
</html>