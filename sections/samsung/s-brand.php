<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
$brand = home_single($conn, 'samsung_brand');
$features = home_rows($conn, 'samsung_brand_features');
?>
<section id="samsung-why" class="samsung-brand-section">
  <div class="container-fluid samsung-brand-wrap">

    <div class="samsung-brand-header">
      <span class="samsung-brand-badge"><?php echo htmlspecialchars($brand['badge_text'] ?? 'Get Instant Quote'); ?></span>
      <h2 class="samsung-brand-heading">
        <?php echo htmlspecialchars($brand['heading_pre'] ?? 'Sell Your'); ?>
        <span class="highlight"><?php echo htmlspecialchars($brand['heading_highlight'] ?? 'Samsung'); ?></span>
        <?php echo htmlspecialchars($brand['heading_post'] ?? 'the Smart Way'); ?>
      </h2>
      <p class="samsung-brand-subtext"><?php echo htmlspecialchars($brand['subtext'] ?? 'Get an instant, no-obligation price for your Samsung phone and turn it into cash today.'); ?></p>
    </div>

    <div class="samsung-brand-card">
      <div class="samsung-brand-left">
        <h3 class="samsung-brand-title"><?php echo htmlspecialchars($brand['left_title'] ?? 'We Buy All Latest Samsung Phones'); ?></h3>
        <p class="samsung-brand-text"><?php echo htmlspecialchars($brand['left_text'] ?? 'Sell your Samsung phone in Dubai and get the best value instantly. Quick, easy and 100% secure process.'); ?></p>

        <div class="samsung-brand-features">
          <?php foreach ($features as $f): ?>
          <div class="samsung-brand-feature">
            <span class="samsung-brand-feature-icon"><i class="<?php echo htmlspecialchars($f['icon']); ?>"></i></span>
            <div>
              <h4><?php echo htmlspecialchars($f['title']); ?></h4>
              <p><?php echo htmlspecialchars($f['subtitle']); ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="samsung-brand-right">
        <div class="samsung-brand-right-card">
          <span class="samsung-brand-right-badge"><?php echo htmlspecialchars($brand['right_badge'] ?? 'SELL SAMSUNG'); ?></span>
          <h3 class="samsung-brand-logo"><?php echo htmlspecialchars($brand['right_logo_text'] ?? 'SAMSUNG'); ?></h3>
          <span class="samsung-brand-divider">&#10022;</span>
          <p class="samsung-brand-right-text"><?php echo htmlspecialchars($brand['right_text'] ?? 'Select Samsung to get an instant price for your phone.'); ?></p>
          <a href="<?php echo htmlspecialchars($brand['button_link'] ?? 'samsung.php#samsung'); ?>" class="samsung-brand-btn">
            <?php echo htmlspecialchars($brand['button_text'] ?? 'Select Samsung'); ?> <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>