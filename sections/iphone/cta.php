<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
$cta = home_single($conn, 'iphone_cta');
?>
<section id="samsung-cta" class="samsung-cta-section">
  <div class="container-fluid samsung-cta-wrap">
    <div class="row align-items-center g-4">

      <div class="col-lg-6">
        <span class="samsung-cta-tag"><?php echo htmlspecialchars($cta['tag_text'] ?? 'SELL TODAY'); ?></span>
        <h2 class="samsung-cta-heading"><?php echo $cta['heading'] ?? "Your iPhone Won't Get Any Newer.<br>Sell It Before the Price Drops."; ?></h2>
        <p class="samsung-cta-text"><?php echo htmlspecialchars($cta['text'] ?? 'Get the best cash price for your iPhone in Dubai with our fast, safe, and easy service. Free pickup and instant payment at your doorstep.'); ?></p>
      </div>

      <div class="col-lg-6">
        <div class="samsung-cta-card">
          <h3 class="samsung-cta-card-heading"><?php echo htmlspecialchars($cta['card_heading'] ?? 'Get an Instant Quote for Your iPhone'); ?></h3>
          <a href="<?php echo htmlspecialchars($cta['primary_btn_link'] ?? 'apple.php#apple'); ?>" class="samsung-cta-primary-btn">
            <i class="fa-solid fa-bolt"></i> <?php echo htmlspecialchars($cta['primary_btn_text'] ?? 'Get Instant Quote'); ?>
          </a>
          <a href="<?php echo htmlspecialchars($cta['whatsapp_link'] ?? 'https://wa.me/971502166562'); ?>" target="_blank" class="samsung-cta-whatsapp-btn">
            <i class="fa-brands fa-whatsapp"></i> WhatsApp for Quote
          </a>
        </div>
      </div>

    </div>
  </div>
</section>