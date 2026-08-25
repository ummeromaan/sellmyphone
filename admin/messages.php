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

// --------------------
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_esc = mysqli_real_escape_string($conn, $search);

if ($search_esc !== '') {
    $where = "WHERE full_name LIKE '%$search_esc%' OR email LIKE '%$search_esc%' OR subject LIKE '%$search_esc%'";
} else {
    $where = "";
}

// ---------- Total count (pagination) ----------
$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM contact_messages $where");
$total_items  = mysqli_fetch_assoc($count_result)['total'];

$per_page     = 10;
$total_pages  = max(1, (int) ceil($total_items / $per_page));
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($current_page < 1) $current_page = 1;
if ($current_page > $total_pages) $current_page = $total_pages;

$offset = ($current_page - 1) * $per_page;

$messages = mysqli_query($conn, "SELECT * FROM contact_messages $where ORDER BY id DESC LIMIT $per_page OFFSET $offset");

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
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">Messages</a>
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

<div class="d-flex align-items-center justify-content-between mt-3">
  <div class="w-50">
    <form method="GET" action="" class="mt-2">
    <div class="input-group" style="max-width:400px;">
      <input type="text" name="search" class="form-control" placeholder="Search by name, email or subject..." value="<?php echo htmlspecialchars($search); ?>">
      <button type="submit" class="btn up-btn" style="height:43px;">Search</button>
      <?php if ($search !== ''): ?>
        <a href="messages.php" class="btn btn-secondary" style="height:43px;">Clear</a>
      <?php endif; ?>
    </div>
    </form>
  </div>
</div>

<div class="card rounded-2 mb-3 mt-3" style="width:100%;">
  <div class="card-body">
   <div class="table-responsive orders-table-scroll">
   <table class="table table-bordered align-middle orders-table" style="font-size:13px;">
    <thead>
      <tr>
        <th>#</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Received</th>
        <th style="width:70px;">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($messages) === 0): ?>
        <tr><td colspan="8" class="text-center text-muted py-3">No messages found.</td></tr>
      <?php endif; ?>

      <?php while ($row = mysqli_fetch_assoc($messages)): ?>
      <tr>
        <td class="fw-bold">#MSG<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo htmlspecialchars($row['phone'] ?? '-'); ?></td>
        <td><?php echo htmlspecialchars($row['subject'] ?? '-'); ?></td>
        <td>
          <span class="address-toggle" style="max-width:220px;"><?php echo htmlspecialchars($row['message']); ?></span>
        </td>
        <td><?php echo date('M j, Y g:i A', strtotime($row['created_at'])); ?></td>
        <td>
          <a href="delete-message.php?id=<?php echo $row['id']; ?>"
             class="btn btn-sm btn-outline-danger"
             onclick="return confirm('Delete this message?');">
             <i class="fa-solid fa-trash"></i>
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
   </table>
   </div>
  </div>
</div>

<script>
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
          <a class="page-link" href="?page=<?php echo max(1, $current_page - 1); ?><?php echo ($search !== '') ? '&search=' . urlencode($search) : ''; ?>">Previous</a>
        </li>
        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
          <li class="page-item <?php echo ($p == $current_page) ? 'active' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $p; ?><?php echo ($search !== '') ? '&search=' . urlencode($search) : ''; ?>"><?php echo $p; ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?php echo ($current_page == $total_pages) ? 'disabled' : ''; ?>">
          <a class="page-link" href="?page=<?php echo min($total_pages, $current_page + 1); ?><?php echo ($search !== '') ? '&search=' . urlencode($search) : ''; ?>">Next</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>

</div>

</div><!--container-->

</div><!--main-->
</body>
</html>