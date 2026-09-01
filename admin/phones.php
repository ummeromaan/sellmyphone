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

// ---------- Search: if something is typed in the search box, only fetch matching rows ----------
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_esc = mysqli_real_escape_string($conn, $search);

if ($search_esc !== '') {
    $where = "WHERE brand LIKE '%$search_esc%' OR phone_model LIKE '%$search_esc%'";
} else {
    $where = "";
}

$result = mysqli_query($conn, "SELECT * FROM phones $where ORDER BY phone_model ASC, FIELD(storage, '128GB','256GB','512GB','1TB','2TB') ASC");
// ---------- Group all rows by model ----------
$grouped = [];
while ($row = mysqli_fetch_assoc($result)) {
    $key = $row['brand'] . '|' . $row['phone_model'];

    if (!isset($grouped[$key])) {
        $grouped[$key] = [
            'brand'       => $row['brand'],
            'phone_model' => $row['phone_model'],
            'image'       => $row['image'],
            'variants'    => [],
        ];
    }

    $grouped[$key]['variants'][] = $row;
}

// ---------- Pagination: show 10 models per page ----------
$per_page     = 10;
$total_items  = count($grouped);
$total_pages  = max(1, (int) ceil($total_items / $per_page));
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($current_page < 1) $current_page = 1;
if ($current_page > $total_pages) $current_page = $total_pages;

$offset = ($current_page - 1) * $per_page;

// array_slice's third parameter "true" preserves the keys (brand|model)
$paged_groups = array_slice($grouped, $offset, $per_page, true);

// ---------- Distinct brands already in DB, for the Add Phone form's suggestions ----------
$brand_result = mysqli_query($conn, "SELECT DISTINCT brand FROM phones ORDER BY brand ASC");
$existing_brands = [];
if ($brand_result) {
    while ($b = mysqli_fetch_assoc($brand_result)) {
        $existing_brands[] = $b['brand'];
    }
}
if (empty($existing_brands)) {
    $existing_brands = ['Apple', 'Samsung'];
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
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">Models & pricing</a>
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



<?php echo $msg; ?>
<div class="d-flex align-items-center justify-content-between">
  <div class="w-50">
    <form method="GET" action="" class="mt-2 ms-2">
    <div class="input-group" style="max-width:400px;">
      <input type="text" name="search" class="form-control" placeholder="Search by brand or model..." value="<?php echo htmlspecialchars($search); ?>">
      <button type="submit" class="btn up-btn" style="height:43px;">Search</button>
      <?php if ($search !== ''): ?>
        <a href="phones.php" class="btn btn-secondary" style="height:43px;">Clear</a>
      <?php endif; ?>
    </div>
  </form>
      </div>
  <div class="">
    <a href="export-prices.php" class="btn btn-outline-dark me-2 fw-bold p-2">
    <i class="fa-solid fa-file-export me-1"></i> Export Prices
    </a>
    <button type="button" class="btn btn-outline-dark me-2 fw-bold p-2" data-bs-toggle="modal" data-bs-target="#importPricesModal">
    <i class="fa-solid fa-file-csv me-1"></i> Import Prices
    </button>
    <button type="button" class="btn border-dark me-2 fw-bold text-white p-2" style="background-color:#0e0831;" data-bs-toggle="modal" data-bs-target="#addPhoneModal">
    + Add Phone
    </button>
</div>

      </div>
  <!-- ==================== SEARCH BAR ==================== -->
  

  <?php if (empty($grouped)): ?>
    <div class="card mt-3"><div class="card-body">
      <?php echo ($search !== '') ? "'$search' No phone found" : "No phone found."; ?>
    </div></div>
  <?php endif; ?>

  <?php foreach ($paged_groups as $group): ?>

    <?php
    // ---------- Fetch this model's accessories (from the small separate table) ----------
    $brand_esc = mysqli_real_escape_string($conn, $group['brand']);
    $model_esc = mysqli_real_escape_string($conn, $group['phone_model']);
    $acc_result = mysqli_query($conn, "SELECT * FROM model_accessories WHERE brand='$brand_esc' AND phone_model='$model_esc' LIMIT 1");
    $accessories = mysqli_fetch_assoc($acc_result);
    ?>

    <div class="card rounded-3 mb-3 mt-3 p-3">
      <div class="row align-items-start">

        <!-- ==================== IMAGE + BRAND + MODEL ==================== -->
        <div class="col-md-2 text-center">
          <img src="../assets/images/<?php echo htmlspecialchars($group['image'] ?? ''); ?>"
               style="width:90px; height:120px; object-fit:cover; border-radius:10px;">
          <div class="mt-2">
            <span class="badge rounded-pill" style="background-color:#f1e6f5; color:#7a2d8c; padding:5px 10px; font-size:12px;">
              <?php echo htmlspecialchars($group['brand']); ?>
            </span>
            <h6 class="fw-bold mt-1 mb-0"><?php echo htmlspecialchars($group['phone_model']); ?></h6>
          </div>
        </div>

        <!-- ==================== STORAGE TABLE ==================== -->
        <div class="col-md-6">
          <h6 class="fw-bold ct">Storage &amp; Prices (AED)</h6>
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr>
                <th>Storage</th>
                <th>Flawless</th>
                <th>Good</th>
                <th>Fair</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($group['variants'] as $v): ?>
              <tr>
                <td><?php echo htmlspecialchars($v['storage']); ?></td>
                <td><?php echo number_format($v['flawless_price'], 0); ?></td>
                <td><?php echo number_format($v['good_price'], 0); ?></td>
                <td><?php echo number_format($v['fair_price'], 0); ?></td>
                <td class="text-center">
                  <button type="button" class="btn btn-primary btn-sm js-edit-btn"
                    data-id="<?php echo $v['id']; ?>"
                    data-brand="<?php echo htmlspecialchars($group['brand'], ENT_QUOTES); ?>"
                    data-model="<?php echo htmlspecialchars($group['phone_model'], ENT_QUOTES); ?>"
                    data-storage="<?php echo htmlspecialchars($v['storage'], ENT_QUOTES); ?>"
                    data-flawless="<?php echo $v['flawless_price']; ?>"
                    data-good="<?php echo $v['good_price']; ?>"
                    data-fair="<?php echo $v['fair_price']; ?>"
                    data-charger="<?php echo $accessories['charger_price'] ?? 0; ?>"
                    data-earphones="<?php echo $accessories['earphones_price'] ?? 0; ?>"
                    data-box="<?php echo $accessories['box_price'] ?? 0; ?>"
                    data-bill="<?php echo $accessories['bill_price'] ?? 0; ?>">
                    <i class="fa-solid fa-pen"></i>
                  </button>
                  <a href="delete-phone.php?id=<?php echo $v['id']; ?>"
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('Do you want to delete that storage variant?');"><i class="fa-solid fa-trash"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- ==================== ACCESSORIES TABLE ==================== -->
        <div class="col-md-4">
          <h6 class="fw-bold ct">Accessories &amp; Prices (AED)</h6>
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr>
                <th>Accessory</th>
                <th>Price</th>
              </tr>
            </thead>
            <tbody>
              <tr><td>Charger</td><td><?php echo number_format($accessories['charger_price'] ?? 0, 0); ?></td></tr>
              <tr><td>Earphones</td><td><?php echo number_format($accessories['earphones_price'] ?? 0, 0); ?></td></tr>
              <tr><td>Box</td><td><?php echo number_format($accessories['box_price'] ?? 0, 0); ?></td></tr>
              <tr><td>Valid Bill</td><td><?php echo number_format($accessories['bill_price'] ?? 0, 0); ?></td></tr>
            </tbody>
          </table>
          
        </div>

      </div>
    </div>

  <?php endforeach; ?>

  <!-- ==================== PAGINATION ==================== -->
  <!-- ==================== PAGINATION ==================== -->
<?php if ($total_pages > 1): ?>
  <nav class="mt-3 mb-4 d-flex justify-content-center">
    <ul class="pagination">

      <!-- Previous arrow -->
      <li class="page-item <?php echo ($current_page == 1) ? 'disabled' : ''; ?>">
        <a class="page-link"
           href="?page=<?php echo max(1, $current_page - 1); ?><?php echo ($search !== '') ? '&search=' . urlencode($search) : ''; ?>">
           &laquo;
        </a>
      </li>

      <?php for ($p = 1; $p <= $total_pages; $p++): ?>
        <li class="page-item <?php echo ($p == $current_page) ? 'active' : ''; ?>">
          <a class="page-link"
             href="?page=<?php echo $p; ?><?php echo ($search !== '') ? '&search=' . urlencode($search) : ''; ?>">
             <?php echo $p; ?>
          </a>
        </li>
      <?php endfor; ?>

      <!-- Next arrow -->
      <li class="page-item <?php echo ($current_page == $total_pages) ? 'disabled' : ''; ?>">
        <a class="page-link"
           href="?page=<?php echo min($total_pages, $current_page + 1); ?><?php echo ($search !== '') ? '&search=' . urlencode($search) : ''; ?>">
           &raquo;
        </a>
      </li>

    </ul>
  </nav>
<?php endif; ?>

</div><!--container-->

<!-- ==================== IMPORT PRICES MODAL (CSV upload, bulk update/insert) ==================== -->
<div class="modal fade" id="importPricesModal" tabindex="-1" aria-labelledby="importPricesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="import-prices" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="importPricesModalLabel">Import Prices from CSV</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="max-height:70vh; overflow-y:auto;">

          <p class="text-muted" style="font-size:13.5px;">
            Save your Excel sheet in <strong>CSV</strong> format, then upload it here.
            The header row must include these columns (order doesn't matter, they're matched by name):
          </p>

          <div class="border rounded p-2 mb-3" style="font-size:12.5px; background:#f8f8fb; overflow-x:auto;">
            <code>Brand, Model, Storage, Flawless (or Excellent), Good, Fair</code><br>
            <code>+ optional: Charger, Earphones, Box, Bill</code>
          </div>

          <a href="sample-prices-template.csv" download class="d-inline-block mb-3" style="font-size:13px;">
            <i class="fa-solid fa-download me-1"></i> Download sample template
          </a>
          <p class="text-muted" style="font-size:12.5px;">
            Tip: use the <strong>Export Prices</strong> button to download the current data in the exact format above, edit it in Excel, then re-import it here to update prices in bulk.
          </p>

          <ul class="text-muted" style="font-size:13px; padding-left:18px;">
            <li>The CSV must include a "Brand" column with each row set to Apple or Samsung</li>
            <li>Storage: both "128GB" and "128 GB" are accepted</li>
            <li>If the Model+Storage already exists in the DB, only the <strong>price will be updated</strong>; otherwise a <strong>new row will be added</strong></li>
          </ul>

          <div class="mb-2">
            <label class="form-label fw-bold">CSV File</label>
            <input type="file" class="form-control" name="csv_file" accept=".csv" required>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="import_prices" class="btn up-btn">Upload &amp; Import</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ==================== ADD PHONE MODAL (single form, works for any brand) ==================== -->
<div class="modal fade" id="addPhoneModal" tabindex="-1" aria-labelledby="addPhoneModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="add-phone" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="addPhoneModalLabel">Add Phone</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Brand</label>
              <select name="brand" class="form-control" required>
                <option value="" disabled selected>Select Brand</option>
                <option value="Apple">Apple</option>
                <option value="Samsung">Samsung</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Phone Model</label>
              <input type="text" class="form-control" name="phone_model" placeholder="e.g. iPhone 17 / Galaxy S25" required>
            </div>
          </div>

          <hr>

          <div class="border rounded p-3 mb-2">
            <label class="form-label fw-bold">128GB</label>
            <div class="row g-2">
              <div class="col-4"><label class="form-label">Flawless</label><input type="number" step="0.01" class="form-control" name="flawless_128"></div>
              <div class="col-4"><label class="form-label">Good</label><input type="number" step="0.01" class="form-control" name="good_128"></div>
              <div class="col-4"><label class="form-label">Fair</label><input type="number" step="0.01" class="form-control" name="fair_128"></div>
            </div>
          </div>

          <div class="border rounded p-3 mb-2">
            <label class="form-label fw-bold">256GB</label>
            <div class="row g-2">
              <div class="col-4"><label class="form-label">Flawless</label><input type="number" step="0.01" class="form-control" name="flawless_256"></div>
              <div class="col-4"><label class="form-label">Good</label><input type="number" step="0.01" class="form-control" name="good_256"></div>
              <div class="col-4"><label class="form-label">Fair</label><input type="number" step="0.01" class="form-control" name="fair_256"></div>
            </div>
          </div>

          <div class="border rounded p-3 mb-2">
            <label class="form-label fw-bold">512GB</label>
            <div class="row g-2">
              <div class="col-4"><label class="form-label">Flawless</label><input type="number" step="0.01" class="form-control" name="flawless_512"></div>
              <div class="col-4"><label class="form-label">Good</label><input type="number" step="0.01" class="form-control" name="good_512"></div>
              <div class="col-4"><label class="form-label">Fair</label><input type="number" step="0.01" class="form-control" name="fair_512"></div>
            </div>
          </div>

          <div class="border rounded p-3 mb-2">
            <label class="form-label fw-bold">1TB</label>
            <div class="row g-2">
              <div class="col-4"><label class="form-label">Flawless</label><input type="number" step="0.01" class="form-control" name="flawless_1tb"></div>
              <div class="col-4"><label class="form-label">Good</label><input type="number" step="0.01" class="form-control" name="good_1tb"></div>
              <div class="col-4"><label class="form-label">Fair</label><input type="number" step="0.01" class="form-control" name="fair_1tb"></div>
            </div>
          </div>

          <div class="border rounded p-3 mb-3">
            <label class="form-label fw-bold">2TB</label>
            <div class="row g-2">
              <div class="col-4"><label class="form-label">Flawless</label><input type="number" step="0.01" class="form-control" name="flawless_2tb"></div>
              <div class="col-4"><label class="form-label">Good</label><input type="number" step="0.01" class="form-control" name="good_2tb"></div>
              <div class="col-4"><label class="form-label">Fair</label><input type="number" step="0.01" class="form-control" name="fair_2tb"></div>
            </div>
          </div>

          <hr>
          <h6 class="fw-bold ct">Accessories Prices</h6>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label">Charger (AED)</label>
              <input type="number" step="0.01" class="form-control" name="charger_price" placeholder="0">
            </div>
            <div class="col-6">
              <label class="form-label">Earphones (AED)</label>
              <input type="number" step="0.01" class="form-control" name="earphones_price" placeholder="0">
            </div>
            <div class="col-6">
              <label class="form-label">Box (AED)</label>
              <input type="number" step="0.01" class="form-control" name="box_price" placeholder="0">
            </div>
            <div class="col-6">
              <label class="form-label">Valid Bill (AED)</label>
              <input type="number" step="0.01" class="form-control" name="bill_price" placeholder="0">
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label fw-bold">Phone Image</label>
            <input type="file" class="form-control" name="image" accept=".jpg,.jpeg,.png,.webp">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_phone" class="btn up-btn">Add Phone</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ==================== EDIT PHONE MODAL (single shared form, works for any brand) ==================== -->
<div class="modal fade" id="editPhoneModal" tabindex="-1" aria-labelledby="editPhoneModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="edit-phone" enctype="multipart/form-data">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="editPhoneModalLabel">Edit Phone</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Brand</label>
              <select name="brand" id="edit_brand" class="form-control" required>
                <option value="Apple">Apple</option>
                <option value="Samsung">Samsung</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Phone Model</label>
              <input type="text" class="form-control" name="phone_model" id="edit_phone_model" required>
            </div>
          </div>

          <hr>

          <div class="border rounded p-3 mb-2">
            <label class="form-label fw-bold">Storage</label>
            <input type="text" class="form-control mb-2" name="storage" id="edit_storage" placeholder="e.g. 128GB" required>
            <div class="row g-2">
              <div class="col-4"><label class="form-label">Flawless</label><input type="number" step="0.01" class="form-control" name="flawless_price" id="edit_flawless_price" required></div>
              <div class="col-4"><label class="form-label">Good</label><input type="number" step="0.01" class="form-control" name="good_price" id="edit_good_price" required></div>
              <div class="col-4"><label class="form-label">Fair</label><input type="number" step="0.01" class="form-control" name="fair_price" id="edit_fair_price" required></div>
            </div>
          </div>

          <hr>
          <h6 class="fw-bold ct">Accessories Prices</h6>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label">Charger (AED)</label>
              <input type="number" step="0.01" class="form-control" name="charger_price" id="edit_charger_price" placeholder="0">
            </div>
            <div class="col-6">
              <label class="form-label">Earphones (AED)</label>
              <input type="number" step="0.01" class="form-control" name="earphones_price" id="edit_earphones_price" placeholder="0">
            </div>
            <div class="col-6">
              <label class="form-label">Box (AED)</label>
              <input type="number" step="0.01" class="form-control" name="box_price" id="edit_box_price" placeholder="0">
            </div>
            <div class="col-6">
              <label class="form-label">Valid Bill (AED)</label>
              <input type="number" step="0.01" class="form-control" name="bill_price" id="edit_bill_price" placeholder="0">
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label fw-bold">Replace Image (optional)</label>
            <input type="file" class="form-control" name="image" accept=".jpg,.jpeg,.png,.webp">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="update_phone" class="btn up-btn">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// On Edit button click, fill the modal's fields (same modal works for both Apple and Samsung)
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.js-edit-btn');
    if (!btn) return;

    document.getElementById('edit_id').value              = btn.dataset.id;
    document.getElementById('edit_brand').value            = btn.dataset.brand;
    document.getElementById('edit_phone_model').value      = btn.dataset.model;
    document.getElementById('edit_storage').value          = btn.dataset.storage;
    document.getElementById('edit_flawless_price').value   = btn.dataset.flawless;
    document.getElementById('edit_good_price').value       = btn.dataset.good;
    document.getElementById('edit_fair_price').value       = btn.dataset.fair;
    document.getElementById('edit_charger_price').value    = btn.dataset.charger;
    document.getElementById('edit_earphones_price').value  = btn.dataset.earphones;
    document.getElementById('edit_box_price').value        = btn.dataset.box;
    document.getElementById('edit_bill_price').value       = btn.dataset.bill;

    var modal = new bootstrap.Modal(document.getElementById('editPhoneModal'));
    modal.show();
});
</script>

</div><!--main-->
</body>
</html>