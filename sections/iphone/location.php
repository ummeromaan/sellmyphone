<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
$loc = home_single($conn, 'iphone_location');
$areas = home_rows($conn, 'iphone_location_areas');
?>
<section id="samsung-coverage" class="samsung-coverage-section">
  <div class="container-fluid samsung-coverage-wrap">

    <span class="samsung-coverage-badge"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($loc['badge_text'] ?? 'Full Dubai Reach'); ?></span>
    <h2 class="samsung-coverage-heading"><?php echo htmlspecialchars($loc['heading'] ?? 'Same-Day Doorstep Pickup Across Dubai'); ?></h2>
    <p class="samsung-coverage-text"><?php echo htmlspecialchars($loc['text'] ?? 'Our mobile specialists operate across all major residential communities, business towers, and villa estates with 60 to 90 minute arrival times:'); ?></p>

    <div class="samsung-coverage-areas">
      <?php foreach ($areas as $a): ?>
      <span class="samsung-area-pill"><?php echo htmlspecialchars($a['area_name']); ?></span>
      <?php endforeach; ?>
    </div>

  </div>
</section>