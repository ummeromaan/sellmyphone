<?php
session_start();

// Block direct access - must be logged in to view this page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';
require_once 'includes/notifications.php';
/**@var mysqli $conn */

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$notif_count = get_notification_count($conn);

// ---------- Card 1: Total Orders ----------
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders"))['cnt'];

// ---------- Card 2: Pending Orders ----------
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders WHERE status='Pending'"))['cnt'];

// ---------- Card 3: Completed Orders ----------
$completed_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders WHERE status='Completed'"))['cnt'];

// ---------- Card 4: Est. Payout () ----------
$est_payout = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(offered_price) AS total FROM orders WHERE status='Completed'"))['total'];
$est_payout = $est_payout ?? 0;

// ---------- Recent Orders () ----------
$recent_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC LIMIT 5");

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
    <a class="navbar-brand fw-bold text-dark fs-3" href="#">Dashboard</a>
       
        <ul class="navbar-nav ms-auto align-items-center">

            <li class="nav-item dropdown me-3" id="notif-dropdown">
                <a class="nav-link position-relative fs-3 text-dark" href="#" id="notifBellLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-regular fa-bell"></i>
                    <span class="notif-badge <?php echo $notif_count > 0 ? '' : 'd-none'; ?>" id="notifBadge"><?php echo $notif_count; ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end notif-panel p-0" aria-labelledby="notifBellLink">
                    <div class="notif-panel-header">Notifications</div>
                    <div class="notif-panel-list" id="notifList">
                        <div class="text-center text-muted py-4 small">Loading...</div>
                    </div>
                </div>
            </li>

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

<script>
// ==================== NOTIFICATIONS (bell icon) ====================
function renderNotifItems(items) {
    const list = document.getElementById('notifList');
    if (!items.length) {
        list.innerHTML = '<div class="text-center text-muted py-4 small">No activity yet.</div>';
        return;
    }
    list.innerHTML = items.map(function (it) {
        return '<a href="' + it.link + '" class="notif-item ' + (it.is_new ? 'notif-item-new' : '') + '">'
            + '<div class="notif-icon"><i class="fa-solid ' + it.icon + '"></i></div>'
            + '<div class="notif-text">'
            + '<p class="notif-title">' + it.title + '</p>'
            + '<p class="notif-sub">' + it.subtitle + ' &middot; ' + it.time + '</p>'
            + '</div>'
            + '</a>';
    }).join('');
}

function fetchNotifCount() {
    fetch('get-notifications.php')
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'ok') return;
            const badge = document.getElementById('notifBadge');
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        })
        .catch(() => {});
}

document.getElementById('notifBellLink').addEventListener('click', function () {
    fetch('get-notifications.php')
        .then(res => res.json())
        .then(data => {
            if (data.status === 'ok') renderNotifItems(data.items);
        })
        .then(() => fetch('mark-notifications-seen.php'))
        .then(() => {
            document.getElementById('notifBadge').classList.add('d-none');
        })
        .catch(() => {});
});

//new count check after every 30 sec
setInterval(fetchNotifCount, 30000);
</script>

 <h2 class="fw-bold pt-4" style="color: #1f174d;">Welcome back, <?php echo htmlspecialchars($admin_name); ?>!</h2>
  <p class="text-muted">Here's what's happening with your store today.</p>
 

  <!-- ==================== STAT CARDS ==================== -->
  <div class="row g-3 mt-2">

    <div class="col-md-3">
      <div class="card p-3 h-100 rounded-4">
         <div class="d-flex align-items-center gap-3">
  <i class="fa-solid fa-mobile-screen ic" style="color:#0e0831; font-size:40px;"></i>
  <div>
    <p class="mb-0 fw-bold" style="font-size:18px;">Total Orders</p>
    <h3 class="fw-bold mb-0" style="color:#0e0831;"><?php echo $total_orders; ?></h3>
  </div>
</div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card p-3 h-100 rounded-4">
        <div class="d-flex align-items-center gap-3">
  <i class="fa-solid fa-clipboard-list ic text-warning" style="font-size:40px;"></i>
  <div>
    <p class="mb-0 fw-bold" style="font-size:17px;">Pending Orders</p>
    <h3 class="fw-bold text-warning"><?php echo $pending_orders; ?> </h3>
  </div>
</div>
      </div>
    </div>
 
 
    <div class="col-md-3">
      <div class="card p-3 h-100 rounded-4">
         <div class="d-flex align-items-center gap-3">
  <i class="fa-solid fa-circle-check ic" style="color:#2fa84f; font-size:40px;"></i>
  <div>
    <p class="mb-0 fw-bold"style="font-size:17px;">Completed Orders</p>
    <h3 class="fw-bold text-success"><?php echo $completed_orders; ?></h3>
  </div>
</div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card p-3 h-100 rounded-4">
      <div class="d-flex align-items-center gap-3">
  <i class="fa-solid fa-sack-dollar" style="color: #41104b; font-size:40px;"></i>
  <div>
    <p class="mb-0 fw-bold" style="font-size:17px;">Est.payout</p>
    <h3 class="fw-bold"><?php echo $est_payout; ?></h3>
  </div>
</div>
      </div>
    </div>

  </div>

  <!-- ==================== RECENT ORDERS ==================== -->
  <div class="d-flex align-items-center mt-4">
    <h5 class="fw-bold" style="color:#3f073c;">Recent Orders</h5>
    <a href="orders.php" class="btn up-btn ms-auto">View All</a>
  </div>

  <div class="table-responsive orders-table-scroll mt-2">
  <table class="table table-bordered align-middle orders-table w-100" style="font-size:13px;">
    <thead>
      <tr>
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
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($recent_orders) === 0): ?>
        <tr><td colspan="13" class="text-center text-muted py-3">No order found.</td></tr>
      <?php endif; ?>

      <?php while ($row = mysqli_fetch_assoc($recent_orders)): ?>
      <?php
      $status_color = [
          'Pending'   => 'text-warning',
          'Contacted' => 'text-primary',
          'Completed' => 'text-success',
          'Cancelled' => 'text-danger',
      ];
      $color_class = $status_color[$row['status']] ?? '';

      $images = array_filter([$row['image_1'], $row['image_2'], $row['image_3'], $row['image_4']]);
      ?>
      <tr>
        <td class="fw-bold">#ORD<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
        <td><?php echo htmlspecialchars($row['email'] ?? '-'); ?></td>
        <td><?php echo htmlspecialchars($row['phone_no']); ?></td>
        <td><?php echo htmlspecialchars($row['location'] ?? '-'); ?></td>
        <td><?php echo htmlspecialchars($row['address'] ?? '-'); ?></td>
        <td><?php echo htmlspecialchars($row['phone_model']); ?></td>
        <td><?php echo number_format($row['offered_price'], 0); ?></td>
        <td><?php echo $row['pickup_date'] ? date('M j, Y', strtotime($row['pickup_date'])) : '-'; ?></td>
        <td><?php echo htmlspecialchars($row['time_slot'] ?? '-'); ?></td>
        <td style="min-width:190px;">
          <div class="d-flex flex-nowrap gap-1">
            <?php foreach ($images as $img): ?>
              <img src="../assets/images/<?php echo htmlspecialchars($img); ?>" class="order-thumb">
            <?php endforeach; ?>
            <?php if (empty($images)): ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </div>
        </td>
        <td><span class="fw-bold <?php echo $color_class; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
        <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>

  <!-- ==================== QUICK ACTIONS ==================== -->
  <div class="card rounded-4 mt-4 p-3">
    <h5 class="fw-bold mb-3" style="color:#090533;">Quick Actions</h5>
    <div class="row g-3">

      <div class="col-md-3 col-sm-6">
        <a href="phones.php" class="qa-card qa-purple">
          <div class="qa-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
          <div>
            <p class="qa-title">Add New Model</p>
            <p class="qa-desc">Add new phone model with pricing</p>
          </div>
          <span class="qa-arrow"><i class="fa-solid fa-arrow-right"></i></span>
        </a>
      </div>

      <div class="col-md-3 col-sm-6">
        <a href="orders.php" class="qa-card qa-blue">
          <div class="qa-icon"><i class="fa-solid fa-cart-shopping"></i></div>
          <div>
            <p class="qa-title">View Orders</p>
            <p class="qa-desc">View and manage all orders</p>
          </div>
          <span class="qa-arrow"><i class="fa-solid fa-arrow-right"></i></span>
        </a>
      </div>

      <div class="col-md-3 col-sm-6">
        <a href="phones.php" class="qa-card qa-green">
          <div class="qa-icon"><i class="fa-solid fa-tag"></i></div>
          <div>
            <p class="qa-title">Manage Pricing</p>
            <p class="qa-desc">Update phone prices and conditions</p>
          </div>
          <span class="qa-arrow"><i class="fa-solid fa-arrow-right"></i></span>
        </a>
      </div>

      <div class="col-md-3 col-sm-6">
        <a href="add-admin.php" class="qa-card qa-orange">
          <div class="qa-icon"><i class="fa-solid fa-user-group"></i></div>
          <div>
            <p class="qa-title">Manage Admins</p>
            <p class="qa-desc">Add or manage admin accounts</p>
          </div>
          <span class="qa-arrow"><i class="fa-solid fa-arrow-right"></i></span>
        </a>
      </div>

    </div>
  </div>

                </div><!--container-->

</div><!--main-->
</body>
</html>