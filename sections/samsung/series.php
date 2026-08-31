<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
$series = home_single($conn, 'samsung_series');
$cards = home_rows($conn, 'samsung_series_cards');
$series_link = $series['button_link'] ?? 'samsung.php#samsung';
?>
<section id="samsung-series" class="samsung-series-section">
  <div class="container-fluid samsung-series-wrap">
    <div class="row align-items-stretch g-4">

      <div class="col-lg-4">
        <div class="samsung-series-left">
          <span class="samsung-series-tag"><?php echo htmlspecialchars($series['tag_text'] ?? 'SAMSUNG BUYBACK'); ?></span>
          <h2 class="samsung-series-title-main"><?php echo htmlspecialchars($series['title'] ?? 'We Buy All Latest Samsung Phones'); ?></h2>
          <p class="samsung-series-text"><?php echo htmlspecialchars($series['text'] ?? 'From flagship series to budget phones - we buy all Samsung models in any condition at the best price in Dubai.'); ?></p>

          <div class="samsung-series-checklist">
            <div class="samsung-series-check-item"><i class="fa-solid fa-circle-check"></i> All Models Accepted</div>
            <div class="samsung-series-check-item"><i class="fa-solid fa-circle-check"></i> Any Condition &ndash; New or Used</div>
            <div class="samsung-series-check-item"><i class="fa-solid fa-circle-check"></i> Best Market Price</div>
            <div class="samsung-series-check-item"><i class="fa-solid fa-circle-check"></i> Safe, Secure &amp; Hassle Free</div>
          </div>

          <a href="<?php echo htmlspecialchars($series_link); ?>" class="samsung-series-btn">
            <?php echo htmlspecialchars($series['button_text'] ?? 'View All Samsung Models'); ?> <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="samsung-series-grid">
          <?php foreach ($cards as $c): ?>
          <div class="samsung-series-card">
            <span class="samsung-series-icon"><i class="<?php echo htmlspecialchars($c['icon']); ?>"></i></span>
            <h3 class="samsung-series-card-title"><?php echo htmlspecialchars($c['card_title']); ?> <span><?php echo htmlspecialchars($c['card_title_paren']); ?></span></h3>
            <p class="samsung-series-desc"><?php echo htmlspecialchars($c['desc']); ?></p>
            <div class="samsung-series-models">
              <?php foreach (array_filter(array_map('trim', explode(',', $c['models']))) as $m): ?>
              <a href="<?php echo htmlspecialchars($series_link); ?>" class="samsung-model-pill"><?php echo htmlspecialchars($m); ?></a>
              <?php endforeach; ?>
              <a href="<?php echo htmlspecialchars($series_link); ?>" class="samsung-model-pill samsung-model-more">+ More</a>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </div>
</section>