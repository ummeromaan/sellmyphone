<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
/**@var mysqli $conn */

$hero          = home_single($conn, 'home_hero');
$hero_features = home_rows($conn, 'home_hero_features');
$hero_stats    = home_rows($conn, 'home_hero_stats');
?>
<section id="hero" class="hero-container reveal-on-scroll"<?php if (!empty($hero['bg_image'])): ?> style="background-image:url('<?php echo htmlspecialchars(home_img($hero['bg_image'])); ?>')"<?php endif; ?>>
  <!-- Dark overlay to fade the background image on the text side only -->
  <div class="hero-overlay"></div>

 <div class="container-fluid hero-fluid-wrap position-relative">
    <div class="row align-items-center">

      <!-- Text content -->
      <div class="col-lg-7 col-md-8">
        <div class="hero-cont">

          <span class="hero-badge">
            <i class="<?php echo htmlspecialchars($hero['badge_icon'] ?? 'fa-solid fa-shield-halved'); ?>"></i>
            <?php echo $hero['badge_text'] ?? ''; ?>
          </span>

          <h1 class="hero-content1">
            <?php echo $hero['title_pre'] ?? 'Turn Your Old Phone<br>Into'; ?>
            <span class="highlight"><?php echo $hero['title_highlight'] ?? 'Instant Cash'; ?></span>
          </h1>

          <p class="hero-content2">
            <?php echo $hero['subtitle'] ?? ''; ?>
          </p>

          <div class="hero-info-row">
            <?php foreach ($hero_features as $f): ?>
            <div class="info-box">
              <i class="<?php echo htmlspecialchars($f['icon_class']); ?> fs-4"></i>
              <span><?php echo $f['label']; ?></span>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="h-btns d-flex gap-3">
            <!-- Scrolls down to the Brands section, where the user picks their phone brand -->
            <a href="#calc" class="btn heroo-btn btn-primary d-flex align-items-center gap-2">
              <b>Book Free Pickup</b>
              <i class="fa-solid fa-arrow-right"></i>
            </a>
            <!-- Scrolls down to the How It Works section -->
            <a href="#how-it-works" class="btn btn-outline-hero d-flex align-items-center gap-2">
              <i class="fa-solid fa-circle-play"></i>
               How It Works
            </a>
          </div>

        </div>
      </div>

      <!-- Right side is intentionally empty - the phones already show
           in the section's background image -->
      <div class="col-lg-5 col-md-4 phone-visuals reveal-on-scroll">
        <img class="reveal-item" src="<?php echo htmlspecialchars(home_img($hero['side_image'] ?? '', 'fons.webp')); ?>">
      </div>

    </div>

    <!-- Trust stats bar -->
    <div class="hero-stats-bar">
      <?php $total = count($hero_stats); foreach ($hero_stats as $i => $s): ?>
      <div class="hero-stat">
        <i class="<?php echo htmlspecialchars($s['icon_class']); ?> fs-3" style="color:<?php echo htmlspecialchars($s['icon_color'] ?: '#f7c82f'); ?>;"></i>
        <div class="hero-stat-text">
          <strong><?php echo $s['value']; ?></strong>
          <span><?php echo $s['label']; ?></span>
        </div>
      </div>
      <?php if ($i < $total - 1): ?><div class="hero-stats-divider"></div><?php endif; ?>
      <?php endforeach; ?>
    </div>

  </div>
</section>
