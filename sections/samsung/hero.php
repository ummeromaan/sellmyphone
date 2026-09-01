<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
$hero = home_single($conn, 'samsung_hero');
?>


<section id="samsung-hero" class="samsung-hero-container reveal-on-scroll"
    <?php if (!empty($hero['bg_image'])): ?>
    style="background-image: url('<?php echo htmlspecialchars(home_img($hero['bg_image'])); ?>');"
    <?php endif; ?>>
 <div class="samsung-hero-overlay"></div>
  <div class="container-fluid samsung-hero-fluid-wrap position-relative">
    <div class="row align-items-center">

      <div class="col-lg-7 col-md-8">
        <div class="samsung-hero-cont">
          <span class="s-badge"><?php echo htmlspecialchars($hero['badge_text'] ?? 'SAMSUNG BUYBACK IN DUBAI'); ?></span>
          <h1 class="samsung-hero-content1">
            <?php echo htmlspecialchars($hero['title_pre'] ?? 'Sell Your Samsung'); ?><br>
            <span class="highlight"><?php echo htmlspecialchars($hero['title_highlight'] ?? 'in Dubai'); ?></span>
          </h1>

          <p class="samsung-hero-content2">
            <?php echo $hero['subtitle'] ?? "Looking to sell your Samsung phone in Dubai for the best possible price? We buy all
            Galaxy S, A and Z series models in any condition, whether it's brand new, gently
            used or slightly damaged. Simply tell us your model to get an instant, no-obligation
            price estimate online. Once you accept our offer, we schedule a free doorstep pickup
            anywhere in Dubai and the wider UAE at a time that suits you. You get paid securely
            on the spot, with no hidden fees, no waiting and no hassle."; ?>
          </p>

          <div class="samsung-h-btns d-flex gap-3">
            <a href="<?php echo htmlspecialchars($hero['button_link'] ?? 'samsung.php#samsung'); ?>" class="btn samsung-heroo-btn d-flex align-items-center gap-2">
               <b><?php echo htmlspecialchars($hero['button_text'] ?? 'Get Instant Price Now'); ?></b>
              <span class="samsung-btn-icon"><i class="fa-solid fa-arrow-right"></i></span>
            </a>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-md-4 samsung-phone-visuals reveal-on-scroll">
        <img class="reveal-item" src="<?php echo htmlspecialchars(home_img($hero['hero_image'] ?? '', 'samsung.png')); ?>" alt="Sell Samsung Galaxy phone in Dubai">
      </div>

    </div>
  </div>
</section>