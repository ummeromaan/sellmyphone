<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
$policy = home_single($conn, 'iphone_policy');
$cards = home_rows($conn, 'iphone_policy_cards');
?>
<section class="sm-policy-section">
  <div class="container-fluid samsung-policy-wrap">

    <div class="sm-policy-tag-wrap"><span><?php echo htmlspecialchars($policy['tag_text'] ?? 'CLEAR ACCEPTANCE POLICY'); ?></span></div>

    <h2 class="sm-policy-title"><?php echo htmlspecialchars($policy['title'] ?? 'iPhone Models & Conditions We Buy'); ?> <span class="highlight">in Dubai</span></h2>

    <p class="sm-policy-subtitle"><?php echo htmlspecialchars($policy['subtitle'] ?? "Transparent pricing starts with clear standards. Here's our exact criteria for purchasing iPhones in Dubai, so you know exactly what to expect before you request a quote — no guesswork, no last-minute price drops."); ?></p>

    <div class="row g-4 sm-policy-cards">
      <?php foreach ($cards as $c): ?>
      <div class="col-lg-4">
        <div class="sm-policy-card">
          <div class="sm-policy-icon <?php echo $c['list_type']=='cross' ? 'sm-policy-icon-excluded' : ''; ?>"><i class="<?php echo htmlspecialchars($c['icon']); ?>"></i></div>
          <h3 class="sm-policy-card-title"><?php echo htmlspecialchars($c['title']); ?></h3>
          <ul class="sm-policy-list <?php echo $c['list_type']=='cross' ? 'sm-policy-list-cross' : 'sm-policy-list-check'; ?>">
            <?php foreach (array_filter(array_map('trim', explode("\n", $c['items']))) as $item): ?>
            <li><?php echo htmlspecialchars($item); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="sm-policy-banner">
      <div class="sm-policy-banner-item">
        <i class="fa-solid fa-gem"></i>
        <div>
          <p class="sm-policy-banner-text"><?php echo htmlspecialchars($policy['banner_text'] ?? 'We offer the best value for genuine iPhones in the best possible condition, backed by a fair, upfront evaluation every single time.'); ?></p>
          <p class="sm-policy-banner-highlight"><?php echo htmlspecialchars($policy['banner_highlight'] ?? 'No hidden terms. No surprises.'); ?></p>
        </div>
      </div>
      <div class="sm-policy-banner-divider"></div>
      <div class="sm-policy-banner-item sm-policy-banner-trust">
        <i class="fa-solid fa-award"></i>
        <div>
          <p class="sm-policy-banner-title"><?php echo htmlspecialchars($policy['banner_title'] ?? 'TRUSTED IN DUBAI'); ?></p>
          <p class="sm-policy-banner-sub"><?php echo htmlspecialchars($policy['banner_sub'] ?? 'Fair • Fast • Transparent'); ?></p>
        </div>
      </div>
    </div>

  </div>
</section>