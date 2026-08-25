<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
/**@var mysqli $conn */

$hiw       = home_single($conn, 'home_hiw');
$hiw_steps = home_rows($conn, 'home_hiw_steps');

$iqc          = home_single($conn, 'home_iqc');
$iqc_features = home_rows($conn, 'home_iqc_features');
?>
<!--how it works / process section-->

<section class="how-it-works-section" id="how-it-works">
    <div class="container">

        <div class="hiw-header text-center">
            <div class="hiw-tag-wrap">
                <span class="hiw-tag-text">
                   <?php echo $hiw['tag_text'] ?? 'PROCESS'; ?>
                </span>
            </div>

            <h2 class="hiw-title">
                <?php echo $hiw['title'] ?? 'How It Works'; ?>
                <span class="hiw-underline"></span>
            </h2>
        </div>

        <div class="hiw-steps">

            <?php $total_steps = count($hiw_steps); foreach ($hiw_steps as $i => $step): ?>
            <div class="hiw-step">
                <div class="hiw-step-top">
                    <span class="hiw-number"><?php echo htmlspecialchars($step['step_number']); ?></span>
                    <span class="hiw-connect-line"></span>
                    <span class="hiw-icon-circle">
                        <i class="<?php echo htmlspecialchars($step['icon_class']); ?>"></i>
                    </span>
                </div>
                <h4 class="hiw-step-title"><?php echo $step['title']; ?></h4>
                <p class="hiw-step-desc"><?php echo $step['description']; ?></p>
            </div>

            <?php if ($i < $total_steps - 1): ?>
            <span class="hiw-connector"><i class="fa-solid fa-chevron-right"></i></span>
            <?php endif; ?>
            <?php endforeach; ?>

        </div>

    </div>
</section>



<!--get instant qoute-->

<!--instant quote CTA section - sits right after how-it-works-->

<section class="iqc-section" id="instant-quote"<?php if (!empty($iqc['bg_image'])): ?> style="background-image:url('<?php echo htmlspecialchars(home_img($iqc['bg_image'])); ?>')"<?php endif; ?>>
    <div class="container">

        <div class="iqc-wrap">

            <!-- Left: phone image + heading + features -->
            <div class="iqc-left">

                
                <!--<div class="iqc-phone-visual">
                    <img src="assets/images/back.png" alt="Phones" class="iqc-phone-img">
                </div>-->

                <div class="iqc-text">
                    <h2 class="iqc-title">
                        <?php echo $iqc['title_pre'] ?? 'Sell Your Phone in'; ?><br>
                        <span class="highlight"><?php echo $iqc['title_highlight'] ?? 'Minutes'; ?></span>
                    </h2>

                    <p class="iqc-desc">
                        <?php echo $iqc['description'] ?? ''; ?>
                    </p>

                    <ul class="iqc-features">
                        <?php foreach ($iqc_features as $f): ?>
                        <li><i class="<?php echo htmlspecialchars($f['icon_class']); ?> fs-5" style="color:<?php echo htmlspecialchars($f['icon_color'] ?: '#D4AF37'); ?>"></i> <?php echo $f['text']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>

            <!-- Middle: quote form card - kept static, this is the functional calculator form -->
            <div class="iqc-form-card">

                <select class="iqc-select" id="iqcBrand">
                    <option value="" selected disabled>Select Brand</option>
                    <option value="apple">Apple</option>
                    <option value="samsung">Samsung</option>
                </select>

                <select class="iqc-select" id="iqcStorage">
                    <option value="" selected disabled>Storage Capacity</option>
                    <option value="128gb">128GB</option>
                    <option value="256gb">256GB</option>
                    <option value="512gb">512GB</option>
                </select>

                <select class="iqc-select" id="iqcCondition">
                    <option value="" selected disabled>Condition</option>
                    <option value="flawless">Flawless</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                </select>

                <button type="button" class="iqc-continue-btn" id="iqcContinueBtn">
                    Continue
                </button>

            </div>

            <!-- Right: estimated value card -->
            <div class="iqc-estimate-card">
                
                <span class="iqc-estimate-label"><?php echo $iqc['estimate_label'] ?? 'Get your phone value'; ?></span>
                
                <span class="iqc-estimate-value"><?php echo $iqc['estimate_value'] ?? 'Instant and accurate qoute'; ?></span>
                <span class="iqc-estimate-badge">
                    <i class="fa-solid fa-star"></i>
                    <?php echo $iqc['estimate_badge_text'] ?? 'Best Price in Dubai!'; ?>
                </span>
            </div>

        </div>

    </div>
</section>

<script>
// "Continue" click -> smooth scroll down to the existing brand cards section
document.getElementById('iqcContinueBtn').addEventListener('click', function () {
    var brandsSection = document.getElementById('brands');
    if (brandsSection) {
        brandsSection.scrollIntoView({ behavior: 'smooth' });
    }
});
</script>
