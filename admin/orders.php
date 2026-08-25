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

// ---------- Bulk status update (select via checkboxes and change all at once) ----------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bulk_update'])) {
    if (!empty($_POST['order_ids']) && !empty($_POST['bulk_status'])) {
        $bulk_status = mysqli_real_escape_string($conn, $_POST['bulk_status']);
        $ids = array_map('intval', $_POST['order_ids']);
        $ids_str = implode(',', $ids);

        mysqli_query($conn, "UPDATE orders SET status='$bulk_status' WHERE id IN ($ids_str)");
        $_SESSION['msg'] = "<div class='alert alert-success w-50 mb-0'>" . count($ids) . " order(s) updated to $bulk_status successfully.</div>";
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'>Please select orders and choose a status first.</div>";
    }
    header("Location: orders.php");
    exit();
}

// ---------- Get search + filter values from URL ----------
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

$search_esc = mysqli_real_escape_string($conn, $search);
$status_esc = mysqli_real_escape_string($conn, $status);

// ---------- Build the WHERE clause dynamically ----------
$conditions = [];

if ($search_esc !== '') {
    $conditions[] = "(customer_name LIKE '%$search_esc%' OR phone_no LIKE '%$search_esc%')";
}

if ($status_esc !== '' && $status_esc !== 'All') {
    $conditions[] = "status = '$status_esc'";
}

$where = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

// ---------- Get total count (for pagination) ----------
$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders $where");
$total_items  = mysqli_fetch_assoc($count_result)['total'];

$per_page     = 10;
$total_pages  = max(1, (int) ceil($total_items / $per_page));
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($current_page < 1) $current_page = 1;
if ($current_page > $total_pages) $current_page = $total_pages;

$offset = ($current_page - 1) * $per_page;

// ---------- Fetch the actual orders (only this page's 10) ----------
$orders = mysqli_query($conn, "SELECT * FROM orders $where ORDER BY id DESC LIMIT $per_page OFFSET $offset");

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
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">Orders</a>
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

<div class="orders-toolbar mt-3 mb-2">

  <!-- ==================== SEARCH + FILTER BAR ==================== -->
  <form method="GET" action="" class="d-flex flex-wrap align-items-center gap-2 mb-2">
    <div class="search-wrap">
     
      <input type="text" name="search" class="form-control search-input"
             placeholder="Search by name or phone..." value="<?php echo htmlspecialchars($search); ?>">
    </div>

    <select name="status" class="form-select status-select">
      <option value="">All Status</option>
      <option value="Pending"   <?php echo ($status === 'Pending')   ? 'selected' : ''; ?>>Pending</option>
      <option value="Contacted" <?php echo ($status === 'Contacted') ? 'selected' : ''; ?>>Contacted</option>
      <option value="Completed" <?php echo ($status === 'Completed') ? 'selected' : ''; ?>>Completed</option>
      <option value="Cancelled" <?php echo ($status === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
    </select>

    <button type="submit" class="btn up-btn filter-btn">Filter</button>
    <?php if ($search !== '' || $status !== ''): ?>
      <a href="orders.php" class="btn clear-btn">Clear</a>
    <?php endif; ?>
  </form>

  <!-- ==================== SELECT / BULK ACTIONS BAR ==================== -->
  <div class="select-bar d-flex flex-wrap align-items-center gap-2">
    <button type="button" id="toggle-select-btn" class="select-toggle-btn">Select</button>

    <div class="d-flex align-items-center gap-2 bulk-controls" id="bulk-controls">
      <select name="bulk_status" form="bulkForm" id="bulk-status-select" class="form-select form-select-sm bulk-status-select">
        <option value="">Bulk change status...</option>
        <option value="Pending">Pending</option>
        <option value="Contacted">Contacted</option>
        <option value="Completed">Completed</option>
        <option value="Cancelled">Cancelled</option>
      </select>

      <button type="submit" name="bulk_update" form="bulkForm" id="bulk-apply-btn" class="btn btn-sm up-btn">Apply</button>
    </div>
  </div>

</div>

<form method="POST" action="orders" id="bulkForm">
<div class="card rounded-2 mb-3 mt-4" style="width:100%;">
  <div class="card-body">
   <div class="table-responsive orders-table-scroll">
   <table class="table table-bordered align-middle orders-table" style="font-size:13px;">
    <thead>
      <tr>
        <th class="checkbox-col" style="width:30px;"></th>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Email</th>
        <th>Phone No</th>
        <th>Location</th>
        <th>Address</th>
        <th>Phone Model</th>
        <th>Price(AED)</th>
        <th>Pickup Date</th>
        <th>Time Slot</th>
        <th>Images</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($orders) === 0): ?>
        <tr><td colspan="14" class="text-center text-muted py-3">No orders found.</td></tr>
      <?php endif; ?>

      <?php while ($row = mysqli_fetch_assoc($orders)): ?>
      <?php
      // Address expands/collapses inline (opens on click, closes on mouse leave)
      $address_full = $row['address'] ?? '';

      // Array of images (ignoring empty slots)
      $images = array_filter([$row['image_1'], $row['image_2'], $row['image_3'], $row['image_4']]);

      $status_color = [
          'Pending'   => 'text-warning',
          'Contacted' => 'text-primary',
          'Completed' => 'text-success',
          'Cancelled' => 'text-danger',
      ];
      $color_class = $status_color[$row['status']] ?? '';
      ?>
      <tr>
        <td class="checkbox-col"><input type="checkbox" name="order_ids[]" value="<?php echo $row['id']; ?>" class="row-checkbox"></td>
        <td class="fw-bold">#ORD<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
        <td><?php echo htmlspecialchars($row['email'] ?? '-'); ?></td>
        <td><?php echo htmlspecialchars($row['phone_no']); ?></td>
        <td><?php echo htmlspecialchars($row['location'] ?? '-'); ?></td>

        <td>
          <?php if ($address_full !== ''): ?>
            <span class="address-toggle"><?php echo htmlspecialchars($address_full); ?></span>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>

        <td><?php echo htmlspecialchars($row['phone_model']); ?></td>
        <td><?php echo number_format($row['offered_price'], 0); ?></td>
        <td><?php echo $row['pickup_date'] ? date('M j, Y', strtotime($row['pickup_date'])) : '-'; ?></td>
        <td><?php echo htmlspecialchars($row['time_slot'] ?? '-'); ?></td>

        <td style="min-width:190px;">
          <div class="d-flex flex-nowrap gap-1">
            <?php foreach ($images as $img): ?>
              <img src="../assets/images/<?php echo htmlspecialchars($img); ?>"
                   class="order-thumb"
                   data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $row['id']; ?>">
            <?php endforeach; ?>
            <?php if (empty($images)): ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </div>
        </td>

        <td class="fw-bold <?php echo $color_class; ?>"><?php echo htmlspecialchars($row['status']); ?></td>

        <td class="text-center">
          <a href="delete-order.php?id=<?php echo $row['id']; ?>"
             class="btn btn-danger btn-sm"
             onclick="return confirm('Delete this order? This cannot be undone.');">
             <i class="fa-solid fa-trash"></i>
          </a>
        </td>
      </tr>

      <!-- ==================== IMAGES MODAL ==================== -->
      <?php if (!empty($images)): ?>
      <div class="modal fade" id="viewModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-bold">Order #ORD<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?> — Images</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="d-flex flex-wrap gap-2">
                <?php foreach ($images as $img): ?>
                  <div class="modal-img-wrap">
                    <img src="../assets/images/<?php echo htmlspecialchars($img); ?>">
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php endwhile; ?>
    </tbody>
   </table>
   </div>
  </div>
</div>
</form>

<script>
// "Select" button: checkbox column + bulk status/apply controls show/hide together
document.getElementById('toggle-select-btn').addEventListener('click', function () {
    const table = document.querySelector('.orders-table');
    const bulkControls = document.getElementById('bulk-controls');

    table.classList.toggle('show-checkboxes');
    this.classList.toggle('active');
    bulkControls.classList.toggle('show');

    if (!table.classList.contains('show-checkboxes')) {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('bulk-status-select').value = '';
    }
});

// Address expands on click, collapses on mouse leave
document.querySelectorAll('.address-toggle').forEach(function (el) {
    el.addEventListener('click', function () {
        this.classList.add('expanded');
    });
    el.addEventListener('mouseleave', function () {
        this.classList.remove('expanded');
    });
});
</script>

<!-- ==================== PAGINATION ==================== -->
<div class="d-flex justify-content-between align-items-center mt-3 mb-4 flex-wrap gap-2">

  <p class="text-muted mb-0">
    Showing <?php echo $total_items > 0 ? ($offset + 1) : 0; ?>
    to <?php echo min($offset + $per_page, $total_items); ?>
    of <?php echo $total_items; ?> entries
  </p>

  <?php if ($total_pages > 1): ?>
    <nav>
      <ul class="pagination mb-0">
        <li class="page-item <?php echo ($current_page == 1) ? 'disabled' : ''; ?>">
          <a class="page-link" href="?page=<?php echo max(1, $current_page - 1); ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">Previous</a>
        </li>
        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
          <li class="page-item <?php echo ($p == $current_page) ? 'active' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $p; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>"><?php echo $p; ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?php echo ($current_page == $total_pages) ? 'disabled' : ''; ?>">
          <a class="page-link" href="?page=<?php echo min($total_pages, $current_page + 1); ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">Next</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>

</div>

</div><!--container-->

</div><!--main-->
</body>
</html>